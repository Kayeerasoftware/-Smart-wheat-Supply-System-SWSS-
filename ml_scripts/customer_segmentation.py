import pandas as pd
import numpy as np
from sklearn.cluster import KMeans
from sklearn.preprocessing import StandardScaler
import json
import os
from datetime import datetime, timedelta

def calculate_purchase_patterns(df):
    """Calculate detailed purchase patterns for each customer"""
    patterns = {}
    
    for customer_id in df['customer_id'].unique():
        customer_data = df[df['customer_id'] == customer_id]
        
        # Basic RFM metrics
        max_date = df['order_date'].max()
        last_purchase = customer_data['order_date'].max()
        recency = (max_date - last_purchase).days
        
        frequency = len(customer_data)
        monetary = customer_data['total_amount'].sum()
        
        # Purchase pattern analysis
        avg_order_value = customer_data['total_amount'].mean()
        max_order_value = customer_data['total_amount'].max()
        min_order_value = customer_data['total_amount'].min()
        
        # Purchase timing patterns
        purchase_dates = customer_data['order_date'].sort_values()
        if len(purchase_dates) > 1:
            time_between_purchases = purchase_dates.diff().dropna().dt.days
            avg_days_between_purchases = time_between_purchases.mean()
            purchase_consistency = time_between_purchases.std()  # Lower = more consistent
        else:
            avg_days_between_purchases = 0
            purchase_consistency = 0
        
        # Product preference analysis
        if 'product_name' in customer_data.columns:
            product_counts = customer_data['product_name'].value_counts()
            favorite_product = product_counts.index[0] if len(product_counts) > 0 else 'Unknown'
            product_diversity = len(product_counts)  # Number of different products purchased
        else:
            favorite_product = 'Unknown'
            product_diversity = 1
        
        # Seasonal patterns (if enough data)
        if len(customer_data) >= 4:
            monthly_purchases = customer_data.groupby(customer_data['order_date'].dt.month).size()
            peak_purchase_month = monthly_purchases.idxmax() if len(monthly_purchases) > 0 else 1
        else:
            peak_purchase_month = 1
        
        # Spending pattern analysis
        spending_trend = 0
        if len(customer_data) >= 3:
            # Calculate if spending is increasing, decreasing, or stable
            recent_orders = customer_data.nlargest(3, 'order_date')['total_amount']
            if len(recent_orders) >= 2:
                spending_trend = (recent_orders.iloc[-1] - recent_orders.iloc[0]) / recent_orders.iloc[0]
        
        patterns[customer_id] = {
            'recency': recency,
            'frequency': frequency,
            'monetary': monetary,
            'avg_order_value': avg_order_value,
            'max_order_value': max_order_value,
            'min_order_value': min_order_value,
            'avg_days_between_purchases': avg_days_between_purchases,
            'purchase_consistency': purchase_consistency,
            'favorite_product': favorite_product,
            'product_diversity': product_diversity,
            'peak_purchase_month': peak_purchase_month,
            'spending_trend': spending_trend,
            'total_orders': frequency,
            'total_spent': monetary
        }
    
    return pd.DataFrame.from_dict(patterns, orient='index')

