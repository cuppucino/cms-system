<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import {
    Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow,
} from '@/components/ui/table'
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
import { Rocket, Plus, Search, Edit3, Trash2, ShieldAlert } from 'lucide-vue-next'
import type { BreadcrumbItem } from '@/types'
import { computed, ref } from 'vue'

interface Stock { id: number; size: string; total: number; issued: number; available: number }
interface Props { stock: Stock[] }

const props = defineProps<Props>()
const page = usePage()

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Gown Management', href: '/admin/gowns' }]

const handleDelete = (id: number) => {
    if (confirm('Are you sure you want to delete this stock?')) {
        router.delete(route('admin.gowns.destroy', { gown: id }), { preserveScroll: true })
    }
}

// ---------- derived totals ----------
const totals = computed(() => {
    const t = { total: 0, issued: 0, available: 0 }
    for (const s of props.stock || []) {
        t.total += Number(s.total || 0)
        t.issued += Number(s.issued || 0)
        t.available += Number(s.available || 0)
    }
    return t
})

const pct = (num: number, den: number) => (den > 0 ? Math.round((num / den) * 100) : 0)

// ---------- filters ----------
const q = ref('')
const filtered = computed(() => {
    const needle = q.value.trim().toLowerCase()
    if (!needle) return props.stock || []
    return (props.stock || []).filter(s => `${s.size}`.toLowerCase().includes(needle))
})

const lowThreshold = 10 // show low badge if available < 10
</script>

<template>

    <Head title="Gown Management" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4 p-4">
            <!-- Flash Notification -->
            <div v-if="page.props.flash?.message" class="rounded-xl border border-indigo-200 bg-indigo-50 p-4">
                <Alert class="flex items-start gap-3 p-0">
                    <Rocket class="mt-0.5 h-5 w-5 shrink-0 text-indigo-600" />
                    <div>
                        <AlertTitle class="text-sm font-semibold text-indigo-900">Notification</AlertTitle>
                        <AlertDescription class="text-sm text-indigo-800">{{ page.props.flash.message }}
                        </AlertDescription>
                    </div>
                </Alert>
            </div>

            <!-- Header & Actions -->
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h1 class="text-xl font-semibold text-gray-900">Gown Stock</h1>
                <Link :href="route('admin.gowns.create')">
                <Button class="inline-flex items-center gap-2 bg-indigo-600 text-white hover:bg-indigo-500">
                    <Plus class="h-4 w-4" /> Add Gown Stock
                </Button>
                </Link>
            </div>

            <!-- Summary cards -->
            <section class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Total</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-800">{{ totals.total.toLocaleString() }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Issued</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-800">{{ totals.issued.toLocaleString() }}</p>
                    <div class="mt-3 h-1 w-full overflow-hidden rounded-full bg-gray-200">
                        <div class="h-1 rounded-full bg-gradient-to-r from-emerald-500 to-indigo-600"
                            :style="{ width: pct(totals.issued, totals.total) + '%' }" />
                    </div>
                    <p class="mt-1 text-xs text-gray-600">{{ pct(totals.issued, totals.total) }}% issued</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500">Available</p>
                        <span v-if="totals.available < lowThreshold"
                            class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700">
                            <ShieldAlert class="h-3.5 w-3.5" /> Low
                        </span>
                    </div>
                    <p class="mt-1 text-2xl font-semibold text-gray-800">{{ totals.available.toLocaleString() }}</p>
                </div>
            </section>

            <!-- Filters -->
            <div class="grid grid-cols-1 gap-3 rounded-xl border border-gray-200 bg-white p-3 shadow-sm sm:grid-cols-3">
                <div class="relative sm:col-span-1">
                    <Search class="pointer-events-none absolute left-2 top-2.5 h-4 w-4 text-gray-400" />
                    <input v-model="q" type="text" placeholder="Filter by size (e.g., S, M, L)"
                        class="w-full rounded-md border border-gray-300 bg-white px-8 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" />
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <Table>
                    <TableCaption>All gown sizes and stock.</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[96px]">Size</TableHead>
                            <TableHead>Total</TableHead>
                            <TableHead>Issued</TableHead>
                            <TableHead>Available</TableHead>
                            <TableHead>Utilization</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="!filtered.length">
                            <TableCell colspan="6" class="py-10 text-center text-sm text-gray-500">No stock found.
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="s in filtered" :key="s.id" class="hover:bg-gray-50">
                            <TableCell class="font-medium text-gray-900">{{ s.size }}</TableCell>
                            <TableCell>{{ s.total }}</TableCell>
                            <TableCell>{{ s.issued }}</TableCell>
                            <TableCell>
                                <span
                                    :class="s.available < lowThreshold ? 'rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700' : ''">{{
                                    s.available }}</span>
                            </TableCell>
                            <TableCell>
                                <div class="mb-1 flex items-center justify-between text-xs text-gray-500">
                                    <span>{{ pct(s.issued, s.total) }}%</span>
                                    <span>{{ Math.max(0, s.total - s.issued) }} left</span>
                                </div>
                                <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-200">
                                    <div class="h-1.5 rounded-full bg-indigo-600"
                                        :style="{ width: pct(s.issued, s.total) + '%' }" />
                                </div>
                            </TableCell>
                            <TableCell class="text-right">
                                <div class="inline-flex items-center gap-1">
                                    <Link :href="route('admin.gowns.edit', { gown: s.id })">
                                    <Button variant="ghost" class="h-8 px-2 text-gray-700 hover:bg-gray-100">
                                        <Edit3 class="h-4 w-4" />
                                    </Button>
                                    </Link>
                                    <Button variant="ghost" class="h-8 px-2 text-red-600 hover:bg-red-50"
                                        @click="handleDelete(s.id)">
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Friendly, consistent with Users/Reports dashboards */
</style>
