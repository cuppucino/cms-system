<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import StudentLayout from '@/layouts/StudentLayout.vue'
import { Calendar, MapPin, GraduationCap, QrCode, Bell, CheckCircle2, Clock } from 'lucide-vue-next'

defineOptions({ layout: StudentLayout })

// ---------- safe inputs ----------
const page = usePage()
const user = computed(() => (page.props.auth as any)?.user ?? {})
const summary = (page.props.studentSummary as any) ?? { status: user.status }

// Normalized bits (all optional-safe)
const session = summary.session || {}
const gown = summary.gown || {}
const invitation = summary.invitation || {}
const attended = !!summary.attended

// ---------- progress steps ----------
const steps = computed(() => ([
    { key: 'registered', label: 'Register', done: summary.status !== 'pending', current: summary.status === 'pending' },
    { key: 'confirmed', label: 'Confirm Attendance', done: summary.status === 'confirmed', current: summary.status === 'registered' },
    { key: 'gown', label: 'Gown', done: !!gown.size || !!gown.collected_at, current: !gown.size && summary.status !== 'pending' },
    { key: 'invitation', label: 'Invitation', done: !!invitation.code || !!invitation.qr, current: !invitation.code && summary.status === 'confirmed' },
    { key: 'attend', label: 'Attend', done: attended, current: !attended && !!invitation.code },
]))

const progressPct = computed(() => {
    const d = steps.value.filter(s => s.done).length
    return Math.round((d / steps.value.length) * 100)
})

// Small helpers
const badgeClass = (s: string) => ({
    pending: 'bg-yellow-50 text-yellow-700',
    registered: 'bg-blue-50 text-blue-700',
    confirmed: 'bg-green-50 text-green-700',
}[s] || 'bg-gray-50 text-gray-600')

const fmtDate = (d?: string) => (d ? new Date(d).toLocaleString() : '—')
</script>

<template>

    <Head title="Student Dashboard" />

    <!-- Greeting -->
    <div class="rounded-xl border bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <h1 class="text-lg font-semibold">Welcome, {{ user.name }}</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
            Track your convocation progress and complete any pending actions below.
        </p>
    </div>

    <!-- Journey -->
    <div class="rounded-xl border bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <h2 class="mb-4 text-sm font-medium text-gray-700 dark:text-gray-200">Your Journey</h2>

        <div class="relative flex items-start justify-between gap-2">
            <div v-for="(s, idx) in steps" :key="s.key" class="relative flex-1">
                <!-- Connector line (only between steps) -->
                <div v-if="idx < steps.length - 1" class="absolute left-1/2 top-5 h-px w-full -translate-x-1/2"
                    :class="s.done ? 'bg-indigo-500/80' : 'bg-gray-300 dark:bg-gray-700'"></div>

                <!-- Node (refined) -->
                <div class="relative z-10 flex flex-col items-center text-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 text-sm font-semibold transition-all"
                        :class="{
                            'bg-gradient-to-r from-indigo-500 to-violet-600 border-indigo-600 text-white shadow-md': s.done,
                            'border-indigo-500 bg-white text-indigo-600': s.current && !s.done,
                            'border-gray-300 bg-white text-gray-400 dark:border-gray-700': !s.done && !s.current
                        }">
                        {{ idx + 1 }}
                    </div>
                    <span class="mt-2 text-xs font-medium text-gray-700 dark:text-gray-200">{{ s.label }}</span>
                </div>
            </div>
        </div>

        <!-- Sleek gradient progress bar -->
        <div class="mt-6">
            <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-800">
                <div class="h-1.5 rounded-full bg-gradient-to-r from-indigo-500 to-violet-600 transition-all duration-500 ease-out shadow-[inset_0_0_0_1px_rgba(0,0,0,0.02)]"
                    :style="{ width: progressPct + '%' }" />
            </div>
            <p class="mt-2 text-right text-sm text-gray-600 dark:text-gray-300">
                Progress: <span class="font-semibold text-indigo-600">{{ progressPct }}%</span>
            </p>
        </div>
    </div>

    <!-- Quick status cards -->
    <div class="grid gap-4 md:grid-cols-3">
        <!-- Registration -->
        <div class="rounded-xl border bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold">Registration</h3>
                <CheckCircle2 v-if="summary.status === 'confirmed'" class="h-4 w-4 text-green-600" />
                <Clock v-else class="h-4 w-4 text-gray-400" />
            </div>
            <p class="mt-1 text-sm">
                Status:
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium capitalize"
                    :class="badgeClass(summary.status)">
                    {{ summary.status || 'pending' }}
                </span>
            </p>
            <div class="mt-3">
                <Link href="/student/registration"
                    class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-500">
                Open
                </Link>
            </div>
        </div>

        <!-- Session -->
        <div class="rounded-xl border bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-sm font-semibold">Session</h3>
            <p class="mt-2 flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                <Calendar class="h-4 w-4 text-gray-400" />
                {{ session.date ? new Date(session.date).toLocaleDateString() : 'TBA' }}
            </p>
            <p class="mt-1 flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                <MapPin class="h-4 w-4 text-gray-400" />
                {{ session.location || 'TBA' }}
            </p>
            <div class="mt-3">
                <Link href="/student/session"
                    class="inline-flex items-center rounded-lg border px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-800">
                View
                </Link>
            </div>
        </div>

        <!-- Gown -->
        <div class="rounded-xl border bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-sm font-semibold">Gown</h3>
            <p class="mt-2 flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                <GraduationCap class="h-4 w-4 text-gray-400" />
                Size: <span class="ml-1 font-medium">{{ gown.size || 'Not selected' }}</span>
            </p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Collected: {{ fmtDate(gown.collected_at) }} • Returned: {{ fmtDate(gown.returned_at) }}
            </p>
            <div class="mt-3">
                <Link href="/student/gown"
                    class="inline-flex items-center rounded-lg border px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-800">
                Manage
                </Link>
            </div>
        </div>
    </div>

    <!-- Next Actions -->
    <div class="rounded-xl border bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <h3 class="text-base font-semibold">Next Action</h3>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
            Complete your registration first. After that, you can download your invitation and make payment if required.
        </p>

        <div class="mt-4 grid gap-3 sm:grid-cols-3">
            <!-- Invitation -->
            <Link href="/student/invitation"
                class="flex items-center justify-between rounded-lg border px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-800">
            <span class="inline-flex items-center gap-2">
                <QrCode class="h-4 w-4 text-gray-400" /> Invitation
            </span>
            <span class="text-xs text-gray-500">{{ invitation.code ? 'Ready' : 'Pending' }}</span>
            </Link>

            <!-- Payments -->
            <Link href="/student/payments"
                class="flex items-center justify-between rounded-lg border px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-800">
            <span class="inline-flex items-center gap-2"> 💳 Payments </span>
            <span class="text-xs text-gray-500">{{ summary.payment_status || '—' }}</span>
            </Link>

            <!-- Notifications -->
            <Link :href="route('student.notifications.index')"
                class="flex items-center justify-between rounded-lg border px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-800">
            <span class="inline-flex items-center gap-2">
                <Bell class="h-4 w-4 text-gray-400" /> Notifications
            </span>
            <span class="text-xs text-gray-500">
                {{ (user as any).unread_notifications ?? 0 }} unread
            </span>
            </Link>
        </div>
    </div>
</template>
