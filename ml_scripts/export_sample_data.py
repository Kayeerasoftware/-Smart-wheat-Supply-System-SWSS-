import pandas as pd
import numpy as np
import os
from datetime import datetime, timedelta
import random

def generate_sample_orders():
    """Generate sample order data for ML models"""
    export_dir = os.path.abspath(os.path.join(os.path.dirname(__file__), '../storage/app/ml_exports'))
    
    # Create export directory if it doesn't exist
    os.makedirs(export_dir, exist_ok=True)
    
    # Generate sample manufacturer orders (what suppliers analyze)
    generate_manufacturer_orders(export_dir)
    
    # Generate sample retailer orders (what manufacturers analyze)
    generate_retailer_orders(export_dir)
    
    # Generate sample distributor orders (what manufacturers analyze)
    generate_distributor_orders(export_dir)
    
    print("Sample data exported successfully!")

def generate_manufacturer_orders(export_dir):
    """Generate sample manufacturer order data"""
    # Generate 12 months of data
    start_date = datetime.now() - timedelta(days=365)
    dates = []
    quantities = []
    amounts = []
    
    # Base demand with seasonal variation
    base_demand = 1000  # tons per month
    seasonal_factor = 1.2  # 20% seasonal variation
    
    for i in range(12):
        date = start_date + timedelta(days=i*30)
        dates.append(date.strftime('%Y-%m-%d'))
        
        # Add seasonal variation (higher demand in winter months)
        month = date.month
        if month in [11, 12, 1, 2]:  # Winter months
            seasonal_multiplier = seasonal_factor
        elif month in [6, 7, 8]:  # Summer months
            seasonal_multiplier = 0.8
        else:
            seasonal_multiplier = 1.0
        
        # Add some random variation
        random_factor = random.uniform(0.8, 1.2)
        quantity = int(base_demand * seasonal_multiplier * random_factor)
        quantities.append(quantity)
        
        # Calculate amount (assuming $500 per ton)
        amount = quantity * 500
        amounts.append(amount)
    
    # Create DataFrame
    df = pd.DataFrame({
        'order_date': dates,
        'quantity': quantities,
        'total_amount': amounts,
        'manufacturer_id': [1] * len(dates),
        'supplier_id': [1] * len(dates)
    })
    
    # Save to CSV
    output_path = os.path.join(export_dir, 'manufacturer_orders.csv')
    df.to_csv(output_path, index=False)
    print(f"Manufacturer orders exported to: {output_path}")

def generate_retailer_orders(export_dir):
    """Generate sample retailer order data"""
    # Generate 12 months of data
    start_date = datetime.now() - timedelta(days=365)
    dates = []
    quantities = []
    amounts = []
    
    # Base demand for finished products
    base_demand = 500  # units per month
    
    for i in range(12):
        date = start_date + timedelta(days=i*30)
        dates.append(date.strftime('%Y-%m-%d'))
        
        # Add some random variation
        random_factor = random.uniform(0.7, 1.3)
        quantity = int(base_demand * random_factor)
        quantities.append(quantity)
        
        # Calculate amount (assuming $50 per unit)
        amount = quantity * 50
        amounts.append(amount)
    
    # Create DataFrame
    df = pd.DataFrame({
        'order_date': dates,
        'quantity': quantities,
        'total_amount': amounts,
        'retailer_id': [1] * len(dates),
        'manufacturer_id': [1] * len(dates)
    })
    
    # Save to CSV
    output_path = os.path.join(export_dir, 'retailer_orders.csv')
    df.to_csv(output_path, index=False)
    print(f"Retailer orders exported to: {output_path}")

def generate_distributor_orders(export_dir):
    """Generate sample distributor order data"""
    # Generate 12 months of data
    start_date = datetime.now() - timedelta(days=365)
    dates = []
    quantities = []
    amounts = []
    
    # Base demand for finished products
    base_demand = 800  # units per month
    
    for i in range(12):
        date = start_date + timedelta(days=i*30)
        dates.append(date.strftime('%Y-%m-%d'))
        
        # Add some random variation
        random_factor = random.uniform(0.8, 1.2)
        quantity = int(base_demand * random_factor)
        quantities.append(quantity)
        
        # Calculate amount (assuming $40 per unit for distributors)
        amount = quantity * 40
        amounts.append(amount)
    
    # Create DataFrame
    df = pd.DataFrame({
        'order_date': dates,
        'quantity': quantities,
        'total_amount': amounts,
        'distributor_id': [1] * len(dates),
        'manufacturer_id': [1] * len(dates)
    })
    
    # Save to CSV
    output_path = os.path.join(export_dir, 'distributor_orders.csv')
    df.to_csv(output_path, index=False)
    print(f"Distributor orders exported to: {output_path}")

if __name__ == "__main__":
    generate_sample_orders() 