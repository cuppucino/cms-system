<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, usePage, Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps<{
    attendance: { total_registered: number; total_checked_in: number }
    gowns: { size: string; issued: number; returned: number; outstanding: number }[]
    guests: { session: { name: string; guest_quota: number }; total_guests: number }[]
    guestRegistrations: { student_name: string; session_name: string; guest_count: number }[]
    sessions: { name: string; quota: number; registered: number }[]
}>()

const page = usePage()

const pct = (num: number, den: number) => (den > 0 ? Math.round((num / den) * 100) : 0)
const fmt = (n: number) => n.toLocaleString()

const totalRegistered = computed(() => props.attendance.total_registered || 0)
const totalCheckedIn = computed(() => props.attendance.total_checked_in || 0)
const checkInRate = computed(() => pct(totalCheckedIn.value, totalRegistered.value))

const gownsTotals = computed(() => {
    const total = props.gowns?.reduce((s, g) => s + (g.total || 0), 0) || 0
    const returned = props.gowns?.reduce((s, g) => s + (g.returned || 0), 0) || 0
    const outstanding = total - returned
    const returnRate = pct(returned, total)
    return { total, returned, outstanding, returnRate }
})

const totalGuests = computed(() => props.guests?.reduce((s, g) => s + (g?.total_guests || 0), 0) || 0)

const sessionsMeta = computed(() => {
    const quota = props.sessions?.reduce((s, r) => s + (r.quota || 0), 0) || 0
    const registered = props.sessions?.reduce((s, r) => s + (r.registered || 0), 0) || 0
    const fillRate = pct(registered, quota)
    const remaining = quota - registered
    return { quota, registered, fillRate, remaining }
})
</script>

