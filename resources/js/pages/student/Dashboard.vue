<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import StudentLayout from '@/layouts/StudentLayout.vue'
defineOptions({ layout: StudentLayout })

const page = usePage()
const user = page.props.auth?.user ?? { status: 'pending' }

// Optional server summary (safe fallback)
const summary = (page.props.studentSummary as any) ?? { status: user.status }

// Dynamic progress steps
const steps = computed(() => ([
    {
        key: 'registered', label: 'Register',
        done: summary.status !== 'pending',
        current: summary.status === 'pending'
    },
    {
        key: 'confirmed', label: 'Confirm Attendance',
        done: summary.status === 'confirmed',
        current: summary.status === 'registered'
    },
    {
        key: 'gown', label: 'Gown',
        done: !!summary.gown,
        current: !summary.gown && summary.status !== 'pending'
    },
    {
        key: 'invitation', label: 'Invitation',
        done: !!summary.invitation,
        current: !summary.invitation && summary.status === 'confirmed'
    },
    {
        key: 'attend', label: 'Attend',
        done: !!summary.attended,
        current: !summary.attended && !!summary.invitation
    },
]))
</script>

<template>

    <Head title="Student Dashboard" />

    <!-- Greeting -->
    <div class="rounded-xl border bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h1 class="text-lg font-semibold">Welcome, {{ user.name }}</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
            Track your convocation progress and complete any pending actions below.
        </p>
    </div>

    <!-- Progress -->
    <div class="rounded-xl border bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <h2 class="mb-4 text-sm font-medium text-gray-700 dark:text-gray-200">Your Journey</h2>

        <!-- Step line -->
        <div class="relative flex items-center justify-between">
            <div v-for="(s, idx) in steps" :key="s.key" class="flex-1 relative">
                <!-- Connector line -->
                <div v-if="idx < steps.length - 0" class="absolute top-4 left-1/2 h-0.5 w-full -translate-x-1/2"
                    :class="s.done ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-700'">
                </div>

                <!-- Step circle -->
                <div class="relative z-10 flex flex-col items-center">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full border-2" :class="{
                        'bg-blue-600 border-blue-600 text-white': s.done,
                        'border-blue-600 bg-white text-blue-600': s.current,
                        'border-gray-300 bg-white text-gray-400 dark:border-gray-700': !s.done && !s.current
                    }">
                        <span class="text-xs font-medium">{{ idx + 1 }}</span>
                    </div>
                    <span class="mt-2 text-xs font-medium">{{ s.label }}</span>
                </div>
            </div>
        </div>

        <!-- Progress percentage -->
        <div class="mt-4 text-right text-sm text-gray-600 dark:text-gray-300">
            Progress:
            <span class="font-semibold text-blue-600">
                {{Math.round((steps.filter(s => s.done).length / steps.length) * 100)}}%
            </span>
        </div>
    </div>
    <!-- Status cards -->
    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-sm font-semibold">Registration</h3>
            <p class="mt-1 text-sm">
                Status:
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium capitalize" :class="{
                    'bg-yellow-50 text-yellow-700': summary.status === 'pending',
                    'bg-blue-50 text-blue-700': summary.status === 'registered',
                    'bg-green-50 text-green-700': summary.status === 'confirmed',
                }">
                    {{ summary.status }}
                </span>
            </p>
            <div class="mt-3">
                <Link href="/student/registration"
                    class="rounded-lg bg-blue-600 px-3 py-2 text-sm text-white hover:bg-blue-700">Open</Link>
            </div>
        </div>

        <div class="rounded-xl border bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-sm font-semibold">Session</h3>
            <p class="mt-1 text-sm">View your date, time, and venue.</p>
            <div class="mt-3">
                <Link href="/student/session"
                    class="rounded-lg border px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-800">View</Link>
            </div>
        </div>

        <div class="rounded-xl border bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-sm font-semibold">Gown</h3>
            <p class="mt-1 text-sm">Select size and see collection/return status.</p>
            <div class="mt-3">
                <Link href="/student/gown"
                    class="rounded-lg border px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-800">Manage</Link>
            </div>
        </div>
    </div>

    <!-- Big panel CTA -->
    <div class="rounded-xl border bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="text-base font-semibold">Next Action</h3>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
            Complete your registration first. After that, you can download your invitation and make payment if required.
        </p>
        <div class="mt-4 flex flex-wrap gap-2">
            <Link href="/student/invitation"
                class="rounded-lg border px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-800">Invitation</Link>
            <Link href="/student/payments"
                class="rounded-lg border px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-800">Payments</Link>
            <Link href="/student/notifications"
                class="rounded-lg border px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-800">Notifications
            </Link>
        </div>
    </div>
</template>
