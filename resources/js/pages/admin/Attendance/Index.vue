<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { reactive } from 'vue';

const props = defineProps<{
    records: any,
    filters: { status?: string, search?: string }
}>();

const filters = reactive({
    status: props.filters.status || '',
    search: props.filters.search || ''
});

const applyFilters = () => {
    router.get(route('admin.attendance.index'), filters, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>

    <Head title="Attendance" />

    <AppLayout :breadcrumbs="[{ title: 'Attendance', href: route('admin.attendance.index') }]">
        <div class="mb-4 flex items-center space-x-4">
            <!-- Search -->
            <input v-model="filters.search" type="text" placeholder="Search student..."
                class="border rounded px-3 py-2" />

            <!-- Status Filter -->
            <select v-model="filters.status" class="border rounded px-3 py-2">
                <option value="">All Status</option>
                <option value="reserved">Reserved</option>
                <option value="checked_in">Checked In</option>
            </select>

            <Button @click="applyFilters" class="bg-blue-600">Apply</Button>
        </div>

        <Link :href="route('admin.attendance.export')">
        <Button class="bg-green-600">Export Attendance</Button>
        </Link>


        <!-- Table -->
        <table class="w-full border-collapse border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">Student</th>
                    <th class="border px-4 py-2">Email</th>
                    <th class="border px-4 py-2">Status</th>
                    <th class="border px-4 py-2">Check-in Time</th>
                    <th class="border px-4 py-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="r in props.records.data" :key="r.id">
                    <td class="border px-4 py-2">{{ r.user?.name }}</td>
                    <td class="border px-4 py-2">{{ r.user?.email }}</td>
                    <td class="border px-4 py-2">{{ r.status }}</td>
                    <td class="border px-4 py-2">{{ r.checked_in_at ?? '-' }}</td>
                    <td class="border px-4 py-2 text-center">
                        <Button class="bg-green-600"
                            @click="router.post(route('admin.attendance.manualCheckIn', { id: r.id }))"
                            v-if="r.status !== 'checked_in'">
                            Manual Check-in
                        </Button>
                    </td>
                </tr>
            </tbody>
        </table>
    </AppLayout>
</template>
