<template>
  <div class="glass-panel p-6">
    <h2 class="text-lg font-bold mb-6 flex items-center gap-2">
      <span class="w-2 h-5 bg-purple-500 rounded-full inline-block"></span>
      {{ t('historicLog') }}
    </h2>

    <div class="overflow-x-auto max-h-[300px] overflow-y-auto">
      <table class="w-full text-left text-sm text-[var(--text-main)]">
        <thead class="bg-[var(--bg-table-header)] text-[var(--text-secondary)] uppercase text-xs sticky top-0 z-10">
          <tr>
            <th class="p-3">{{ t('market') }}</th>
            <th class="p-3">{{ t('range') }}</th>
            <th class="p-3">{{ t('periods') }}</th>
            <th class="p-3">{{ t('crossesCount') }}</th>
            <th class="p-3 text-right">{{ t('actions') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[var(--border-glass)]">
          <tr 
            v-for="log in history" 
            :key="log.id" 
            class="hover:bg-[var(--bg-table-row-hover)] transition-colors cursor-pointer"
            @click="$emit('load-log', log)"
          >
            <td class="p-3 font-bold text-blue-500">{{ log.symbol }} ({{ log.interval }})</td>
            <td class="p-3 text-xs text-[var(--text-secondary)]">
              {{ log.from_date }} <br/> {{ log.to_date }}
            </td>
            <td class="p-3 font-mono">Short: {{ log.short_period }} | Long: {{ log.long_period }}</td>
            <td class="p-3">
              <span class="bg-purple-500/10 border border-purple-500/20 text-purple-400 px-2.5 py-0.5 rounded-full text-xs font-bold inline-block">
                {{ log.crossover_count }}
              </span>
            </td>
            <td class="p-3 text-right">
              <button 
                class="text-xs text-purple-500 hover:text-purple-400 font-bold underline cursor-pointer"
                @click.stop="$emit('load-log', log)"
              >
                {{ t('loadAnalysis') }}
              </button>
            </td>
          </tr>
          <tr v-if="history.length === 0">
            <td colspan="5" class="p-6 text-center text-[var(--text-secondary)]">{{ t('noCalculations') }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { t } from '../../../i18n'

defineProps<{
  history: any[]
}>()

defineEmits<{
  (e: 'load-log', log: any): void
}>()
</script>
