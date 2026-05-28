import { ref } from 'vue'

export function useCrossover() {
  const API_BASE = import.meta.env.VITE_API_BASE_URL ?? 'http://localhost:8000'

  const loading = ref(false)
  const result = ref<any>(null)
  const history = ref<any[]>([])

  // Display parameters matching active calculations
  const activeSymbol = ref('')
  const activeInterval = ref('')
  const activeShort = ref(50)
  const activeLong = ref(200)

  const form = ref({
    symbol: 'BTCUSDT',
    interval: '30m',
    short_period: 50,
    long_period: 200,
    from: '2024-10-20T00:00',
    to: '2024-10-27T00:00'
  })

  async function loadHistory() {
    try {
      const key = sessionStorage.getItem('app_password')
      const response = await fetch(`${API_BASE}/api/v1/crossovers/history?password=${key}`)
      const data = await response.json()
      if (data.status === 'success') {
        history.value = data.data
      }
    } catch (e) {
      console.error(e)
    }
  }

  async function calculate() {
    loading.value = true
    try {
      const key = sessionStorage.getItem('app_password')
      // Format local datetime strings to backend format: Y-m-d H:i:s
      const fromFormatted = form.value.from.replace('T', ' ') + ':00'
      const toFormatted = form.value.to.replace('T', ' ') + ':00'

      const response = await fetch(`${API_BASE}/api/v1/crossovers/calculate?password=${key}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-App-Password': key || ''
        },
        body: JSON.stringify({
          symbol: form.value.symbol,
          interval: form.value.interval,
          from: fromFormatted,
          to: toFormatted,
          short_period: form.value.short_period,
          long_period: form.value.long_period
        })
      })
      
      const data = await response.json()
      if (data.status === 'success') {
        result.value = data.data
        activeSymbol.value = form.value.symbol
        activeInterval.value = form.value.interval
        activeShort.value = form.value.short_period
        activeLong.value = form.value.long_period
        
        loadHistory()
      } else {
        alert(JSON.stringify(data.data || data.message))
      }
    } catch (e: any) {
      console.error(e)
      alert('Error: ' + e.message)
    } finally {
      loading.value = false
    }
  }

  // Load parameters from log and trigger query
  function loadFromHistory(log: any) {
    form.value.symbol = log.symbol
    form.value.interval = log.interval
    form.value.short_period = log.short_period
    form.value.long_period = log.long_period
    
    // Format dates for input datetime-local
    form.value.from = log.from_date.replace(' ', 'T').substring(0, 16)
    form.value.to = log.to_date.replace(' ', 'T').substring(0, 16)
    
    calculate()
  }

  return {
    loading,
    result,
    history,
    activeSymbol,
    activeInterval,
    activeShort,
    activeLong,
    form,
    loadHistory,
    calculate,
    loadFromHistory
  }
}