def segment_customers_by_purchase_patterns(patterns_df, n_clusters=5):
    """Segment customers based on comprehensive purchase patterns"""
    # Select features for clustering
    features = [
        'recency', 'frequency', 'monetary', 'avg_order_value',
        'avg_days_between_purchases', 'purchase_consistency',
        'product_diversity', 'spending_trend'
    ]
    
    # Remove rows with missing values
    patterns_clean = patterns_df[features].dropna()
    
    # Scale the features
    scaler = StandardScaler()
    patterns_scaled = scaler.fit_transform(patterns_clean)
    
    # Apply K-means clustering
    kmeans = KMeans(n_clusters=n_clusters, random_state=42)
    patterns_clean['cluster'] = kmeans.fit_predict(patterns_scaled)
    
    # Assign segment names based on purchase behavior characteristics
    segment_names = []
    for idx, row in patterns_clean.iterrows():
        recency = row['recency']
        frequency = row['frequency']
        monetary = row['monetary']
        avg_order_value = row['avg_order_value']
        spending_trend = row['spending_trend']
        product_diversity = row['product_diversity']
        
        # Champions: High value, frequent, recent, increasing spending
        if (recency <= 30 and frequency >= 5 and monetary >= 1000 and 
            spending_trend > 0.1 and product_diversity >= 3):
            segment_names.append('Champions')
        
        # Big Spenders: High monetary value, regardless of frequency
        elif monetary >= 1500 and avg_order_value >= 200:
            segment_names.append('Big Spenders')
        
        # Frequent Customers: High frequency, consistent purchases
        elif frequency >= 8 and row['purchase_consistency'] <= 30:
            segment_names.append('Frequent Customers')
        
        # Loyal Customers: Moderate frequency, consistent, stable spending
        elif (frequency >= 3 and frequency < 8 and 
              row['purchase_consistency'] <= 45 and abs(spending_trend) < 0.2):
            segment_names.append('Loyal Customers')
        
        # Recent Customers: Recent purchases but low frequency
        elif recency <= 60 and frequency <= 2:
            segment_names.append('Recent Customers')
        
        # At Risk: High recency, low frequency, declining spending
        elif recency > 90 and frequency <= 2 and spending_trend < -0.1:
            segment_names.append('At Risk')
        
        # Occasional Buyers: Everything else
        else:
            segment_names.append('Occasional Buyers')
    
    patterns_clean['segment_name'] = segment_names
    
    # Add back the original data
    result_df = patterns_df.copy()
    result_df['segment_name'] = patterns_clean['segment_name']
    result_df['cluster'] = patterns_clean['cluster']
    
    return result_df

def generate_purchase_based_recommendations(segmented_df):
    """Generate recommendations based on actual purchase patterns"""
    recommendations = {}
    
    for segment in segmented_df['segment_name'].unique():
        segment_data = segmented_df[segmented_df['segment_name'] == segment]
        
        if segment == 'Champions':
            recommendations[segment] = [
                f"Offer exclusive access to new products (they buy {segment_data['product_diversity'].mean():.1f} different products)",
                f"Implement VIP program with {segment_data['avg_order_value'].mean():.0f}% higher rewards",
                f"Provide early access to premium products (avg order: ${segment_data['avg_order_value'].mean():.0f})",
                f"Request testimonials (they've made {segment_data['frequency'].mean():.1f} purchases)"
            ]
        elif segment == 'Big Spenders':
            recommendations[segment] = [
                f"Focus on premium products (avg order: ${segment_data['avg_order_value'].mean():.0f})",
                f"Offer bulk purchase discounts (max order: ${segment_data['max_order_value'].mean():.0f})",
                f"Provide personalized account management",
                f"Create exclusive high-value product bundles"
            ]
        elif segment == 'Frequent Customers':
            recommendations[segment] = [
                f"Offer subscription services (they buy every {segment_data['avg_days_between_purchases'].mean():.0f} days)",
                f"Implement auto-reorder for favorite products",
                f"Provide volume discounts for consistent purchases",
                f"Create loyalty tiers based on purchase frequency"
            ]
        elif segment == 'Loyal Customers':
            recommendations[segment] = [
                f"Maintain consistent quality (they're very consistent buyers)",
                f"Offer moderate volume discounts",
                f"Provide personalized recommendations based on purchase history",
                f"Implement retention programs for stable customers"
            ]
        elif segment == 'Recent Customers':
            recommendations[segment] = [
                f"Send welcome series with product education",
                f"Offer first-time buyer incentives",
                f"Provide product recommendations based on initial purchase",
                f"Create onboarding program for new customers"
            ]
        elif segment == 'At Risk':
            recommendations[segment] = [
                f"Send re-engagement campaigns (last purchase: {segment_data['recency'].mean():.0f} days ago)",
                f"Offer win-back incentives with special discounts",
                f"Conduct customer feedback surveys",
                f"Provide personalized re-engagement offers"
            ]
        else:  # Occasional Buyers
            recommendations[segment] = [
                f"Send periodic promotional campaigns",
                f"Offer seasonal discounts and promotions",
                f"Provide product education content",
                f"Create awareness campaigns for new products"
            ]
    
    return recommendations

