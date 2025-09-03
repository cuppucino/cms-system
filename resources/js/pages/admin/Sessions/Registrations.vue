<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { Rocket } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Registration {
    id: number;
    student_name: string;
    session_name: string;
    guest_count: number;
    gown_size?: string | null;
    status?: 'pending' | 'registered' | 'checked_in';
    created_at?: string | null;
}

const props = defineProps<{ registrations: Registration[] }>();
const page = usePage();

// ---- sorting ----
type SortKey = keyof Registration;
const sortKey = ref<SortKey>('id');
const sortDir = ref<'asc' | 'desc'>('asc');

const toggleSort = (key: SortKey) => {
    if (sortKey.value === key) {
        sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortKey.value = key;
        sortDir.value = 'asc';
    }
};

const sorted = computed(() => {
    const arr = [...props.registrations];
    const dir = sortDir.value === 'asc' ? 1 : -1;

    return arr.sort((a, b) => {
        const x = (a[sortKey.value] ?? '') as any;
        const y = (b[sortKey.value] ?? '') as any;

        // numeric sort for id/guest_count
        if (sortKey.value === 'id' || sortKey.value === 'guest_count') {
            return ((Number(x) || 0) - (Number(y) || 0)) * dir;
        }

        // date sort
        if (sortKey.value === 'created_at') {
            return (new Date(x).getTime() - new Date(y).getTime()) * dir;
        }

        // string sort fallback
        return String(x).localeCompare(String(y)) * dir;
    });
});
</script>


<template>
    <Head title="Session Registrations" />
    <AppLayout :breadcrumbs="[{ title: 'Registrations', href: route('admin.registrations.index') }]">
        <div v-if="page.props.flash?.message" class="mb-4">
            <Alert class="bg-blue-200 flex items-start gap-3 p-4 rounded-md">
                <Rocket class="h-5 w-5 mt-1 shrink-0 text-blue-700" />
                <div>
                    <AlertTitle>Notification</AlertTitle>
                    <AlertDescription>{{ page.props.flash.message }}</AlertDescription>
                </div>
            </Alert>
        </div>

        <div class="mt-4">
            <table class="w-full border border-gray-300 rounded-md">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2 cursor-pointer select-none" @click="toggleSort('id')">
                            ID <span v-if="sortKey === 'id'">{{ sortDir === 'asc' ? '▲' : '▼' }}</span>
                        </th>
                        <th class="border px-4 py-2 cursor-pointer select-none" @click="toggleSort('student_name')">
                            Student <span v-if="sortKey === 'student_name'">{{ sortDir === 'asc' ? '▲' : '▼' }}</span>
                        </th>
                        <th class="border px-4 py-2 cursor-pointer select-none" @click="toggleSort('session_name')">
                            Session <span v-if="sortKey === 'session_name'">{{ sortDir === 'asc' ? '▲' : '▼' }}</span>
                        </th>
                        <th class="border px-4 py-2 text-center cursor-pointer select-none"
                            @click="toggleSort('guest_count')">
                            Guests <span v-if="sortKey === 'guest_count'">{{ sortDir === 'asc' ? '▲' : '▼' }}</span>
                        </th>
                        <th class="border px-4 py-2 cursor-pointer select-none" @click="toggleSort('gown_size')">
                            Gown <span v-if="sortKey === 'gown_size'">{{ sortDir === 'asc' ? '▲' : '▼' }}</span>
                        </th>
                        <th class="border px-4 py-2 cursor-pointer select-none" @click="toggleSort('status')">
                            Status <span v-if="sortKey === 'status'">{{ sortDir === 'asc' ? '▲' : '▼' }}</span>
                        </th>
                        <th class="border px-4 py-2 cursor-pointer select-none" @click="toggleSort('created_at')">
                            Registered At <span v-if="sortKey === 'created_at'">{{ sortDir === 'asc' ? '▲' : '▼' }}</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="r in sorted" :key="r.id" class="hover:bg-gray-50">
                        <td class="border px-4 py-2">{{ r.id }}</td>
                        <td class="border px-4 py-2">{{ r.student_name }}</td>
                        <td class="border px-4 py-2">{{ r.session_name }}</td>
                        <td class="border px-4 py-2 text-center">{{ r.guest_count }}</td>
                        <td class="border px-4 py-2">{{ r.gown_size || '-' }}</td>
                        <td class="border px-4 py-2">
                            <span :class="{
                                'text-gray-700': r.status === 'pending',
                                'text-blue-700': r.status === 'registered',
                                'text-emerald-700': r.status === 'checked_in',
                            }">
                                {{ r.status || 'pending' }}
                            </span>
                        </td>
                        <td class="border px-4 py-2">{{ r.created_at || '-' }}</td>
                    </tr>
                </tbody>
            </table>

        </div>
    </AppLayout>
</template>
