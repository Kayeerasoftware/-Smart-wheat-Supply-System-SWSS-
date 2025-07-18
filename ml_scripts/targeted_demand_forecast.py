import pandas as pd
import numpy as np
from statsmodels.tsa.arima.model import ARIMA
import json
import os
from datetime import datetime, timedelta

def analyze_downstream_demand(role, user_id):
    """Analyze downstream demand based on user role"""
    export_dir = os.path.abspath(os.path.join(os.path.dirname(__file__), '../storage/app/ml_exports'))
    
    if role == 'farmer':
        # Farmers: Analyze supplier demand for wheat
        return analyze_supplier_demand_for_wheat(export_dir)
    elif role == 'supplier':
        # Suppliers: Analyze manufacturer demand for processed wheat
        return analyze_manufacturer_demand_for_supplier(export_dir, user_id)
    elif role == 'manufacturer':
        # Manufacturers: Analyze retailer/distributor demand for finished products
        return analyze_retailer_demand_for_manufacturer(export_dir, user_id)
    else:
        return None

def analyze_supplier_demand_for_wheat(export_dir):
    """Analyze how much wheat suppliers are buying from farmers"""
    supplier_orders = []
    
    # Get all supplier order data
    for filename in os.listdir(export_dir):
        if filename.startswith('supplier_') and filename.endswith('.csv'):
            csv_path = os.path.join(export_dir, filename)
            df = pd.read_csv(csv_path)
            if not df.empty:
                supplier_orders.append(df)
    
    if not supplier_orders:
        return None
    
    # Combine all supplier orders
    combined_df = pd.concat(supplier_orders, ignore_index=True)
    combined_df['order_date'] = pd.to_datetime(combined_df['order_date'])
    
    # Aggregate by month
    combined_df['month'] = combined_df['order_date'].dt.to_period('M').dt.to_timestamp()
    monthly_demand = combined_df.groupby('month')['quantity'].sum().sort_index()
    
    return generate_forecast(monthly_demand, "Wheat Demand from Suppliers")

def analyze_manufacturer_demand_for_supplier(export_dir, supplier_id):
    """Analyze how much processed wheat manufacturers are buying from suppliers"""
    manufacturer_orders = []
    
    # Get all manufacturer order data
    for filename in os.listdir(export_dir):
        if filename.startswith('manufacturer_') and filename.endswith('.csv'):
            csv_path = os.path.join(export_dir, filename)
            df = pd.read_csv(csv_path)
            if not df.empty:
                manufacturer_orders.append(df)
    
    if not manufacturer_orders:
        return None
    
    # Combine all manufacturer orders
    combined_df = pd.concat(manufacturer_orders, ignore_index=True)
    combined_df['order_date'] = pd.to_datetime(combined_df['order_date'])
    
    # Aggregate by month
    combined_df['month'] = combined_df['order_date'].dt.to_period('M').dt.to_timestamp()
    monthly_demand = combined_df.groupby('month')['quantity'].sum().sort_index()
    
    return generate_forecast(monthly_demand, "Processed Wheat Demand from Manufacturers")

def analyze_retailer_demand_for_manufacturer(export_dir, manufacturer_id):
    """Analyze how much finished products retailers/distributors are buying from manufacturers"""
    retailer_orders = []
    distributor_orders = []
    
    # Get retailer orders
    for filename in os.listdir(export_dir):
        if filename.startswith('retailer_') and filename.endswith('.csv'):
            csv_path = os.path.join(export_dir, filename)
            df = pd.read_csv(csv_path)
            if not df.empty:
                retailer_orders.append(df)
    
    # Get distributor orders
    for filename in os.listdir(export_dir):
        if filename.startswith('distributor_') and filename.endswith('.csv'):
            csv_path = os.path.join(export_dir, filename)
            df = pd.read_csv(csv_path)
            if not df.empty:
                distributor_orders.append(df)
    
    # Combine retailer and distributor orders
    all_orders = retailer_orders + distributor_orders
    
    if not all_orders:
        return None
    
    # Combine all orders
    combined_df = pd.concat(all_orders, ignore_index=True)
    combined_df['order_date'] = pd.to_datetime(combined_df['order_date'])
    
    # Aggregate by month
    combined_df['month'] = combined_df['order_date'].dt.to_period('M').dt.to_timestamp()
    monthly_demand = combined_df.groupby('month')['quantity'].sum().sort_index()
    
    return generate_forecast(monthly_demand, "Finished Product Demand from Retailers/Distributors")

