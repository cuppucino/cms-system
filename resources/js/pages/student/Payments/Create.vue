<script setup lang="ts">
import StudentLayout from '@/layouts/StudentLayout.vue'
import { Head, router } from '@inertiajs/vue3'

defineOptions({ layout: StudentLayout })

const props = defineProps<{
  gown: { id: number; size?: string | null }
  amount: string
}>()

const handlePay = () => {
  router.post(route('student.payments.store'), {
    gown_id: props.gown.id,
    amount: props.amount,
  })
}
</script>

<template>
  <Head title="Make Payment" />

  <div class="max-w-lg mx-auto bg-white shadow rounded-xl border p-6">
    <h2 class="text-lg font-semibold">Deposit Payment</h2>
    <p class="mt-1 text-sm text-gray-600">This is a dummy payment (no real charge).</p>

    <div class="mt-4 space-y-2 text-sm">
      <div class="flex justify-between">
        <span>Gown Size</span>
        <span class="font-medium">{{ props.gown.size ?? '—' }}</span>
      </div>
      <div class="flex justify-between">
        <span>Amount</span>
        <span class="text-indigo-600 font-semibold">RM {{ props.amount }}</span>
      </div>
    </div>

    <div class="mt-6">
      <button
        class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500"
        @click="handlePay"
      >
        Pay Now
      </button>
    </div>
  </div>
</template>
