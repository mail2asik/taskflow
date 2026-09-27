<script setup lang="ts">
import { ref } from 'vue'

const submitted = ref(false)
const form = ref({
  name: '',
  email: '',
  message: '',
})

function handleSubmit() {
  submitted.value = true
}
</script>

<template>
  <div class="py-16 max-w-4xl mx-auto px-4 space-y-12">
    <div class="text-center space-y-4">
      <h1 class="text-4xl font-extrabold text-slate-900">Contact Our Team</h1>
      <p class="text-slate-600">Have questions about TaskFlow? We are here to help.</p>
    </div>

    <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
      <form v-if="!submitted" @submit.prevent="handleSubmit" class="space-y-6">
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1">Your Name</label>
          <input
            v-model="form.name"
            type="text"
            required
            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            placeholder="John Doe"
          />
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1">Email Address</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            placeholder="john@example.com"
          />
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1">Message</label>
          <textarea
            v-model="form.message"
            rows="4"
            required
            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            placeholder="Tell us how we can help..."
          ></textarea>
        </div>

        <button
          type="submit"
          class="w-full py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition"
        >
          Send Message
        </button>
      </form>

      <div v-else class="text-center py-8 space-y-4">
        <div class="text-5xl">✅</div>
        <h3 class="text-2xl font-bold text-slate-900">Message Received!</h3>
        <p class="text-slate-600">Thank you for reaching out. Our support team will respond within 24 hours.</p>
      </div>
    </div>
  </div>
</template>