def main():
    # Load order data
    export_dir = os.path.abspath(os.path.join(os.path.dirname(__file__), '../storage/app/ml_exports'))
    all_orders = []
    
    # Combine all user order data
    for filename in os.listdir(export_dir):
        if filename.endswith('.csv'):
            csv_path = os.path.join(export_dir, filename)
            df = pd.read_csv(csv_path)
            if not df.empty:
                # Parse user info from filename
                parts = filename.split('_')
                if len(parts) >= 3:
                    role = parts[0]
                    user_id = parts[1]
                    df['customer_id'] = f"{role}_{user_id}"
                    df['customer_role'] = role
                    all_orders.append(df)
    
    if not all_orders:
        print("No order data found!")
        return
    
    # Combine all data
    combined_df = pd.concat(all_orders, ignore_index=True)
    combined_df['order_date'] = pd.to_datetime(combined_df['order_date'])
    
    # Calculate detailed purchase patterns
    print("Analyzing purchase patterns...")
    patterns_df = calculate_purchase_patterns(combined_df)
    
    # Segment customers based on purchase behavior
    print("Segmenting customers based on purchase patterns...")
    segmented_df = segment_customers_by_purchase_patterns(patterns_df)
    
    # Generate purchase-based recommendations
    print("Generating recommendations based on purchase behavior...")
    recommendations = generate_purchase_based_recommendations(segmented_df)
    
    # Save results
    output_dir = os.path.abspath(os.path.join(os.path.dirname(__file__), '../storage/app/public/segments'))
    os.makedirs(output_dir, exist_ok=True)
    
    # Prepare data for JSON serialization
    customers_data = {}
    for customer_id, row in segmented_df.iterrows():
        customers_data[customer_id] = {
            'segment_name': row['segment_name'],
            'recency': int(row['recency']),
            'frequency': int(row['frequency']),
            'monetary': float(row['monetary']),
            'avg_order_value': float(row['avg_order_value']),
            'max_order_value': float(row['max_order_value']),
            'min_order_value': float(row['min_order_value']),
            'avg_days_between_purchases': float(row['avg_days_between_purchases']),
            'purchase_consistency': float(row['purchase_consistency']),
            'favorite_product': row['favorite_product'],
            'product_diversity': int(row['product_diversity']),
            'peak_purchase_month': int(row['peak_purchase_month']),
            'spending_trend': float(row['spending_trend']),
            'total_orders': int(row['total_orders']),
            'total_spent': float(row['total_spent']),
            'rfm_score': int(row['recency'] + row['frequency'] + row['monetary'] / 100)
        }
    
    # Save customer segments with purchase patterns
    segments_data = {
        'customers': customers_data,
        'recommendations': recommendations,
        'summary': {
            'total_customers': len(segmented_df),
            'segment_distribution': segmented_df['segment_name'].value_counts().to_dict(),
            'purchase_insights': {
                'avg_order_value': float(segmented_df['avg_order_value'].mean()),
                'avg_frequency': float(segmented_df['frequency'].mean()),
                'avg_recency': float(segmented_df['recency'].mean()),
                'total_revenue': float(segmented_df['monetary'].sum())
            }
        }
    }
    
    with open(os.path.join(output_dir, 'customer_segments.json'), 'w') as f:
        json.dump(segments_data, f, indent=2, default=str)
    
    print("Customer segmentation based on purchase patterns completed!")
    print(f"Total customers analyzed: {len(segmented_df)}")
    print(f"Total revenue analyzed: ${segmented_df['monetary'].sum():,.2f}")
    print("\nSegment distribution based on purchase behavior:")
    for segment, count in segmented_df['segment_name'].value_counts().items():
        avg_spend = segmented_df[segmented_df['segment_name'] == segment]['monetary'].mean()
        print(f"  {segment}: {count} customers (avg spend: ${avg_spend:.0f})")

if __name__ == "__main__":
    main() 