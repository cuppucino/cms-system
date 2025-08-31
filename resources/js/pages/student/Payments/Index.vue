<script setup lang="ts">
import StudentLayout from '@/layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

defineOptions({ layout: StudentLayout })

const props = defineProps<{
  payments: Array<{
    id: number
    reference: string
    amount: string
    status: string
    created_at: string
    gown?: { id: number; size?: string | null } | null
  }>
  canPay: boolean
  gownId?: number | null
  amount: string
}>()
</script>

<template>
  <Head title="Payments" />

  <div class="rounded-xl border bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between">
      <h1 class="text-lg font-semibold">Payments</h1>
      <Link
        v-if="canPay && gownId"
        :href="route('student.payments.create', gownId)"
        class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500"
      >
        Pay Deposit (RM {{ amount }})
      </Link>
    </div>

    <div v-if="!payments.length" class="mt-6 text-sm text-gray-600">
      No payments yet.
    </div>

    <div v-else class="mt-4 overflow-hidden rounded-lg border">
      <table class="w-full text-left text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-3 py-2">Reference</th>
            <th class="px-3 py-2">Gown Size</th>
            <th class="px-3 py-2">Amount</th>
            <th class="px-3 py-2">Status</th>
            <th class="px-3 py-2">Date</th>
            <th class="px-3 py-2 text-right">Receipt</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <tr v-for="p in payments" :key="p.id" class="hover:bg-gray-50">
            <td class="px-3 py-2 font-mono">{{ p.reference }}</td>
            <td class="px-3 py-2">{{ p.gown?.size ?? '—' }}</td>
            <td class="px-3 py-2">RM {{ p.amount }}</td>
            <td class="px-3 py-2">
              <span
                class="rounded-full px-2 py-0.5 text-xs font-medium"
                :class="p.status === 'paid' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'"
              >
                {{ p.status }}
              </span>
            </td>
            <td class="px-3 py-2">{{ p.created_at }}</td>
            <td class="px-3 py-2 text-right">
              <Link :href="route('student.payments.receipt', p.id)" class="text-indigo-600 hover:underline">View</Link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
