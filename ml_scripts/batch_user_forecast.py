import os
import pandas as pd
import json
from statsmodels.tsa.arima.model import ARIMA

EXPORT_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), '../storage/app/ml_exports'))
FORECAST_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), '../storage/app/public/forecasts'))
os.makedirs(FORECAST_DIR, exist_ok=True)

for filename in os.listdir(EXPORT_DIR):
    if filename.endswith('.csv'):
        # Parse role and user_id from filename
        parts = filename.split('_')
        if len(parts) < 3:
            continue
        role = parts[0]
        user_id = parts[1]
        csv_path = os.path.join(EXPORT_DIR, filename)
        df = pd.read_csv(csv_path)
        if df.empty:
            continue
        # Parse date and aggregate by month
        df['order_date'] = pd.to_datetime(df['order_date'])
        df['month'] = df['order_date'].dt.to_period('M').dt.to_timestamp()
        monthly = df.groupby('month')['quantity'].sum().sort_index()
        # Fit ARIMA model
        try:
            model = ARIMA(monthly, order=(1,1,1))
            model_fit = model.fit()
            forecast = model_fit.forecast(steps=6)
        except Exception as e:
            print(f"Forecast failed for {filename}: {e}")
            continue
        # Save forecast as JSON
        forecast_data = {
            "dates": [str(date)[:7] for date in forecast.index],
            "values": [float(val) for val in forecast.values]
        }
        out_name = f"forecast_{role}_{user_id}.json"
        out_path = os.path.join(FORECAST_DIR, out_name)
        with open(out_path, 'w') as f:
            json.dump(forecast_data, f)
        print(f"Saved forecast for {role} {user_id} to {out_name}") 