<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import StudentLayout from '@/layouts/StudentLayout.vue'
import { CheckCircle2, ShieldOff } from 'lucide-vue-next'
import moment from 'moment-timezone'

defineOptions({ layout: StudentLayout })

// The controller returns an array like:
// [{ id, title, message, is_read (computed), created_at }]
defineProps<{
    notifications: Array<{
        id: number
        title: string
        message: string
        is_read: boolean
        created_at: string
        created_at_iso: string
        created_ago: string
    }>
}>()


// ---- Actions ----

// Mark ALL as read (stamps user.last_notif_seen_at = now())
// Route: student.notifications.seen
const markAllSeen = () => {
    router.post(route('student.notifications.seen'), {}, { preserveScroll: true }).then(() => {
        // Update the unread notifications count on the frontend
        user.value.unread_notifications = 0
    })
}


// (Optional) If you still keep per-item "read" route, you can keep this.
// Otherwise you can delete it and the button below.
const markRead = (id: number) => {
    router.post(route('student.notifications.read', id), {}, { preserveScroll: true }).then(() => {
        // Decrease unread notifications count by 1
        user.value.unread_notifications -= 1
    })
}


// ---- Helpers ----
const toMYT = (iso: string | null) => {
    if (!iso) return '—'
    return moment.utc(iso).tz('Asia/Kuala_Lumpur').format('YYYY-MM-DD HH:mm:ss')
}
</script>

<template>
    <div class="mx-auto max-w-2xl space-y-4 p-4">
        <div class="mb-4 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Notifications</h1>

            <!-- Mark all as read -->
            <button type="button" @click="markAllSeen"
                class="rounded bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none"
                title="Mark all as read">
                Mark all as read
            </button>
        </div>

        <!-- Empty state -->
        <div v-if="!notifications.length" class="text-gray-500 dark:text-gray-400">
            No notifications yet.
        </div>

        <!-- Notifications list -->
        <div v-for="n in notifications" :key="n.id"
            class="rounded-lg border p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-start justify-between">
                <h2 class="font-semibold text-gray-900 dark:text-gray-100">
                    {{ n.title }}
                    <span v-if="!n.is_read" class="ml-2 rounded-full bg-indigo-600 px-2 py-0.5 text-xs text-white">
                        New
                    </span>
                </h2>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ toMYT(n.created_at_iso) }}
                </span>
            </div>

            <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                {{ n.message }}
            </p>

            <!-- Per-item mark read (optional). Keep only if your route exists and you still want it. -->
            <button v-if="!n.is_read" @click="markRead(n.id)"
                class="mt-2 text-xs font-medium text-blue-700 hover:underline" type="button">
                Mark as read
            </button>

            <!-- Status badge -->
            <div v-if="n.is_read" class="mt-2 flex items-center gap-2">
                <CheckCircle2 class="h-4 w-4 text-green-600" />
                <span class="text-xs text-green-600">Read</span>
            </div>
            <div v-else class="mt-2 flex items-center gap-2">
                <ShieldOff class="h-4 w-4 text-gray-400" />
                <span class="text-xs text-gray-400">Unread</span>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* custom styles if needed */
</style>
