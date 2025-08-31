<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import { Head, Link } from '@inertiajs/vue3'
import type { BreadcrumbItem } from '@/types'

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Users', href: route('admin.users.index') },
    { title: 'Show', href: '#' },
]

const props = defineProps({
    user: { type: Object, required: true },
})
</script>

<template>

    <Head title="User Details" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900">User Details</h1>
                <Link :href="route('admin.users.index')">
                <Button class="bg-gray-600 text-white hover:bg-gray-500">Back</Button>
                </Link>
            </div>

            <!-- Profile Card -->
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <dl class="grid grid-cols-1 divide-y divide-gray-100 text-sm md:grid-cols-2 md:divide-x md:divide-y-0">
                    <div class="p-4">
                        <dt class="font-medium text-gray-700">Name</dt>
                        <dd class="mt-1 text-gray-900">{{ props.user.name }}</dd>
                    </div>
                    <div class="p-4">
                        <dt class="font-medium text-gray-700">Email</dt>
                        <dd class="mt-1 text-gray-900">{{ props.user.email }}</dd>
                    </div>
                    <div class="p-4">
                        <dt class="font-medium text-gray-700">Student ID</dt>
                        <dd class="mt-1 text-gray-900">{{ props.user.student_id ?? '—' }}</dd>
                    </div>
                    <div class="p-4">
                        <dt class="font-medium text-gray-700">Course</dt>
                        <dd class="mt-1 text-gray-900">{{ props.user.course?.name ?? '—' }}</dd>
                    </div>
                    <div class="p-4">
                        <dt class="font-medium text-gray-700">Gown Size</dt>
                        <dd class="mt-1 text-gray-900">{{ props.user.gown_size ?? '—' }}</dd>
                    </div>
                    <div class="p-4">
                        <dt class="font-medium text-gray-700">Convocation Session</dt>
                        <dd class="mt-1 text-gray-900">{{ props.user.convocation_session?.name ?? '—' }}</dd>
                    </div>
                    <div class="p-4">
                        <dt class="font-medium text-gray-700">Status</dt>
                        <dd class="mt-1 text-gray-900">{{ props.user.status ?? '—' }}</dd>
                    </div>
                    <div class="p-4">
                        <dt class="font-medium text-gray-700">Role</dt>
                        <dd class="mt-1"><span
                                :class="props.user.is_admin ? 'inline-flex items-center rounded-full bg-emerald-600 px-2 py-0.5 text-xs font-medium text-white' : 'inline-flex items-center rounded-full bg-gray-200 px-2 py-0.5 text-xs font-medium text-gray-800'">{{
                                    props.user.is_admin ? 'Admin' : 'User' }}</span></dd>
                    </div>
                </dl>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Clean profile card layout */
</style>
