<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import {
  Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow,
} from '@/components/ui/table'
import Button from '@/components/ui/button/Button.vue'
import { CreditCard, Download, Search } from 'lucide-vue-next'
import { computed, reactive, ref, watch } from 'vue'

const props = defineProps<{
  payments: {
    id: number
    reference: string
    student: string
    gown: string
    amount: number
    status: string
    created_at: string
  }[]
  pagination: { current_page: number; last_page: number; per_page: number; total: number }
  filters: { search?: string; status?: string }
}>()

// -------- filters (synced with server) --------
const f = reactive<{ search: string; status: '' | 'paid' | 'pending' | 'failed' }>({
  search: props.filters?.search ?? '',
  status: (props.filters?.status as any) ?? '',
})
const typing = ref<ReturnType<typeof setTimeout> | null>(null)
const apply = (page?: number) => {
  const params: Record<string, any> = { search: f.search || undefined, status: f.status || undefined, page }
  router.get(route('admin.payments.index'), params, { preserveState: true, preserveScroll: true, replace: true })
}
watch(() => f.search, () => { if (typing.value) clearTimeout(typing.value); typing.value = setTimeout(() => apply(1), 350) })
watch(() => f.status, () => apply(1))

// -------- derived summary --------
const totals = computed(() => {
  const sum = props.payments?.reduce((acc, p) => acc + (Number(p.amount) || 0), 0) || 0
  const paid = props.payments?.filter(p => p.status === 'paid').length || 0
  const failed = props.payments?.filter(p => p.status !== 'paid').length || 0
  return { sum, paid, failed, count: props.payments?.length || 0 }
})
const rm = (n: number) =>
  `RM ${Number(n || 0).toLocaleString('en-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`

// -------- pagination helpers --------
const canPrev = computed(() => props.pagination.current_page > 1)
const canNext = computed(() => props.pagination.current_page < props.pagination.last_page)
const go = (page: number) => apply(page)
</script>

<template>
  <Head title="Payments" />

  <AppLayout :breadcrumbs="[{ title: 'Payments', href: route('admin.payments.index') }]">
    <div class="space-y-4 p-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <h2 class="flex items-center gap-2 text-xl font-semibold text-gray-900">
          <CreditCard class="h-5 w-5 text-indigo-600" /> Payment Records
        </h2>
        <!-- Optional CSV export
        <Link :href="route('admin.payments.export')">
          <Button class="inline-flex items-center gap-2 bg-indigo-600 text-white hover:bg-indigo-500">
            <Download class="h-4 w-4" /> Export CSV
          </Button>
        </Link>
        -->
      </div>

      <!-- Filters -->
      <div class="grid grid-cols-1 gap-3 rounded-xl border border-gray-200 bg-white p-3 shadow-sm sm:grid-cols-3">
        <div class="relative sm:col-span-2">
          <Search class="pointer-events-none absolute left-2 top-2.5 h-4 w-4 text-gray-400" />
          <input
            v-model="f.search"
            type="text"
            placeholder="Search reference, student name or email"
            class="w-full rounded-md border border-gray-300 bg-white px-8 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
          />
        </div>
        <div>
          <select
            v-model="f.status"
            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
          >
            <option value="">All statuses</option>
            <option value="paid">Paid</option>
            <option value="pending">Pending</option>
            <option value="failed">Failed</option>
          </select>
        </div>
      </div>

      <!-- Summary cards -->
      <section class="grid grid-cols-1 gap-3 sm:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
          <p class="text-sm text-gray-500">Total Amount (page)</p>
          <p class="mt-1 text-2xl font-semibold text-gray-800">{{ rm(totals.sum) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
          <p class="text-sm text-gray-500">Payments (page)</p>
          <p class="mt-1 text-2xl font-semibold text-gray-800">{{ totals.count }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
          <p class="text-sm text-gray-500">Paid (page)</p>
          <p class="mt-1 text-2xl font-semibold text-emerald-700">{{ totals.paid }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
          <p class="text-sm text-gray-500">Unpaid / Failed (page)</p>
          <p class="mt-1 text-2xl font-semibold text-amber-700">{{ totals.failed }}</p>
        </div>
      </section>

      <!-- Table -->
      <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <Table>
          <TableCaption>All gown payments by students</TableCaption>
          <TableHeader>
            <TableRow>
              <TableHead class="w-[72px]">ID</TableHead>
              <TableHead>Reference</TableHead>
              <TableHead>Student</TableHead>
              <TableHead>Gown Size</TableHead>
              <TableHead class="text-right">Amount</TableHead>
              <TableHead>Status</TableHead>
              <TableHead>Date</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-if="!props.payments?.length">
              <TableCell colspan="7" class="py-10 text-center text-sm text-gray-500">
                No payment records.
              </TableCell>
            </TableRow>
            <TableRow v-for="p in props.payments" :key="p.id" class="hover:bg-gray-50">
              <TableCell class="text-gray-500">{{ p.id }}</TableCell>
              <TableCell class="font-medium text-gray-900">{{ p.reference }}</TableCell>
              <TableCell>{{ p.student }}</TableCell>
              <TableCell>{{ p.gown }}</TableCell>
              <TableCell class="tabular-nums text-right">{{ rm(p.amount) }}</TableCell>
              <TableCell>
                <span
                  v-if="p.status === 'paid'"
                  class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700"
                  >Paid</span
                >
                <span
                  v-else
                  class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700"
                  >{{ p.status }}</span
                >
              </TableCell>
              <TableCell>{{ p.created_at }}</TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>

      <!-- Pagination -->
      <div class="flex items-center justify-between gap-3 rounded-xl border border-gray-200 bg-white p-3 text-sm shadow-sm">
        <div class="text-gray-600">
          Page <span class="font-medium">{{ pagination.current_page }}</span> of
          <span class="font-medium">{{ pagination.last_page }}</span> •
          <span class="font-medium">{{ pagination.total }}</span> total
        </div>
        <div class="flex items-center gap-2">
          <Button variant="ghost" class="h-8 px-3" :disabled="!canPrev" @click="go(pagination.current_page - 1)">
            Previous
          </Button>
          <Button variant="ghost" class="h-8 px-3" :disabled="!canNext" @click="go(pagination.current_page + 1)">
            Next
          </Button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Right-align currency with tabular nums. Summary cards match the admin theme. */
</style>
