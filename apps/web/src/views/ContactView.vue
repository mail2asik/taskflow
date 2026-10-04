<script setup lang="ts">
import { ref } from 'vue'
import { useContactStore } from '../stores/contact'

const contactStore = useContactStore()

const submitted = ref(false)
const generalError = ref('')
const fieldErrors = ref<Record<string, string[]>>({})

const form = ref({
  name: '',
  email: '',
  message: '',
})

async function handleSubmit() {
  generalError.value = ''
  fieldErrors.value = {}

  try {
    const res = await contactStore.submitContact(form.value)
    if (res.success) {
      submitted.value = true
    }
  } catch (err: any) {
    if (err.response?.data?.errors) {
      fieldErrors.value = err.response.data.errors
    } else {
      generalError.value = err.response?.data?.message || 'Something went wrong. Please try again.'
    }
  }
}
</script>

<template>
  <div class="py-16 max-w-4xl mx-auto px-4 space-y-12">
    <div class="text-center space-y-4">
      <h1 class="text-4xl font-extrabold text-slate-900">Contact Our Team</h1>
      <p class="text-slate-600">Have questions about TaskFlow? We are here to help.</p>
    </div>

    <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
      <!-- General Error Message -->
      <div
        v-if="generalError"
        class="mb-6 p-4 bg-red-50 text-red-700 text-sm rounded-xl border border-red-200"
      >
        {{ generalError }}
      </div>

      <form v-if="!submitted" @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Name -->
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1">Your Name</label>
          <input
            v-model="form.name"
            type="text"
            required
            class="w-full px-4 py-2.5 rounded-xl border focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
            :class="fieldErrors.name ? 'border-red-400 bg-red-50/30' : 'border-slate-300'"
            placeholder="John Doe"
          />
          <p v-if="fieldErrors.name?.[0]" class="text-xs text-red-600 mt-1">
            {{ fieldErrors.name[0] }}
          </p>
        </div>

        <!-- Email -->
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1">Email Address</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="w-full px-4 py-2.5 rounded-xl border focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
            :class="fieldErrors.email ? 'border-red-400 bg-red-50/30' : 'border-slate-300'"
            placeholder="john@example.com"
          />
          <p v-if="fieldErrors.email?.[0]" class="text-xs text-red-600 mt-1">
            {{ fieldErrors.email[0] }}
          </p>
        </div>

        <!-- Message -->
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1">Message</label>
          <textarea
            v-model="form.message"
            rows="4"
            required
            minlength="10"
            class="w-full px-4 py-2.5 rounded-xl border focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
            :class="fieldErrors.message ? 'border-red-400 bg-red-50/30' : 'border-slate-300'"
            placeholder="Tell us how we can help..."
          ></textarea>
          <p v-if="fieldErrors.message?.[0]" class="text-xs text-red-600 mt-1">
            {{ fieldErrors.message[0] }}
          </p>
        </div>

        <button
          type="submit"
          :disabled="contactStore.isLoading"
          class="w-full py-3 bg-brand-600 text-white font-bold rounded-xl hover:bg-brand-700 disabled:opacity-50 transition shadow-md shadow-brand-600/20"
        >
          {{ contactStore.isLoading ? 'Sending...' : 'Send Message' }}
        </button>
      </form>

      <!-- Success State -->
      <div v-else class="text-center py-8 space-y-4">
        <div class="text-5xl">✅</div>
        <h3 class="text-2xl font-bold text-slate-900">Message Received!</h3>
        <p class="text-slate-600">Thank you for reaching out. Our support team will respond within 24 hours.</p>
      </div>
    </div>
  </div>
</template>