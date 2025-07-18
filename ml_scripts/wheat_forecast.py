import pandas as pd
import matplotlib.pyplot as plt
from statsmodels.tsa.arima.model import ARIMA
import os
import json

# Load the data
file_path = os.path.join(os.path.dirname(__file__), 'wheat_demand.csv')
df = pd.read_csv(file_path)

# Combine year and month/period into a single date (try to parse as much as possible)
def parse_date(row):
    year = row['Date/Time'][:4]
    month = row['Date/Time'][-3:]
    # Try to get month from the period, fallback to Jan if not found
    try:
        month_num = pd.to_datetime(month, format='%b').month
    except:
        month_num = 1
    return pd.Timestamp(year=int(year), month=month_num, day=1)

df['parsed_date'] = df.apply(parse_date, axis=1)

# Aggregate demand by month
df_monthly = df.groupby('parsed_date')['Demand per year'].sum().sort_index()

# Plot the historical demand
df_monthly.plot(title='Historical Wheat Demand', marker='o')
plt.ylabel('Demand per year')
plt.show()

# Fit ARIMA model (simple, for demonstration)
model = ARIMA(df_monthly, order=(1,1,1))
model_fit = model.fit()

# Forecast next 6 months
forecast = model_fit.forecast(steps=6)
print('Forecast for next 6 months:')
print(forecast)

# Plot forecast
plt.figure()
df_monthly.plot(label='Observed', marker='o')
forecast.index = pd.date_range(df_monthly.index[-1] + pd.offsets.MonthBegin(), periods=6, freq='MS')
forecast.plot(label='Forecast', marker='x')
plt.legend()
plt.title('Wheat Demand Forecast')
plt.ylabel('Demand per year')
plt.show()

# Save forecast to JSON for Laravel
forecast_data = {
    "dates": [str(date)[:7] for date in forecast.index],  # YYYY-MM
    "values": [float(val) for val in forecast.values]
}
output_path = os.path.join(os.path.dirname(__file__), '../storage/app/public/forecast_results.json')
with open(output_path, 'w') as f:
    json.dump(forecast_data, f) 