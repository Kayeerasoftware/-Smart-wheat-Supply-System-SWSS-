import pandas as pd
import json
import os
from datetime import datetime, timedelta

def calculate_frequent_customer_discounts(customer_data, order_items):
    """Calculate discounts for Frequent Customers"""
    discounts = []
    
    # Bulk purchase incentive (buy 5+ items, get 20% off)
    total_items = sum(order_items.values())
    if total_items >= 5:
        discounts.append({
            'type': 'bulk_purchase',
            'description': f'Bulk Purchase Discount (20% off) - {total_items} items',
            'discount_percentage': 20,
            'minimum_items': 5,
            'items_purchased': total_items,
            'savings_message': f'Save 20% on your order of {total_items} items!'
        })
    
    # Bundle deals (buy 3+ products, save 15%)
    unique_products = len(order_items.keys())
    if unique_products >= 3:
        discounts.append({
            'type': 'bundle_deal',
            'description': f'Bundle Deal (15% off) - {unique_products} different products',
            'discount_percentage': 15,
            'minimum_products': 3,
            'products_purchased': unique_products,
            'savings_message': f'Bundle discount: 15% off for buying {unique_products} different products!'
        })
    
    # Auto-reorder discount (5% off subscription orders)
    if customer_data.get('is_subscription_order', False):
        discounts.append({
            'type': 'auto_reorder',
            'description': 'Auto-Reorder Discount (5% off subscription)',
            'discount_percentage': 5,
            'savings_message': '5% off for your subscription order!'
        })
    
    return discounts

def calculate_big_spender_discounts(customer_data, order_total, order_items):
    """Calculate discounts for Big Spenders"""
    discounts = []
    
    # Premium bulk pricing for large orders
    if order_total >= 1000:  # $1000+ orders
        discount_percentage = min(25, (order_total // 500) * 5)  # 5% per $500, max 25%
        discounts.append({
            'type': 'premium_bulk',
            'description': f'Premium Bulk Pricing ({discount_percentage}% off) - Order value: ${order_total:,.2f}',
            'discount_percentage': discount_percentage,
            'minimum_order': 1000,
            'order_total': order_total,
            'savings_message': f'Premium bulk discount: {discount_percentage}% off your ${order_total:,.2f} order!'
        })
    
    # Personalized bulk deals (based on customer's purchase history)
    if customer_data.get('average_order_value', 0) > 500:
        personalized_discount = min(20, customer_data['average_order_value'] // 100)
        discounts.append({
            'type': 'personalized_bulk',
            'description': f'Personalized Bulk Deal ({personalized_discount}% off) - Based on your purchase history',
            'discount_percentage': personalized_discount,
            'savings_message': f'Personalized discount: {personalized_discount}% off based on your purchase history!'
        })
    
    # Exclusive event discounts (if applicable)
    current_month = datetime.now().month
    if current_month in [11, 12]:  # Holiday season
        discounts.append({
            'type': 'exclusive_event',
            'description': 'Exclusive Holiday Event Discount (10% off)',
            'discount_percentage': 10,
            'savings_message': 'Exclusive holiday discount: 10% off for our valued customers!'
        })
    
    return discounts

def calculate_discounts_for_customer(customer_id, customer_segment, order_data):
    """Calculate all applicable discounts for a customer"""
    discounts = []
    
    # Extract order information
    order_total = order_data.get('total_amount', 0)
    order_items = order_data.get('items', {})  # {product_id: quantity}
    
    # Get customer data (you can extend this with more customer information)
    customer_data = {
        'average_order_value': order_data.get('average_order_value', 0),
        'is_subscription_order': order_data.get('is_subscription', False),
        'purchase_frequency': order_data.get('purchase_frequency', 0)
    }
    
    # Calculate segment-specific discounts
    if customer_segment == 'Frequent Customers':
        discounts.extend(calculate_frequent_customer_discounts(customer_data, order_items))
    elif customer_segment == 'Big Spenders':
        discounts.extend(calculate_big_spender_discounts(customer_data, order_total, order_items))
    
    # Calculate total discount
    total_discount_percentage = sum(d['discount_percentage'] for d in discounts)
    total_savings = (order_total * total_discount_percentage) / 100
    
    return {
        'customer_id': customer_id,
        'segment': customer_segment,
        'order_total': order_total,
        'discounts': discounts,
        'total_discount_percentage': total_discount_percentage,
        'total_savings': total_savings,
        'final_total': order_total - total_savings
    }

def generate_discount_recommendations(segments_data):
    """Generate discount recommendations for all customers"""
    recommendations = {}
    
    for customer_id, customer_data in segments_data.get('customers', {}).items():
        segment = customer_data.get('segment_name', '')
        
        # Sample order data (in real implementation, this would come from the current order)
        sample_order = {
            'total_amount': customer_data.get('monetary', 0),
            'items': {'product_1': 3, 'product_2': 2, 'product_3': 1},  # Sample items
            'average_order_value': customer_data.get('monetary', 0),
            'is_subscription': False,
            'purchase_frequency': customer_data.get('frequency', 0)
        }
        
        discount_result = calculate_discounts_for_customer(
            customer_id, 
            segment, 
            sample_order
        )
        
        recommendations[customer_id] = discount_result
    
    return recommendations

def main():
    # Load customer segments
    segments_path = os.path.abspath(os.path.join(os.path.dirname(__file__), '../storage/app/public/segments/customer_segments.json'))
    
    if not os.path.exists(segments_path):
        print("Customer segments not found. Run customer segmentation first.")
        return
    
    with open(segments_path, 'r') as f:
        segments_data = json.load(f)
    
    # Generate discount recommendations
    discount_recommendations = generate_discount_recommendations(segments_data)
    
    # Save discount recommendations
    output_dir = os.path.abspath(os.path.join(os.path.dirname(__file__), '../storage/app/public/discounts'))
    os.makedirs(output_dir, exist_ok=True)
    
    with open(os.path.join(output_dir, 'discount_recommendations.json'), 'w') as f:
        json.dump(discount_recommendations, f, indent=2, default=str)
    
    print("Discount recommendations generated!")
    print(f"Generated discounts for {len(discount_recommendations)} customers")
    
    # Print summary
    for customer_id, result in discount_recommendations.items():
        if result['discounts']:
            print(f"\n{customer_id} ({result['segment']}):")
            for discount in result['discounts']:
                print(f"  - {discount['description']}")
            print(f"  Total savings: ${result['total_savings']:.2f}")

if __name__ == "__main__":
    main() 