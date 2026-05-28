<template>
  <section class="glass-panel p-6 lg:col-span-1 h-fit">
    <h2 class="text-lg font-bold mb-6 flex items-center gap-2">
      <span class="w-2 h-5 bg-blue-500 rounded-full inline-block"></span>
      {{ t('configTitle') }}
    </h2>

    <div class="space-y-4">
      <div>
        <label class="block text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider mb-2">{{ t('symbol') }}</label>
        <select 
          :value="modelValue.symbol"
          @change="updateField('symbol', ($event.target as HTMLSelectElement).value)"
          class="w-full bg-[var(--input-bg)] border border-[var(--input-border)] rounded-lg px-3 py-2.5 text-[var(--text-main)] focus:outline-none focus:border-blue-500"
        >
          <option value="BTCUSDT">BTCUSDT</option>
          <option value="ETHUSDT">ETHUSDT</option>
          <option value="XRPUSDT">XRPUSDT</option>
        </select>
      </div>

      <div>
        <label class="block text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider mb-2">{{ t('interval') }}</label>
        <select 
          :value="modelValue.interval"
          @change="updateField('interval', ($event.target as HTMLSelectElement).value)"
          class="w-full bg-[var(--input-bg)] border border-[var(--input-border)] rounded-lg px-3 py-2.5 text-[var(--text-main)] focus:outline-none focus:border-blue-500"
        >
          <option value="1m">1m</option>
          <option value="3m">3m</option>
          <option value="5m">5m</option>
          <option value="15m">15m</option>
          <option value="30m">30m</option>
          <option value="1h">1h</option>
          <option value="2h">2h</option>
          <option value="4h">4h</option>
          <option value="6h">6h</option>
          <option value="8h">8h</option>
          <option value="12h">12h</option>
          <option value="1d">1d</option>
          <option value="3d">3d</option>
          <option value="1w">1w</option>
        </select>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider mb-2">{{ t('shortPeriod') }}</label>
          <input 
            type="number" 
            :value="modelValue.short_period"
            @input="updateField('short_period', parseInt(($event.target as HTMLInputElement).value) || 0)"
            class="w-full bg-[var(--input-bg)] border border-[var(--input-border)] rounded-lg px-3 py-2 text-[var(--text-main)] focus:outline-none focus:border-blue-500" 
          />
        </div>
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider mb-2">{{ t('longPeriod') }}</label>
          <input 
            type="number" 
            :value="modelValue.long_period"
            @input="updateField('long_period', parseInt(($event.target as HTMLInputElement).value) || 0)"
            class="w-full bg-[var(--input-bg)] border border-[var(--input-border)] rounded-lg px-3 py-2 text-[var(--text-main)] focus:outline-none focus:border-blue-500" 
          />
        </div>
      </div>

      <div>
        <label class="block text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider mb-2">{{ t('fromDate') }}</label>
        <input 
          type="datetime-local" 
          :value="modelValue.from"
          @input="updateField('from', ($event.target as HTMLInputElement).value)"
          class="w-full bg-[var(--input-bg)] border border-[var(--input-border)] rounded-lg px-3 py-2 text-[var(--text-main)] text-sm focus:outline-none focus:border-blue-500" 
        />
      </div>

      <div>
        <label class="block text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider mb-2">{{ t('toDate') }}</label>
        <input 
          type="datetime-local" 
          :value="modelValue.to"
          @input="updateField('to', ($event.target as HTMLInputElement).value)"
          class="w-full bg-[var(--input-bg)] border border-[var(--input-border)] rounded-lg px-3 py-2 text-[var(--text-main)] text-sm focus:outline-none focus:border-blue-500" 
        />
      </div>

      <button @click="$emit('calculate')" :disabled="loading" class="custom-button w-full mt-6 cursor-pointer">
        <span v-if="loading" class="flex items-center justify-center gap-2">
          <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
          </svg>
          {{ t('calculating') }}
        </span>
        <span v-else>{{ t('runCalculation') }}</span>
      </button>
    </div>
  </section>
</template>

<script setup lang="ts">
import { t } from '../../../i18n'

interface FormData {
  symbol: string
  interval: string
  short_period: number
  long_period: number
  from: string
  to: string
}

const props = defineProps<{
  modelValue: FormData
  loading: boolean
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: FormData): void
  (e: 'calculate'): void
}>()

function updateField(key: keyof FormData, val: any) {
  emit('update:modelValue', {
    ...props.modelValue,
    [key]: val
  })
}
</script>