def generate_forecast(monthly_data, title):
    """Generate ARIMA forecast for the given monthly data"""
    if len(monthly_data) < 3:
        # Not enough data for forecasting
        return None
    
    try:
        # Fit ARIMA model
        model = ARIMA(monthly_data, order=(1, 1, 1))
        model_fit = model.fit()
        
        # Forecast next 6 months
        forecast = model_fit.forecast(steps=6)
        
        # Calculate confidence intervals
        forecast_ci = model_fit.get_forecast(steps=6).conf_int()
        
        # Prepare forecast data
        forecast_data = {
            "title": title,
            "dates": [str(date)[:7] for date in forecast.index],  # YYYY-MM
            "values": [float(val) for val in forecast.values],
            "lower_ci": [float(val) for val in forecast_ci.iloc[:, 0]],
            "upper_ci": [float(val) for val in forecast_ci.iloc[:, 1]],
            "historical_data": {
                "dates": [str(date)[:7] for date in monthly_data.index],
                "values": [float(val) for val in monthly_data.values]
            },
            "summary": {
                "total_historical_demand": float(monthly_data.sum()),
                "avg_monthly_demand": float(monthly_data.mean()),
                "forecast_total": float(forecast.sum()),
                "forecast_avg": float(forecast.mean()),
                "growth_rate": float((forecast.mean() - monthly_data.mean()) / monthly_data.mean() * 100) if monthly_data.mean() > 0 else 0
            }
        }
        
        return forecast_data
        
    except Exception as e:
        print(f"Forecast generation failed: {e}")
        return None

def generate_recommendations(forecast_data, role):
    """Generate recommendations based on forecast data"""
    if not forecast_data:
        return []
    
    recommendations = []
    summary = forecast_data['summary']
    
    if role == 'farmer':
        if summary['growth_rate'] > 10:
            recommendations.append(f"Increase wheat production by {summary['growth_rate']:.1f}% to meet growing supplier demand")
        elif summary['growth_rate'] < -10:
            recommendations.append(f"Consider reducing wheat production by {abs(summary['growth_rate']):.1f}% due to declining demand")
        else:
            recommendations.append("Maintain current wheat production levels")
        
        recommendations.append(f"Plan for {summary['forecast_avg']:.0f} tons average monthly demand")
        recommendations.append(f"Total expected demand: {summary['forecast_total']:.0f} tons over next 6 months")
    
    elif role == 'supplier':
        if summary['growth_rate'] > 10:
            recommendations.append(f"Increase wheat processing capacity by {summary['growth_rate']:.1f}%")
        elif summary['growth_rate'] < -10:
            recommendations.append(f"Reduce wheat processing by {abs(summary['growth_rate']):.1f}%")
        else:
            recommendations.append("Maintain current processing levels")
        
        recommendations.append(f"Plan for {summary['forecast_avg']:.0f} tons average monthly processing")
        recommendations.append(f"Total expected demand: {summary['forecast_total']:.0f} tons over next 6 months")
    
    elif role == 'manufacturer':
        if summary['growth_rate'] > 10:
            recommendations.append(f"Increase production capacity by {summary['growth_rate']:.1f}%")
        elif summary['growth_rate'] < -10:
            recommendations.append(f"Reduce production by {abs(summary['growth_rate']):.1f}%")
        else:
            recommendations.append("Maintain current production levels")
        
        recommendations.append(f"Plan for {summary['forecast_avg']:.0f} units average monthly production")
        recommendations.append(f"Total expected demand: {summary['forecast_total']:.0f} units over next 6 months")
    
    return recommendations

def main():
    """Main function to generate targeted demand forecasts"""
    export_dir = os.path.abspath(os.path.join(os.path.dirname(__file__), '../storage/app/ml_exports'))
    forecast_dir = os.path.abspath(os.path.join(os.path.dirname(__file__), '../storage/app/public/forecasts'))
    os.makedirs(forecast_dir, exist_ok=True)
    
    # Process each role's demand forecasting
    roles_to_forecast = ['farmer', 'supplier', 'manufacturer']
    
    for role in roles_to_forecast:
        print(f"Generating demand forecast for {role}s...")
        
        if role == 'farmer':
            # Generate one forecast for all farmers
            forecast_data = analyze_downstream_demand(role, None)
            if forecast_data:
                recommendations = generate_recommendations(forecast_data, role)
                forecast_data['recommendations'] = recommendations
                
                # Save for all farmers
                output_path = os.path.join(forecast_dir, f'farmer_demand_forecast.json')
                with open(output_path, 'w') as f:
                    json.dump(forecast_data, f, indent=2, default=str)
                print(f"Saved farmer demand forecast")
        
        else:
            # Generate individual forecasts for suppliers and manufacturers
            for filename in os.listdir(export_dir):
                if filename.startswith(f'{role}_') and filename.endswith('.csv'):
                    parts = filename.split('_')
                    if len(parts) >= 3:
                        user_id = parts[1]
                        
                        forecast_data = analyze_downstream_demand(role, user_id)
                        if forecast_data:
                            recommendations = generate_recommendations(forecast_data, role)
                            forecast_data['recommendations'] = recommendations
                            
                            # Save individual forecast
                            output_path = os.path.join(forecast_dir, f'{role}_{user_id}_demand_forecast.json')
                            with open(output_path, 'w') as f:
                                json.dump(forecast_data, f, indent=2, default=str)
                            print(f"Saved demand forecast for {role} {user_id}")
    
    print("Targeted demand forecasting completed!")

if __name__ == "__main__":
    main() 