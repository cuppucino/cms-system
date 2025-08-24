<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';

const props = defineProps<{
    attendance: { total_registered: number; total_checked_in: number },
    gowns: { size: string; total: number; returned: number }[],
    guests: { session: { name: string }, total_guests: number }[],
    sessions: { name: string; quota: number; registered: number }[],
}>();

const page = usePage();
</script>

<template>

    <Head title="Reports & Analytics" />
    <AppLayout :breadcrumbs="[{ title: 'Reports', href: route('admin.reports.index') }]">

        <h1 class="text-2xl font-bold mb-6">Reports & Analytics</h1>

        <!-- Attendance -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold mb-2">Attendance</h2>
            <p>Total Registered: {{ props.attendance.total_registered }}</p>
            <p>Total Checked In: {{ props.attendance.total_checked_in }}</p>
        </section>

        <!-- Gowns -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold mb-2">Gown Report</h2>
            <table class="w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-2 py-1">Size</th>
                        <th class="border px-2 py-1">Issued</th>
                        <th class="border px-2 py-1">Returned</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="g in props.gowns" :key="g.size">
                        <td class="border px-2 py-1">{{ g.size }}</td>
                        <td class="border px-2 py-1">{{ g.total }}</td>
                        <td class="border px-2 py-1">{{ g.returned }}</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Guests -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold mb-2">Guest Passes</h2>
            <table class="w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-2 py-1">Session</th>
                        <th class="border px-2 py-1">Total Guests</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="g in props.guests" :key="g.session_name">
                        <td class="border px-2 py-1">{{ g.session_name }}</td>
                        <td class="border px-2 py-1">{{ g.total_guests }}</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Sessions -->
        <section>
            <h2 class="text-xl font-semibold mb-2">Session Report</h2>
            <table class="w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-2 py-1">Name</th>
                        <th class="border px-2 py-1">Quota</th>
                        <th class="border px-2 py-1">Registered</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="s in props.sessions" :key="s.name">
                        <td class="border px-2 py-1">{{ s.name }}</td>
                        <td class="border px-2 py-1">{{ s.quota }}</td>
                        <td class="border px-2 py-1">{{ s.registered }}</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </AppLayout>
</template>
