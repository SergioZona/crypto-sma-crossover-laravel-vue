# Crossover Calculation Algorithm & System Architecture

This document outlines the algorithm, data structures, optimization techniques, and lifecycle execution flows used to calculate Moving Average Crossovers efficiently.

---

## SMA Calculation Algorithm

Calculating Simple Moving Averages (SMAs) over raw price points can be done in two ways:
1. **Naive approach**: For every point, sum the last $P$ periods. Complexity: $O(N \cdot P)$.
2. **Sliding-window prefix sum approach**: Maintain a running sum. Shift the window right by adding the new element and subtracting the oldest element. Complexity: $O(N)$ time, $O(N)$ space.

We use the **Sliding-window prefix sum approach** to guarantee optimal linear-time performance even with very large datasets or high-frequency intervals.

### Complexity Analysis
- **Time Complexity**: $\mathcal{O}(N)$ where $N$ is the number of candlesticks. We iterate through the array of closes exactly once.
- **Space Complexity**: $\mathcal{O}(N)$ to store the computed SMA values corresponding to each candlestick.

---

## Crossover Detection Algorithm

Once SMA arrays of short period $S$ and long period $L$ are calculated, crossover points are detected in a single linear scan:

1. Let $i$ range from $L$ to $N-1$ (since we need at least $L$ data points to start having both SMAs).
2. Compare the relations at index $i-1$ and index $i$:
   - **Golden Cross (Ascending Crossover)**: If $SMA_{short}[i-1] \le SMA_{long}[i-1]$ AND $SMA_{short}[i] > SMA_{long}[i]$.
   - **Death Cross (Descending Crossover)**: If $SMA_{short}[i-1] \ge SMA_{long}[i-1]$ AND $SMA_{short}[i] < SMA_{long}[i]$.

### Complexity Analysis
- **Time Complexity**: $\mathcal{O}(N)$ linear scan.
- **Space Complexity**: $\mathcal{O}(C)$ where $C$ is the number of crossover points discovered ($C \le N$).

---

## Call Flow Sequence Diagram

The flow below details how requests are processed by using Database caches and Redis caching layer to avoid duplicate external API queries and CPU recalculation.

```mermaid
sequence diagram
    autonumber
    actor User as Frontend User
    participant API as CrossoverApiController
    participant DB as Postgres DB
    participant Cache as Redis Cache
    participant Binance as Binance API

    User->>API: POST /api/v1/crossovers/calculate
    Note over API: Generate unique Cache Key from params
    API->>Cache: Check "crossover_calc_res:{hash}"
    alt Cache Hit
        Cache-->>API: Return complete cached result
        API-->>User: Return success response (Instant)
    else Cache Miss
        API->>DB: Query CalculationHistory for exact params
        alt DB Record Found
            DB-->>API: Return DB crossovers record
            API->>Binance: Fetch Candles (via BinanceService cache remember)
            Binance-->>API: Return Candles (Redis-cached or new API call)
            Note over API: Reconstruct chart data (SMAs computed, Crossovers from DB)
            API->>Cache: Save final response to Redis Cache (3600s)
            API-->>User: Return success response
        else DB Record Miss
            API->>Binance: Fetch Candles with buffer offset (via BinanceService cache remember)
            Binance-->>API: Return candles data
            Note over API: Execute linear-time SMA and Crossover calculation
            API->>DB: Save query record to calculation_history
            API->>Cache: Save final response to Redis Cache (3600s)
            API-->>User: Return success response
        end
    end
```

---

## Optimization Details

1. **Buffer Precomputation**: A time offset of $L \times \text{interval\_seconds}$ is subtracted from the start time, fetching historical candles from before the range. This ensures that the moving averages are already fully computed and accurate from the very first minute of the user-selected date range.
2. **JSend Output Structure**: Results are formatted using the JSend specification format for reliable communication.
