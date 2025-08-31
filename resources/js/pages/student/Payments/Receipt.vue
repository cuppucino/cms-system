<script setup lang="ts">
import StudentLayout from '@/layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

defineOptions({ layout: StudentLayout })

const props = defineProps<{
  payment: {
    id: number
    reference: string
    amount: string
    status: string
    created_at: string
    gown?: { id: number; size?: string | null } | null
  }
}>()
</script>

<template>
  <Head title="Payment Receipt" />

  <div class="max-w-lg mx-auto bg-white shadow rounded-xl border p-6">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-semibold">Payment Receipt</h2>
      <span
        class="rounded-full px-3 py-1 text-xs font-medium"
        :class="payment.status === 'paid' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'"
      >
        {{ payment.status }}
      </span>
    </div>

    <div class="mt-4 space-y-2 text-sm">
      <div class="flex justify-between">
        <span>Reference</span>
        <span class="font-mono">{{ payment.reference }}</span>
      </div>
      <div class="flex justify-between">
        <span>Amount</span>
        <span class="font-semibold">RM {{ payment.amount }}</span>
      </div>
      <div class="flex justify-between">
        <span>Date</span>
        <span>{{ payment.created_at }}</span>
      </div>
      <div class="flex justify-between">
        <span>Gown Size</span>
        <span>{{ payment.gown?.size ?? '—' }}</span>
      </div>
    </div>

    <div class="mt-6 text-right">
      <Link href="/student" class="rounded-lg border px-4 py-2 text-sm hover:bg-gray-100">
        Back to Dashboard
      </Link>
    </div>
  </div>
</template>
