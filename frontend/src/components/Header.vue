<template>
  <header class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4 mb-8">
    <div>
      <h1 class="text-3xl font-extrabold gradient-title flex items-center gap-3">
        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
        </svg>
        {{ t('title') }}
      </h1>
      <p class="text-[var(--text-secondary)] text-sm mt-1">{{ t('subtitle') }}</p>
    </div>

    <div class="flex items-center gap-3">
      <!-- Theme Toggle -->
      <button 
        @click="$emit('toggle-theme')" 
        class="p-2.5 rounded-lg bg-[var(--bg-panel)] border border-[var(--border-glass)] text-[var(--text-main)] hover:border-blue-500 transition-all cursor-pointer"
        title="Toggle Light/Dark Theme"
      >
        <svg v-if="theme === 'dark'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"></path>
        </svg>
        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
        </svg>
      </button>

      <!-- Language Selector -->
      <div class="flex items-center gap-1 bg-[var(--bg-panel)] p-1 rounded-lg border border-[var(--border-glass)]">
        <button 
          @click="setLanguage('es')" 
          :class="['px-2.5 py-1 rounded text-xs font-bold transition-all cursor-pointer', lang === 'es' ? 'bg-blue-600 text-white' : 'text-[var(--text-secondary)] hover:text-[var(--text-main)]']"
        >
          ES
        </button>
        <button 
          @click="setLanguage('en')" 
          :class="['px-2.5 py-1 rounded text-xs font-bold transition-all cursor-pointer', lang === 'en' ? 'bg-blue-600 text-white' : 'text-[var(--text-secondary)] hover:text-[var(--text-main)]']"
        >
          EN
        </button>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { lang, t, setLanguage } from '../i18n'

defineProps<{
  theme: 'dark' | 'light'
}>()

defineEmits<{
  (e: 'toggle-theme'): void
}>()
</script>