<template>
    <Head title="Reports & Analytics" />
    <AppLayout :breadcrumbs="[{ title: 'Reports', href: route('admin.reports.index') }]">
        <div class="space-y-6 p-4">
            <div class="mb-2 flex items-start justify-between gap-4">
                <h1 class="text-2xl font-bold tracking-tight">Reports & Analytics</h1>
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <select id="exportKind" v-model="(page.props as any).exportKind"
                            class="rounded-l-lg border border-gray-300 bg-white px-2 py-2 text-sm">
                            <option value="attendance">Attendance</option>
                            <option value="registrations">Registrations</option>
                            <option value="gowns">Gowns</option>
                            <option value="guests">Guests</option>
                            <option value="sessions">Sessions</option>
                        </select>
                        <Link :href="route('admin.reports.export', (page.props as any).exportKind || 'attendance')"
                            class="inline-flex items-center rounded-r-lg border border-l-0 border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                            Export CSV
                        </Link>
                    </div>
                    <button type="button" onclick="window.print()"
                        class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">
                        Print
                    </button>
                </div>
            </div>

            <section aria-labelledby="kpis" class="mb-2">
                <h2 id="kpis" class="sr-only">Key performance indicators</h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-xl border bg-white p-4 shadow hover:shadow-md">
                        <p class="text-sm text-gray-500">Total Registered</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-800">{{ fmt(totalRegistered) }}</p>
                    </div>
                    <div class="rounded-xl border bg-white p-4 shadow hover:shadow-md">
                        <p class="text-sm text-gray-500">Checked In</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-800">{{ fmt(totalCheckedIn) }}</p>
                        <div class="mt-3">
                            <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200">
                                <div class="h-2 rounded-full bg-green-500" :style="{ width: checkInRate + '%' }" />
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Check-in rate: <span class="font-medium text-gray-700">{{ checkInRate }}%</span>
                            </p>
                        </div>
                    </div>
                    <div class="rounded-xl border bg-white p-4 shadow hover:shadow-md">
                        <p class="text-sm text-gray-500">Guest Passes</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-800">{{ fmt(totalGuests) }}</p>
                    </div>
                    <div class="rounded-xl border bg-white p-4 shadow hover:shadow-md">
                        <p class="text-sm text-gray-500">Seat Utilization</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-800">{{ sessionsMeta.fillRate }}%</p>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ fmt(sessionsMeta.registered) }} / {{ fmt(sessionsMeta.quota) }} seats filled
                        </p>
                    </div>
                </div>
            </section>

            <section class="mb-2">
                <h2 class="mb-2 text-lg font-semibold text-gray-800">Gown Report</h2>
                <div class="overflow-hidden rounded-xl border bg-white shadow">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-indigo-50 text-gray-700">
                            <tr>
                                <th class="px-3 py-2 font-medium">Size</th>
                                <th class="px-3 py-2 font-medium">Issued</th>
                                <th class="px-3 py-2 font-medium">Returned</th>
                                <th class="px-3 py-2 font-medium">Outstanding</th>
                                <th class="px-3 py-2 font-medium">Return %</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="g in props.gowns" :key="g.size" class="hover:bg-gray-50">
                                <td class="px-3 py-2">{{ g.size }}</td>
                                <td class="px-3 py-2">{{ fmt(g.issued) }}</td>
                                <td class="px-3 py-2">{{ fmt(g.returned) }}</td>
                                <td class="px-3 py-2">{{ fmt(g.outstanding) }}</td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200">
                                            <div class="h-2 rounded-full bg-indigo-500"
                                                :style="{ width: pct(g.returned, g.issued || 0) + '%' }" />
                                        </div>
                                        <span>{{ pct(g.returned, g.issued || 0) }}%</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="mb-2">
                <h2 class="mb-2 text-lg font-semibold text-gray-800">Guest Passes</h2>
                <div class="overflow-hidden rounded-xl border bg-white shadow">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-indigo-50 text-gray-700">
                            <tr>
                                <th class="px-3 py-2 font-medium">Session</th>
                                <th class="px-3 py-2 font-medium">Total Guests</th>
                                <th class="px-3 py-2 font-medium">Guest Quota</th>
                                <th class="px-3 py-2 font-medium">Remaining</th>
                                <th class="px-3 py-2 font-medium">Utilization</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="(g, idx) in props.guests" :key="g?.session?.name ?? idx"
                                class="hover:bg-gray-50">
                                <td class="px-3 py-2">{{ g?.session?.name ?? 'Unknown' }}</td>
                                <td class="px-3 py-2">{{ fmt(g?.total_guests || 0) }}</td>
                                <td class="px-3 py-2">{{ fmt(g?.session?.guest_quota || 0) }}</td>
                                <td class="px-3 py-2">{{ fmt((g?.session?.guest_quota || 0) - (g?.total_guests || 0)) }}</td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200">
                                            <div class="h-2 rounded-full bg-indigo-500"
                                                :style="{ width: pct(g?.total_guests || 0, g?.session?.guest_quota || 1) + '%' }" />
                                        </div>
                                        <span>{{ pct(g?.total_guests || 0, g?.session?.guest_quota || 1) }}%</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <h3 class="mt-4 mb-2 text-md font-semibold text-gray-800">Guest Registrations by Student</h3>
                <div class="overflow-hidden rounded-xl border bg-white shadow">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-indigo-50 text-gray-700">
                            <tr>
                                <th class="px-3 py-2 font-medium">Student Name</th>
                                <th class="px-3 py-2 font-medium">Session</th>
                                <th class="px-3 py-2 font-medium">Guests</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="(r, idx) in props.guestRegistrations" :key="idx" class="hover:bg-gray-50">
                                <td class="px-3 py-2">{{ r.student_name }}</td>
                                <td class="px-3 py-2">{{ r.session_name }}</td>
                                <td class="px-3 py-2">{{ r.guest_count }}</td>
                            </tr>
                            <tr v-if="!props.guestRegistrations.length" class="hover:bg-gray-50">
                                <td colspan="3" class="px-3 py-2 text-center text-gray-500">No guest registrations</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section>
                <h2 class="mb-2 text-lg font-semibold text-gray-800">Session Report</h2>
                <div class="overflow-hidden rounded-xl border bg-white shadow">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-indigo-50 text-gray-700">
                            <tr>
                                <th class="px-3 py-2 font-medium">Name</th>
                                <th class="px-3 py-2 font-medium">Quota</th>
                                <th class="px-3 py-2 font-medium">Registered</th>
                                <th class="px-3 py-2 font-medium">Utilization</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="s in props.sessions" :key="s.name" class="hover:bg-gray-50">
                                <td class="px-3 py-2">{{ s.name }}</td>
                                <td class="px-3 py-2">{{ fmt(s.quota) }}</td>
                                <td class="px-3 py-2">{{ fmt(s.registered) }}</td>
                                <td class="px-3 py-2">
                                    <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200">
                                        <div class="h-2 rounded-full bg-green-500"
                                            :style="{ width: pct(s.registered, s.quota) + '%' }" />
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">{{ pct(s.registered, s.quota) }}%</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
