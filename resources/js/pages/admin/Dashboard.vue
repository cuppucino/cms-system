<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'

type StatMap = Record<string, number>

defineProps<{
  stats: StatMap
  recentRegistrations: Array<any>
  recentPayments: Array<any>
  notifications: Array<any>
}>()

const breadcrumbs = [{ title: 'Dashboard', href: '/dashboard' }]

// helpers
const fmtInt = (n: number) => (Number.isFinite(n) ? n.toLocaleString() : '0')
const fmtRM = (n: number) => (Number.isFinite(n) ? `RM ${n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : 'RM 0.00')
const fmtDate = (d?: string | number | Date) => (d ? new Date(d).toLocaleDateString() : '-')

const statPairs = computed(() => Object.entries((__props as any).stats || {}))
</script>

<template>
  <Head title="Admin Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-6 p-6">
      <!-- Page Title -->
      <div class="flex items-center justify-between">
        <h1 class="flex items-center gap-3 text-2xl font-bold text-gray-900">
          <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">🎓</span>
          Admin Dashboard
        </h1>
        <div class="flex items-center gap-2">
          <Link href="/admin/reports" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Reports</Link>
          <Link href="/admin/settings" class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">Settings</Link>
        </div>
      </div>

      <!-- Stats Cards -->
      <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
        <div v-for="(pair, idx) in statPairs" :key="pair?.[0] ?? idx" class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm hover:shadow-md">
          <p class="text-sm capitalize text-gray-500">{{ pair?.[0] ?? 'metric' }}</p>
          <p class="mt-1 text-2xl font-semibold text-gray-800">{{ fmtInt(Number(pair?.[1] ?? 0)) }}</p>
          <div class="mt-3 h-1 w-full overflow-hidden rounded-full bg-gray-200">
            <div class="h-1 rounded-full bg-gradient-to-r from-emerald-500 to-indigo-600" :style="{ width: Math.min(100, Number(pair?.[1] ?? 0)) + '%' }" />
          </div>
        </div>
      </section>

      <!-- Quick Actions -->
      <section class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <Link href="/admin/sessions" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-3 py-3 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">New Session</Link>
        <Link href="/admin/invitations" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-3 py-3 text-sm font-medium text-white shadow-sm hover:bg-emerald-500">Send Invitations</Link>
        <Link href="/admin/gowns" class="inline-flex items-center justify-center rounded-lg bg-purple-600 px-3 py-3 text-sm font-medium text-white shadow-sm hover:bg-purple-500">Manage Gowns</Link>
        <Link href="/admin/reports" class="inline-flex items-center justify-center rounded-lg bg-amber-500 px-3 py-3 text-sm font-medium text-white shadow-sm hover:bg-amber-400">View Reports</Link>
      </section>

      <!-- Recent Activity -->
      <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Recent Registrations -->
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
          <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
            <h2 class="text-base font-semibold text-gray-800">Recent Registrations</h2>
            <Link href="/admin/registrations" class="text-sm font-medium text-indigo-600 hover:underline">View all</Link>
          </div>
          <div class="max-h-[360px] overflow-auto">
            <table class="w-full text-left text-sm">
              <thead class="sticky top-0 bg-indigo-50 text-gray-700">
                <tr>
                  <th class="px-4 py-2 font-medium">Student</th>
                  <th class="px-4 py-2 font-medium">Session</th>
                  <th class="px-4 py-2 font-medium">Date</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-if="!recentRegistrations?.length">
                  <td colspan="3" class="px-4 py-6 text-center text-gray-500">No registrations yet.</td>
                </tr>
                <tr v-for="(r, i) in recentRegistrations" :key="r?.id ?? i" class="hover:bg-gray-50">
                  <td class="px-4 py-2">{{ r?.user?.name ?? '—' }}</td>
                  <td class="px-4 py-2">{{ r?.session?.name ?? '—' }}</td>
                  <td class="px-4 py-2">{{ fmtDate(r?.created_at) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Recent Payments -->
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
          <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
            <h2 class="text-base font-semibold text-gray-800">Recent Payments</h2>
            <Link href="/admin/payments" class="text-sm font-medium text-indigo-600 hover:underline">View all</Link>
          </div>
          <ul class="max-h-[360px] divide-y divide-gray-100 overflow-auto">
            <li v-if="!recentPayments?.length" class="px-6 py-6 text-center text-gray-500">No payments yet.</li>
            <li v-for="(p, i) in recentPayments" :key="p?.id ?? i" class="flex items-center justify-between px-6 py-3 hover:bg-gray-50">
              <span class="truncate text-sm text-gray-800">{{ p?.user?.name ?? '—' }}</span>
              <span class="text-sm font-semibold text-emerald-600">{{ fmtRM(Number(p?.amount ?? 0)) }}</span>
            </li>
          </ul>
        </div>
      </section>

      <!-- Charts / Reports -->
      <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="flex min-h-[300px] items-center justify-center rounded-xl border border-gray-200 bg-white p-6 text-gray-400 shadow-sm">
          <div class="text-center">
            <div class="mb-2 text-xl">📊</div>
            <p class="text-sm">Registrations Trend (placeholder)</p>
          </div>
        </div>
        <div class="flex min-h-[300px] items-center justify-center rounded-xl border border-gray-200 bg-white p-6 text-gray-400 shadow-sm">
          <div class="text-center">
            <div class="mb-2 text-xl">📈</div>
            <p class="text-sm">Payments Overview (placeholder)</p>
          </div>
        </div>
      </section>

      <!-- Notifications -->
      <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
          <h3 class="text-base font-semibold text-gray-800">Notifications</h3>
          <Link href="/admin/notifications" class="text-sm font-medium text-indigo-600 hover:underline">View all</Link>
        </div>
        <ul class="max-h-[280px] divide-y divide-gray-100 overflow-auto">
          <li v-if="!notifications?.length" class="px-6 py-6 text-center text-gray-500">You're all caught up.</li>
          <li v-for="(n, i) in notifications" :key="n?.id ?? i" class="flex items-center justify-between px-6 py-3 hover:bg-gray-50">
            <span class="truncate text-sm text-gray-700">{{ n?.message ?? '—' }}</span>
            <span class="text-xs text-gray-400">{{ fmtDate(n?.created_at) }}</span>
          </li>
        </ul>
      </section>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Light, friendly palette consistent with Reports & Student pages */
</style>
