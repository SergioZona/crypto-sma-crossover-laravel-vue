<template>
  <div class="glass-panel p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
      <h2 class="text-lg font-bold flex items-center gap-2">
        <span class="w-2 h-5 bg-blue-500 rounded-full inline-block"></span>
        {{ t('marketChart') }}
        <span v-if="activeSymbol" class="text-xs font-normal text-[var(--text-secondary)]">
          ({{ activeSymbol }} - {{ activeInterval }})
        </span>
      </h2>
      <div v-if="result" class="flex flex-wrap gap-2 text-xs">
        <span class="px-2.5 py-1 rounded bg-blue-500/10 border border-blue-500/20 text-blue-400 font-semibold flex items-center gap-1.5">
          <span class="w-1.5 h-1.5 bg-cyan-400 rounded-full"></span>
          SMA {{ activeShort }}
        </span>
        <span class="px-2.5 py-1 rounded bg-amber-500/10 border border-amber-500/20 text-amber-400 font-semibold flex items-center gap-1.5">
          <span class="w-1.5 h-1.5 bg-amber-400 rounded-full"></span>
          SMA {{ activeLong }}
        </span>
      </div>
    </div>

    <!-- Div where lightweight chart will attach -->
    <div 
      ref="chartContainer" 
      class="w-full h-[400px] rounded-lg border border-[var(--border-glass)] bg-[var(--bg-primary)] overflow-hidden relative"
    >
      <div v-if="!result" class="absolute inset-0 flex flex-col items-center justify-center text-[var(--text-secondary)] p-6 text-center">
        <svg class="w-12 h-12 mb-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 12l3-3 3 3 4-4M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
        </svg>
        <p class="text-sm font-semibold">{{ t('noChartData') }}</p>
        <p class="text-xs mt-1">{{ t('noChartDesc') }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { createChart, createSeriesMarkers, CandlestickSeries, LineSeries } from 'lightweight-charts'
import type { UTCTimestamp } from 'lightweight-charts'
import { t } from '../../../i18n'

const props = defineProps<{
  result: any
  activeSymbol: string
  activeInterval: string
  activeShort: number
  activeLong: number
  theme: 'dark' | 'light'
}>()

const chartContainer = ref<HTMLElement | null>(null)
let chart: any = null
let candleSeries: any = null
let shortSmaSeries: any = null
let longSmaSeries: any = null

function initChart() {
  if (!chartContainer.value || !props.result) return

  // Destroy previous instance
  if (chart) {
    chart.remove()
    chart = null
  }

  const isDark = props.theme === 'dark'
  const textColor = isDark ? '#9ca3af' : '#4b5563'
  const gridColor = isDark ? '#1f2937' : '#e5e7eb'
  const borderColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.08)'

  chart = createChart(chartContainer.value, {
    layout: {
      background: { color: 'transparent' },
      textColor: textColor,
    },
    grid: {
      vertLines: { color: gridColor },
      horzLines: { color: gridColor },
    },
    rightPriceScale: {
      borderColor: borderColor,
    },
    timeScale: {
      borderColor: borderColor,
      timeVisible: true,
      secondsVisible: false,
    },
  })

  // Add Candlestick Series
  candleSeries = chart.addSeries(CandlestickSeries, {
    upColor: '#22c55e',
    downColor: '#ef4444',
    borderDownColor: '#ef4444',
    borderUpColor: '#22c55e',
    wickDownColor: '#ef4444',
    wickUpColor: '#22c55e',
  })

  candleSeries.setData(props.result.candles)

  // Add Short SMA Series
  shortSmaSeries = chart.addSeries(LineSeries, {
    color: '#06b6d4', // Cyan
    lineWidth: 2,
    title: `SMA ${props.activeShort}`,
  })
  shortSmaSeries.setData(props.result.short_smas)

  // Add Long SMA Series
  longSmaSeries = chart.addSeries(LineSeries, {
    color: '#f59e0b', // Gold/Amber
    lineWidth: 2,
    title: `SMA ${props.activeLong}`,
  })
  longSmaSeries.setData(props.result.long_smas)

  // Add crossover markers
  const markers = props.result.crossovers.map((cross: any) => {
    const isUp = cross.type === 'Ascendente'
    return {
      time: cross.timestamp as UTCTimestamp,
      position: isUp ? 'belowBar' : 'aboveBar',
      color: isUp ? '#22c55e' : '#ef4444',
      shape: isUp ? 'arrowUp' : 'arrowDown',
      text: isUp ? 'GOLDEN CROSS' : 'DEATH CROSS',
      size: 1.5
    }
  })

  createSeriesMarkers(candleSeries, markers)
  chart.timeScale().fitContent()
}

function focusChart(timestamp: number) {
  if (!chart) return
  chart.timeScale().scrollToPosition(0, false)
  chart.timeScale().setVisibleRange({
    from: (timestamp - 3600 * 6) as UTCTimestamp,
    to: (timestamp + 3600 * 6) as UTCTimestamp
  })
}

function handleResize() {
  if (chart && chartContainer.value) {
    chart.resize(chartContainer.value.clientWidth, 400)
  }
}

watch(() => props.result, () => {
  initChart()
}, { deep: true })

watch(() => props.theme, () => {
  if (props.result) {
    initChart()
  }
})

onMounted(() => {
  initChart()
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
  if (chart) {
    chart.remove()
  }
})

defineExpose({
  focusChart
})
</script>
