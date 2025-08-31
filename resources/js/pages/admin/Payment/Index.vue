<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import Button from '@/components/ui/button/Button.vue'
import { CreditCard, Download } from 'lucide-vue-next'
import { computed } from 'vue'

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
}>()

// --- derived summary ---
const totals = computed(() => {
    const sum = props.payments?.reduce((acc, p) => acc + (Number(p.amount) || 0), 0) || 0
    const paid = props.payments?.filter(p => p.status === 'paid').length || 0
    const failed = props.payments?.filter(p => p.status !== 'paid').length || 0
    return { sum, paid, failed, count: props.payments?.length || 0 }
})

const rm = (n: number) => `RM ${Number(n || 0).toLocaleString('en-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
</script>

<template>

    <Head title="Payments" />

    <AppLayout :breadcrumbs="[{ title: 'Payments', href: '#' }]">
        <div class="space-y-4 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                    <CreditCard class="h-5 w-5 text-indigo-600" /> Payment Records
                </h2>
                <!-- <Link :href="route('admin.payments.export')">
                <Button class="inline-flex items-center gap-2 bg-indigo-600 text-white hover:bg-indigo-500">
                    <Download class="h-4 w-4" /> Export CSV
                </Button>
                </Link> -->
            </div>

            <!-- Summary cards -->
            <section class="grid grid-cols-1 gap-3 sm:grid-cols-4">
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Total Amount</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-800">{{ rm(totals.sum) }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Payments</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-800">{{ totals.count }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Paid</p>
                    <p class="mt-1 text-2xl font-semibold text-emerald-700">{{ totals.paid }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Unpaid / Failed</p>
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
                            <TableCell colspan="7" class="py-10 text-center text-sm text-gray-500">No payment records.
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="p in props.payments" :key="p.id" class="hover:bg-gray-50">
                            <TableCell class="text-gray-500">{{ p.id }}</TableCell>
                            <TableCell class="font-medium text-gray-900">{{ p.reference }}</TableCell>
                            <TableCell>{{ p.student }}</TableCell>
                            <TableCell>{{ p.gown }}</TableCell>
                            <TableCell class="text-right tabular-nums">{{ rm(p.amount) }}</TableCell>
                            <TableCell>
                                <span v-if="p.status === 'paid'"
                                    class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700">Paid</span>
                                <span v-else
                                    class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700">{{
                                    p.status }}</span>
                            </TableCell>
                            <TableCell>{{ p.created_at }}</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Right-align currency with tabular nums. Summary cards match the admin theme. */
</style>
