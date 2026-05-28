<template>
  <div v-if="result" class="glass-panel p-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-lg font-bold flex items-center gap-2">
        <span class="w-2 h-5 bg-green-500 rounded-full inline-block"></span>
        {{ t('resultsTitle') }}
      </h2>
      <span class="bg-green-500/10 border border-green-500/20 text-green-400 text-xs px-3 py-1 rounded-full font-bold">
        {{ result.crossovers_count }} {{ t('crossesDetected') }}
      </span>
    </div>

    <div class="overflow-x-auto max-h-[300px] overflow-y-auto">
      <table class="w-full text-left text-sm text-[var(--text-main)]">
        <thead class="bg-[var(--bg-table-header)] text-[var(--text-secondary)] uppercase text-xs sticky top-0 z-10">
          <tr>
            <th class="p-3">{{ t('dateTime') }}</th>
            <th class="p-3">{{ t('direction') }}</th>
            <th class="p-3">SMA Short</th>
            <th class="p-3">SMA Long</th>
            <th class="p-3 text-right">{{ t('actions') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[var(--border-glass)]">
          <tr v-for="(cross, i) in result.crossovers" :key="i" class="hover:bg-[var(--bg-table-row-hover)] transition-colors">
            <td class="p-3 font-semibold">{{ cross.time }}</td>
            <td class="p-3">
              <span :class="['px-2.5 py-0.5 rounded text-xs font-bold inline-block', cross.type === 'Ascendente' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20']">
                {{ cross.type }}
              </span>
            </td>
            <td class="p-3 font-mono">{{ cross.short_sma }}</td>
            <td class="p-3 font-mono">{{ cross.long_sma }}</td>
            <td class="p-3 text-right">
              <button 
                @click="$emit('focus-chart', cross.timestamp)" 
                class="text-xs text-blue-500 hover:text-blue-400 font-bold underline cursor-pointer"
              >
                {{ t('viewOnChart') }}
              </button>
            </td>
          </tr>
          <tr v-if="result.crossovers.length === 0">
            <td colspan="5" class="p-6 text-center text-[var(--text-secondary)]">{{ t('noCrosses') }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { t } from '../../../i18n'

defineProps<{
  result: any
}>()

defineEmits<{
  (e: 'focus-chart', timestamp: number): void
}>()
</script>
