<template>
  <div class="min-h-screen p-4 md:p-8 bg-[var(--bg-primary)] text-[var(--text-main)] transition-colors duration-300">
    
    <!-- Modularized Header -->
    <Header :theme="theme" @toggle-theme="toggleTheme" />

    <!-- Authentication Gate vs Main Layout -->
    <main class="max-w-7xl mx-auto">
      <PasswordGuard 
        v-if="!isAuthenticated" 
        v-model="password" 
        @authenticate="authenticate" 
      />

      <div v-else class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Modularized Config Form -->
        <CrossoverForm 
          v-model="form" 
          :loading="loading" 
          @calculate="calculate" 
        />

        <!-- Calculations Results and Graph -->
        <section class="lg:col-span-3 space-y-6">
          
          <!-- Modularized Chart Component -->
          <CrossoverChart 
            ref="chartRef"
            :result="result" 
            :active-symbol="activeSymbol" 
            :active-interval="activeInterval" 
            :active-short="activeShort" 
            :active-long="activeLong" 
            :theme="theme"
          />

          <!-- Modularized Crossover List Table -->
          <CrossoverTable 
            :result="result" 
            @focus-chart="focusChart" 
          />

          <!-- Modularized Saved DB History Log -->
          <HistoryTable 
            :history="history" 
            @load-log="loadFromHistory" 
          />
        </section>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

// Common components
import Header from './components/Header.vue'
import PasswordGuard from './components/PasswordGuard.vue'

// Feature-based crossover components
import CrossoverForm from './features/crossover/components/CrossoverForm.vue'
import CrossoverChart from './features/crossover/components/CrossoverChart.vue'
import CrossoverTable from './features/crossover/components/CrossoverTable.vue'
import HistoryTable from './features/crossover/components/HistoryTable.vue'

// Feature-based composable
import { useCrossover } from './features/crossover/composables/useCrossover'

const theme = ref<'dark' | 'light'>('dark')
const password = ref('')
const isAuthenticated = ref(false)

const chartRef = ref<any>(null)

// Destructure composable methods and state
const {
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
} = useCrossover()

function toggleTheme() {
  theme.value = theme.value === 'dark' ? 'light' : 'dark'
  if (theme.value === 'light') {
    document.documentElement.classList.add('light-theme')
  } else {
    document.documentElement.classList.remove('light-theme')
  }
  localStorage.setItem('theme', theme.value)
}

function authenticate() {
  if (password.value) {
    sessionStorage.setItem('app_password', password.value)
    isAuthenticated.value = true
    loadHistory()
  }
}

// Focus timescale to specific cross point via child chart exposure
function focusChart(timestamp: number) {
  if (chartRef.value) {
    chartRef.value.focusChart(timestamp)
  }
}

onMounted(() => {
  // Theme check
  const savedTheme = localStorage.getItem('theme') as 'dark' | 'light' | null
  if (savedTheme) {
    theme.value = savedTheme
    if (savedTheme === 'light') {
      document.documentElement.classList.add('light-theme')
    }
  }

  const savedPass = sessionStorage.getItem('app_password')
  if (savedPass) {
    password.value = savedPass
    isAuthenticated.value = true
    loadHistory()
  }
})
</script>

<style lang="scss" scoped>
select, input {
  transition: border-color 0.2s, background-color 0.3s;
}
</style>
