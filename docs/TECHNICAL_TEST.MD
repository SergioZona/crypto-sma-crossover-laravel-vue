# Technical Test - Control Ganadero

## Test Description

Create a SPA that calculates how many crossovers exist between two simple moving averages (SMA) within a specified timeframe in a Binance crypto market.

A small form should be provided to input configuration parameters (Market, Time Interval, Date Range, and period lengths for both SMAs). Submitting the form should output the number of SMA crossovers, the exact time they occurred (Date and Time), and whether the crossover was ascending or descending relative to the shorter SMA.

The test should be delivered as a public repository using GitHub, Bitbucket, or GitLab.

**Additional Remarks**

* Visual graphing of prices or crossovers is not required.

Example:
In this chart, two moving average crossovers are observed between October 21 and 26, 2024, for the **BTCUSDT** (Bitcoin - Tether) market on a 30-minute time interval.

* Blue line (Simple Moving Average with a length of 50)
* Yellow line (Simple Moving Average with a length of 200)

Result:
2 SMA Crossovers:
* 2024-10-21 18:00 (Descending)
* 2024-10-24 18:00 (Ascending)

**Non-Functional Requirements**
* Use Laravel as the project's base framework.
* Use Vue.js to develop the frontend.

**Functional Requirements**
* Consume the Binance SPOT API using Klines to fetch historical market data.
* Specify the period length to calculate both SMAs.
* Select the market from the following list:
  * BTCUSDT
  * ETHUSDT
  * XRPUSDT
* Select the timeframe between a specific start date/time and end date/time (e.g., From: 2021-01-01 10:00 To: 2022-01-01 10:00).
* Select the time interval to use:
  * 1m: 1 Minute
  * 3m: 3 Minutes
  * 5m: 5 Minutes
  * 15m: 15 Minutes
  * 30m: 30 Minutes
  * 1h: 1 Hour
  * 2h: 2 Hours
  * 4h: 4 Hours
  * 6h: 6 Hours
  * 8h: 8 Hours
  * 12h: 12 Horas
  * 1d: 1 Day
  * 3d: 3 Days
  * 1w: 1 Week
* Save the query result history in a database.
* The web application should support Internationalization (i18n) for different languages.

---

## Evaluation Criteria

| Aspect | Points |
|---|---|
| Application of SOLID principles | 10 |
| Design patterns applied | 10 |
| Project organization, modularity, and architecture (Abstraction, Scalability, Maintainability) | 10 |
| Code readability | 10 |
| Testability and testing | 10 |
| Mathematical logic | 10 |
| Frontend implementation | 10 |
| API integration | 10 |
| Data processing and analysis | 10 |
| Database management | 10 |
| **TOTAL** | **100** |

---

## Support Material

### Simple Moving Average
The simple (or arithmetic) moving average is calculated by summing a specific number of price data points (e.g., 10 days) and dividing that sum by the total number of periods (10). The result is the average price over that period.

### Test Credentials:
* API KEY: `YOUR_KEY`
* API SECRET: `YOUR_API`

### Binance Documentation:
* Market SPOT API - KLines
