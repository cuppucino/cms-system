<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps<{
  payment: {
    id: number
    reference: string
    amount: number
    status: string
    created_at: string
    gown_collection: { name: string }
  }
}>()
</script>

<template>
  <Head title="Payment Receipt" />

  <AppLayout :breadcrumbs="[{ title: 'Receipt', href: '#' }]">
    <div class="max-w-lg mx-auto bg-white shadow rounded-lg p-6">
      <h2 class="text-xl font-semibold mb-4">Payment Receipt</h2>

      <div class="space-y-2">
        <p><span class="font-bold">Reference:</span> {{ props.payment.reference }}</p>
        <p><span class="font-bold">Gown:</span> {{ props.payment.gown_collection.name }}</p>
        <p><span class="font-bold">Amount:</span> RM {{ props.payment.amount }}</p>
        <p><span class="font-bold">Status:</span>
          <span :class="props.payment.status === 'paid' ? 'text-green-600' : 'text-red-600'">
            {{ props.payment.status }}
          </span>
        </p>
        <p><span class="font-bold">Date:</span> {{ props.payment.created_at }}</p>
      </div>

      <div class="mt-6 text-center">
        <Button class="bg-blue-600 text-white px-4 py-2" @click="$inertia.visit('/dashboard')">
          Back to Dashboard
        </Button>
      </div>
    </div>
  </AppLayout>
</template>
