<script setup lang="ts">
import StudentLayout from '@/layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import moment from 'moment-timezone'

defineOptions({ layout: StudentLayout })

type Invitation = {
    session?: string | null
    created_at?: string | null
    status?: 'Active' | 'Revoked'
    qr_base64?: string | null
    code?: string | null
}

const props = defineProps<{ invitation: Invitation | null }>()

// Function to convert time to Malaysia Time
const getMalaysiaTime = (time: string | null) => {
    if (time) {
        return moment(time).tz('Asia/Kuala_Lumpur').format('YYYY-MM-DD HH:mm:ss') // Format to Malaysia Time
    }
    return '—' // Return '—' if no time is available
}
</script>

<template>

    <Head title="Invitation" />

    <!-- Page header -->
    <div class="rounded-xl border bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900 mb-6">
        <h1 class="text-xl font-semibold">My Invitation</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
            Download your invitation to present during entry and check-in.
        </p>
    </div>

    <div class="max-w-2xl space-y-4">
        <!-- Empty state -->
        <div v-if="!props.invitation"
            class="rounded-xl border bg-white p-6 text-sm text-gray-600 shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            No active invitation has been issued yet.
        </div>

        <!-- Invitation card -->
        <div v-else class="rounded-xl border bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Session</p>
                    <p class="font-medium text-gray-900 dark:text-gray-50">
                        {{ props.invitation.session || '—' }}
                    </p>
                </div>

                <span class="mt-2 inline-flex h-6 items-center rounded-full px-2 text-xs font-medium sm:mt-0" :class="props.invitation.status === 'Active'
                    ? 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-200'
                    : 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-200'">
                    {{ props.invitation.status }}
                </span>
            </div>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Issued At</p>
                    <p class="font-medium text-gray-900 dark:text-gray-50">
                        {{ getMalaysiaTime(props.invitation.created_at) }}
                    </p>
                </div>

                <div v-if="props.invitation.code">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Code</p>
                    <p class="font-mono text-sm font-semibold text-gray-900 dark:text-gray-50">
                        {{ props.invitation.code }}
                    </p>
                </div>
            </div>

            <div v-if="props.invitation.qr_base64" class="mt-6">
                <img :src="`data:image/png;base64,${props.invitation.qr_base64}`" alt="Invitation QR"
                    class="h-36 w-36 rounded-md border dark:border-gray-800" />
            </div>

            <!-- Actions -->
            <div class="mt-6">
                <a href="/student/invitation/download"
                    class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                    Download Invitation
                </a>
            </div>
        </div>
    </div>
</template>
