<script setup lang="ts">
import StudentLayout from '@/layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Calendar, MapPin, Users, Clock } from 'lucide-vue-next'

defineOptions({ layout: StudentLayout })

const props = defineProps<{
    assigned: {
        id: number
        name: string
        date: string | null
        time: string | null
        location: string | null
        quota: number
        registered: number
        notes?: string | null
        arrival_at?: string | null
        dress_code?: string | null
    } | null
    available: Array<{
        id: number
        name: string
        date: string | null
        time: string | null
        location: string | null
        quota: number
        registered: number
    }>
}>()

const fmtDate = (d?: string | null) => d ? new Date(d).toLocaleDateString() : 'TBA'
const fmtTime = (t?: string | null) => t || 'TBA'
const pct = (num: number, den: number) => (den > 0 ? Math.round((num / den) * 100) : 0)
</script>

<template>

    <Head title="Your Session" />

    <!-- Assigned Session -->
    <div class="rounded-2xl border bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-xl font-semibold">Your Session</h1>
            <p class="text-xs text-gray-500">
                Want to change/choose a session? Go to
                <Link href="/student/registration" class="text-indigo-600 hover:underline">Registration</Link>.
            </p>
        </div>

        <div v-if="props.assigned" class="mt-6 grid gap-6 md:grid-cols-2">
            <!-- Big session card -->
            <div class="rounded-xl border p-6 md:p-8 bg-white dark:bg-gray-900 dark:border-gray-800">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-50">{{ props.assigned.name }}</h2>

                <div class="mt-4 space-y-3 text-[15px] text-gray-700 dark:text-gray-200">
                    <p class="flex items-center gap-2">
                        <Calendar class="h-5 w-5 text-gray-400" />
                        {{ fmtDate(props.assigned.date) }} • {{ fmtTime(props.assigned.time) }}
                    </p>
                    <p class="flex items-center gap-2">
                        <MapPin class="h-5 w-5 text-gray-400" />
                        {{ props.assigned.location || 'TBA' }}
                    </p>
                    <p class="flex items-center gap-2">
                        <Users class="h-5 w-5 text-gray-400" />
                        {{ props.assigned.registered }} / {{ props.assigned.quota }} seats filled
                    </p>
                </div>

                <!-- Utilization -->
                <div class="mt-6">
                    <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-800">
                        <div class="h-2 rounded-full bg-gradient-to-r from-indigo-500 to-violet-600 transition-all"
                            :style="{ width: pct(props.assigned.registered, props.assigned.quota) + '%' }" />
                    </div>
                    <p class="mt-2 text-right text-xs text-gray-600 dark:text-gray-300">
                        {{ pct(props.assigned.registered, props.assigned.quota) }}% filled
                    </p>
                </div>
            </div>

            <!-- Big metadata card -->
            <div class="rounded-xl border p-6 md:p-8 bg-white dark:bg-gray-900 dark:border-gray-800">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-50">Important Info</h3>
                <ul class="mt-4 space-y-3 text-[15px] text-gray-700 dark:text-gray-200">
                    <li class="flex items-center gap-2">
                        <Clock class="h-5 w-5 text-gray-400" />
                        Arrival: <span class="ml-1 font-medium">{{ props.assigned.arrival_at || '—' }}</span>
                    </li>
                    <li>
                        Dress code: <span class="font-medium">{{ props.assigned.dress_code || '—' }}</span>
                    </li>
                    <li v-if="props.assigned.notes">
                        Notes: <span class="font-medium">{{ props.assigned.notes }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- No session assigned -->
        <div v-else class="mt-6">
            <p class="text-sm text-gray-600 dark:text-gray-300">
                You haven’t been assigned a session yet. Below are available sessions for your reference.
                To choose one, please use the
                <Link href="/student/registration" class="text-indigo-600 hover:underline">Registration</Link>
                page.
            </p>

            <!-- Bigger cards, 1–2 per row -->
            <div class="mt-6 grid gap-6 sm:grid-cols-2">
                <div v-for="s in props.available" :key="s.id"
                    class="rounded-2xl border bg-white p-6 md:p-8 shadow-sm transition hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-50">{{ s.name }}</h3>

                    <div class="mt-4 space-y-3 text-[15px] text-gray-700 dark:text-gray-200">
                        <p class="flex items-center gap-2">
                            <Calendar class="h-5 w-5 text-gray-400" />
                            {{ fmtDate(s.date) }} • {{ fmtTime(s.time) }}
                        </p>
                        <p class="flex items-center gap-2">
                            <MapPin class="h-5 w-5 text-gray-400" />
                            {{ s.location || 'TBA' }}
                        </p>
                        <p class="flex items-center gap-2">
                            <Users class="h-5 w-5 text-gray-400" />
                            {{ s.registered }} / {{ s.quota }} filled
                        </p>
                    </div>

                    <!-- Mini utilization, still roomy -->
                    <div class="mt-6">
                        <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-800">
                            <div class="h-2 rounded-full bg-indigo-500"
                                :style="{ width: pct(s.registered, s.quota) + '%' }" />
                        </div>
                        <p class="mt-2 text-right text-xs text-gray-600 dark:text-gray-300">
                            {{ pct(s.registered, s.quota) }}% filled
                        </p>
                    </div>

                    <!-- No buttons on purpose (view-only) -->
                    <p class="mt-4 text-xs text-gray-500">
                        To request or change a session, go to
                        <Link href="/student/registration" class="text-indigo-600 hover:underline">Registration</Link>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
