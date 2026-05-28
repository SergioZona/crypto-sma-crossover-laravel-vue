import { ref } from 'vue'
import es from './locales/es.json'
import en from './locales/en.json'

export type Lang = 'es' | 'en'

// Read saved preference or default to browser language / 'es'
const savedLang = localStorage.getItem('lang') as Lang | null
export const lang = ref<Lang>(savedLang || 'es')

export function setLanguage(newLang: Lang) {
  lang.value = newLang
  localStorage.setItem('lang', newLang)
}

export const translations = {
  es,
  en
}

export function t(key: string): string {
  return (translations[lang.value] as any)[key] || key
}
