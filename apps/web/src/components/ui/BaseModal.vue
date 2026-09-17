<script setup lang="ts">
defineProps<{
  isOpen: boolean
  title: string
}>()

defineEmits(['close'])
</script>

<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
      @click.self="$emit('close')"
    >
      <div
        class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-lg w-full p-6 transform transition-all"
      >
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
          <h3 class="text-lg font-bold text-slate-800">{{ title }}</h3>
          <button
            @click="$emit('close')"
            class="text-slate-400 hover:text-slate-600 rounded-lg p-1 transition"
          >
            ✕
          </button>
        </div>
        <slot />
      </div>
    </div>
  </Teleport>
</template>