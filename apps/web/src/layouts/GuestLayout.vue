<script setup lang="ts">
import { ref } from 'vue'
import { RouterLink, RouterView } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useSubscriberStore } from '../stores/subscriber'

const authStore = useAuthStore()
const subscriberStore = useSubscriberStore()

const mobileMenuOpen = ref(false)

// Subscription Form Reactive State
const email = ref('')
const subscribeSuccess = ref(false)
const subscribeError = ref('')

async function handleSubscribe() {
  subscribeError.value = ''
  subscribeSuccess.value = false

  if (!email.value) return

  try {
    const response = await subscriberStore.subscribe({ email: email.value })
    if (response.success) {
      subscribeSuccess.value = true
      email.value = '' // Clear input on success
    }
  } catch (err: any) {
    if (err.response?.data?.errors?.email) {
      subscribeError.value = err.response.data.errors.email[0]
    } else {
      subscribeError.value = err.response?.data?.message || 'Failed to subscribe. Please try again.'
    }
  }
}
</script>

<template>
  <div class="min-h-screen bg-slate-50/60 flex flex-col font-sans antialiased text-slate-800">
    <!-- Navigation Header -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
        
        <!-- Logo (Left Side) -->
        <RouterLink to="/" class="flex items-center gap-3 group">
          <div class="w-10 h-10 rounded-xl bg-brand-600 flex items-center justify-center text-white font-bold text-xl shadow-md shadow-brand-600/30 group-hover:scale-105 transition-transform">
            ⚡
          </div>
          <span class="text-2xl font-black text-slate-900 tracking-tight">
            TaskFlow
          </span>
        </RouterLink>

        <!-- Desktop Links (Center) -->
        <nav class="hidden md:flex items-center gap-1 bg-slate-100/80 p-1.5 rounded-full border border-slate-200/60">
          <RouterLink
            to="/"
            exact-active-class="bg-white text-brand-600 shadow-sm font-bold"
            class="px-5 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 rounded-full transition-all"
          >
            Home
          </RouterLink>
          <RouterLink
            to="/about"
            exact-active-class="bg-white text-brand-600 shadow-sm font-bold"
            class="px-5 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 rounded-full transition-all"
          >
            About Us
          </RouterLink>
          <RouterLink
            to="/how-it-works"
            exact-active-class="bg-white text-brand-600 shadow-sm font-bold"
            class="px-5 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 rounded-full transition-all"
          >
            How It Works
          </RouterLink>
          <RouterLink
            to="/contact"
            exact-active-class="bg-white text-brand-600 shadow-sm font-bold"
            class="px-5 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 rounded-full transition-all"
          >
            Contact Us
          </RouterLink>
        </nav>

        <!-- Dynamic Auth / Application Actions (Right Side) -->
        <div class="hidden md:flex items-center gap-3">
          <template v-if="!authStore.isAuthenticated">
            <RouterLink
              to="/login"
              class="px-5 py-2.5 text-sm font-bold text-slate-700 hover:text-brand-600 transition"
            >
              Sign In
            </RouterLink>
            <RouterLink
              to="/register"
              class="px-6 py-2.5 text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl shadow-md shadow-brand-600/25 transition-all active:scale-95 flex items-center gap-2"
            >
              <span>Get Started</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
              </svg>
            </RouterLink>
          </template>
          <template v-else>
            <RouterLink
              to="/app/projects"
              class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl shadow-md shadow-brand-600/25 transition-all"
            >
              <span>Dashboard</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
              </svg>
            </RouterLink>
          </template>
        </div>

        <!-- Mobile Menu Toggle Button -->
        <button
          @click="mobileMenuOpen = !mobileMenuOpen"
          class="md:hidden p-2.5 rounded-xl text-slate-600 hover:bg-slate-100 focus:outline-none"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Mobile Dropdown Navigation -->
      <div v-if="mobileMenuOpen" class="md:hidden border-b border-slate-200 bg-white px-4 pt-2 pb-6 space-y-2">
        <RouterLink
          to="/"
          @click="mobileMenuOpen = false"
          class="block px-4 py-2.5 rounded-lg text-base font-medium text-slate-700 hover:bg-brand-50 hover:text-brand-600"
        >
          Home
        </RouterLink>
        <RouterLink
          to="/about"
          @click="mobileMenuOpen = false"
          class="block px-4 py-2.5 rounded-lg text-base font-medium text-slate-700 hover:bg-brand-50 hover:text-brand-600"
        >
          About Us
        </RouterLink>
        <RouterLink
          to="/how-it-works"
          @click="mobileMenuOpen = false"
          class="block px-4 py-2.5 rounded-lg text-base font-medium text-slate-700 hover:bg-brand-50 hover:text-brand-600"
        >
          How It Works
        </RouterLink>
        <RouterLink
          to="/contact"
          @click="mobileMenuOpen = false"
          class="block px-4 py-2.5 rounded-lg text-base font-medium text-slate-700 hover:bg-brand-50 hover:text-brand-600"
        >
          Contact Us
        </RouterLink>

        <div class="pt-4 border-t border-slate-100 flex flex-col gap-2">
          <template v-if="!authStore.isAuthenticated">
            <RouterLink
              to="/login"
              @click="mobileMenuOpen = false"
              class="w-full text-center px-4 py-2.5 text-sm font-bold text-slate-700 border border-slate-200 rounded-xl"
            >
              Sign In
            </RouterLink>
            <RouterLink
              to="/register"
              @click="mobileMenuOpen = false"
              class="w-full text-center px-4 py-2.5 text-sm font-bold text-white bg-brand-600 rounded-xl shadow-md"
            >
              Get Started Free
            </RouterLink>
          </template>
          <template v-else>
            <RouterLink
              to="/app/projects"
              @click="mobileMenuOpen = false"
              class="w-full text-center px-4 py-2.5 text-sm font-bold text-white bg-brand-600 rounded-xl shadow-md"
            >
              Go to Dashboard
            </RouterLink>
          </template>
        </div>
      </div>
    </header>

    <!-- Public Page View Outlet -->
    <main class="flex-1">
      <RouterView />
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 border-t border-slate-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
          
          <div class="space-y-4 md:col-span-1">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-brand-600 flex items-center justify-center text-white font-bold">
                ⚡
              </div>
              <span class="text-xl font-bold text-white">TaskFlow</span>
            </div>
            <p class="text-sm leading-relaxed text-slate-400">
              Empowering agile software teams to plan, collaborate, and deliver code on schedule.
            </p>
          </div>

          <div>
            <h3 class="text-xs font-bold text-slate-300 tracking-wider uppercase mb-4">Navigation</h3>
            <ul class="space-y-2.5 text-sm">
              <li><RouterLink to="/" class="hover:text-white transition">Home</RouterLink></li>
              <li><RouterLink to="/about" class="hover:text-white transition">About Us</RouterLink></li>
              <li><RouterLink to="/how-it-works" class="hover:text-white transition">How It Works</RouterLink></li>
              <li><RouterLink to="/contact" class="hover:text-white transition">Contact Us</RouterLink></li>
            </ul>
          </div>

          <div>
            <h3 class="text-xs font-bold text-slate-300 tracking-wider uppercase mb-4">Resources</h3>
            <ul class="space-y-2.5 text-sm">
              <li><a href="#" class="hover:text-white transition">Documentation</a></li>
              <li><a href="#" class="hover:text-white transition">API Reference</a></li>
              <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
              <li><a href="#" class="hover:text-white transition">Terms of Service</a></li>
            </ul>
          </div>

          <!-- Subscription Column -->
          <div>
            <h3 class="text-xs font-bold text-slate-300 tracking-wider uppercase mb-4">Subscribe</h3>
            <p class="text-xs text-slate-400 mb-3">Get product releases and engineering updates.</p>
            
            <form @submit.prevent="handleSubscribe" class="flex flex-col gap-2">
              <input
                v-model="email"
                type="email"
                required
                placeholder="Enter your email"
                class="px-3.5 py-2 text-sm rounded-lg bg-slate-800 border text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 transition"
                :class="subscribeError ? 'border-red-500' : 'border-slate-700'"
              />

              <!-- Error Message Feedback -->
              <p v-if="subscribeError" class="text-xs text-red-400">
                {{ subscribeError }}
              </p>

              <!-- Success Message Feedback -->
              <p v-if="subscribeSuccess" class="text-xs text-emerald-400 font-medium flex items-center gap-1">
                <span>✓</span> Subscribed successfully!
              </p>

              <button
                type="submit"
                :disabled="subscriberStore.isLoading"
                class="px-4 py-2 text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 disabled:opacity-50 rounded-lg transition shadow-md"
              >
                {{ subscriberStore.isLoading ? 'Subscribing...' : 'Subscribe' }}
              </button>
            </form>
          </div>
        </div>

        <div class="pt-8 border-t border-slate-800 flex flex-col md:flex-row items-center justify-between text-xs text-slate-500 gap-4">
          <p>© {{ new Date().getFullYear() }} TaskFlow Inc. All rights reserved.</p>
          <div class="flex gap-6">
            <a href="#" class="hover:text-slate-400">Twitter</a>
            <a href="#" class="hover:text-slate-400">GitHub</a>
            <a href="#" class="hover:text-slate-400">LinkedIn</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>