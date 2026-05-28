<template>
  <div class="glass-panel p-8 max-w-md mx-auto w-full text-center">
    <div class="w-16 h-16 bg-blue-500/10 border border-blue-500/20 text-blue-400 rounded-full flex items-center justify-center mx-auto mb-6">
      <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
      </svg>
    </div>
    <h2 class="text-xl font-bold mb-2">{{ t('authRequired') }}</h2>
    <p class="text-sm text-[var(--text-secondary)] mb-6">{{ t('enterPasswordDesc') }}</p>
    <input 
      :value="modelValue" 
      @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
      type="password" 
      placeholder="••••••••" 
      class="w-full bg-[var(--input-bg)] border border-[var(--input-border)] rounded-lg px-4 py-3 text-center mb-4 text-[var(--text-main)] focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
      @keyup.enter="$emit('authenticate')"
    />
    <button @click="$emit('authenticate')" class="custom-button w-full cursor-pointer">
      {{ t('login') }}
    </button>
  </div>
</template>

<script setup lang="ts">
import { t } from '../i18n'

defineProps<{
  modelValue: string
}>()

defineEmits<{
  (e: 'update:modelValue', val: string): void
  (e: 'authenticate'): void
}>()
</script>
