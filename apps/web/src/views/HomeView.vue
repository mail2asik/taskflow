<script setup lang="ts">
import { RouterLink } from 'vue-router'

const features = [
  {
    icon: '📋',
    title: 'Intuitive Kanban Boards',
    description: 'Organize issues, drag-and-drop tasks across custom workflows, and maintain complete visibility.',
  },
  {
    icon: '⚡',
    title: 'Real-Time Sync',
    description: 'Instantly receive issue updates and status transitions across all team members via WebSockets.',
  },
  {
    icon: '🏷️',
    title: 'Custom Labels & Roles',
    description: 'Assign role-based permissions (Owners, Members) and organize issues with custom tags.',
  },
  {
    icon: '📊',
    title: 'Project Analytics',
    description: 'Track team velocity, open vs. resolved issues, and identify delivery bottlenecks easily.',
  },
]
</script>

<template>
  <div class="space-y-24 py-12">
    <!-- Hero Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8 pt-6">
      
      <!-- Badge Pill -->
      <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-brand-50 border border-brand-100 text-brand-600 text-xs font-bold uppercase tracking-wider">
        <span class="flex h-2 w-2 rounded-full bg-brand-600 animate-pulse"></span>
        Next-Gen Agile Project Management
      </div>

      <!-- Main Headline -->
      <h1 class="text-5xl sm:text-6xl font-black text-slate-900 tracking-tight max-w-4xl mx-auto leading-tight">
        Manage Projects and Tasks with <span class="text-brand-600">Zero Friction</span>
      </h1>

      <p class="text-lg sm:text-xl text-slate-600 max-w-2xl mx-auto leading-relaxed">
        TaskFlow is built for modern engineering teams. Track bugs, organize backlogs, and collaborate seamlessly using flexible Kanban boards.
      </p>

      <!-- Action Buttons -->
      <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
        <RouterLink
          to="/register"
          class="w-full sm:w-auto px-8 py-4 text-base font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl shadow-lg shadow-brand-600/30 transition-all active:scale-95 flex items-center justify-center gap-2"
        >
          <span>Start Free Trial</span>
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
          </svg>
        </RouterLink>
        <RouterLink
          to="/how-it-works"
          class="w-full sm:w-auto px-8 py-4 text-base font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl shadow-sm transition-all"
        >
          See How It Works
        </RouterLink>
      </div>

      <!-- Dashboard Graphic Preview -->
      <div class="pt-8 max-w-5xl mx-auto">
        <div class="rounded-2xl border border-slate-200/80 bg-white p-3 shadow-2xl shadow-slate-900/10">
          <div class="rounded-xl overflow-hidden bg-slate-900 text-slate-100 p-6 text-left space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
              <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-red-500"></span>
                <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                <span class="text-xs text-slate-400 font-mono ml-2">project-board / OnePlatform-V1.2</span>
              </div>
              <span class="text-xs text-brand-500 font-bold bg-brand-500/10 px-3 py-1 rounded-full border border-brand-500/20">Live WebSockets Sync</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 font-sans text-slate-800 pt-2">
              <div class="bg-slate-100 p-4 rounded-xl space-y-3">
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">To Do (2)</div>
                <div class="bg-white p-3 rounded-lg shadow-sm border border-slate-200 text-xs space-y-1">
                  <span class="text-brand-600 font-mono font-bold">OP-104</span>
                  <p class="font-semibold text-slate-800">Setup Redis Cache for API</p>
                </div>
              </div>
              <div class="bg-slate-100 p-4 rounded-xl space-y-3">
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">In Progress (1)</div>
                <div class="bg-white p-3 rounded-lg shadow-sm border border-slate-200 text-xs space-y-1 border-l-4 border-l-brand-600">
                  <span class="text-brand-600 font-mono font-bold">OP-102</span>
                  <p class="font-semibold text-slate-800">Implement ApiResponse Trait</p>
                </div>
              </div>
              <div class="bg-slate-100 p-4 rounded-xl space-y-3">
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Done (3)</div>
                <div class="bg-white p-3 rounded-lg shadow-sm border border-slate-200 text-xs space-y-1">
                  <span class="text-emerald-600 font-mono font-bold">OP-99</span>
                  <p class="font-semibold text-slate-800">Configure Auth Store in Pinia</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Feature Grid -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
        <h2 class="text-3xl font-extrabold text-slate-900">Everything you need to ship faster</h2>
        <p class="text-slate-600">Built to handle complex sprint planning without bloated software overhead.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div v-for="feature in features" :key="feature.title" class="bg-white p-8 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
          <div class="text-4xl mb-4">{{ feature.icon }}</div>
          <h3 class="text-lg font-bold text-slate-900 mb-2">{{ feature.title }}</h3>
          <p class="text-sm text-slate-600 leading-relaxed">{{ feature.description }}</p>
        </div>
      </div>
    </section>

    <!-- Call to Action Banner -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="bg-brand-600 rounded-3xl p-12 text-center text-white space-y-6 shadow-xl shadow-brand-600/20">
        <h2 class="text-3xl sm:text-4xl font-extrabold">Ready to streamline your workflow?</h2>
        <p class="text-brand-100 max-w-xl mx-auto text-lg">
          Join engineering teams that deliver projects on schedule with TaskFlow.
        </p>
        <div>
          <RouterLink
            to="/register"
            class="inline-block px-8 py-4 font-bold text-brand-600 bg-white hover:bg-slate-100 rounded-xl shadow-lg transition"
          >
            Create Your Account Now
          </RouterLink>
        </div>
      </div>
    </section>
  </div>
</template>