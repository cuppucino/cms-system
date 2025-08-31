<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import { Head, Link, usePage, router } from '@inertiajs/vue3'
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
import { Rocket, Bell, Search } from 'lucide-vue-next'
import { reactive, ref, watch } from 'vue'

interface Notification {
  id: number
  user_name: string
  title: string
  message: string
  is_read: boolean
  created_at: string
}

const props = defineProps<{ notifications: Notification[] }>()
const page = usePage()

// Filters (optional server-side)
const filters = reactive<{ q: string; read: '' | 'read' | 'unread' }>({ q: '', read: '' })
const apply = () => router.get(route('admin.notifications.index'), { search: filters.q, read: filters.read }, { preserveState: true, preserveScroll: true, replace: true })
const typing = ref<NodeJS.Timeout | null>(null)
watch(() => filters.q, () => { if (typing.value) clearTimeout(typing.value); typing.value = setTimeout(apply, 350) })
</script>

<template>
  <Head title="Notifications" />

  <AppLayout :breadcrumbs="[{ title: 'Notifications', href: route('admin.notifications.index') }]">
    <div class="space-y-4 p-4">
      <!-- Flash message -->
      <div v-if="page.props.flash?.message" class="rounded-xl border border-indigo-200 bg-indigo-50 p-4">
        <Alert class="flex items-start gap-3 p-0">
          <Rocket class="mt-0.5 h-5 w-5 shrink-0 text-indigo-600" />
          <div>
            <AlertTitle class="text-sm font-semibold text-indigo-900">Notification</AlertTitle>
            <AlertDescription class="text-sm text-indigo-800">{{ page.props.flash.message }}</AlertDescription>
          </div>
        </Alert>
      </div>

      <!-- Header -->
      <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-gray-900">Notifications</h1>
        <Link :href="route('admin.notifications.create')">
          <Button class="inline-flex items-center gap-2 bg-indigo-600 text-white hover:bg-indigo-500">
            <Bell class="h-4 w-4" /> Send Notification
          </Button>
        </Link>
      </div>

      <!-- Filters -->
      <div class="grid grid-cols-1 gap-3 rounded-xl border border-gray-200 bg-white p-3 shadow-sm sm:grid-cols-3">
        <div class="relative sm:col-span-2">
          <Search class="pointer-events-none absolute left-2 top-2.5 h-4 w-4 text-gray-400" />
          <input v-model="filters.q" type="text" placeholder="Search by recipient, title, or text"
                 class="w-full rounded-md border border-gray-300 bg-white px-8 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" />
        </div>
        <div>
          <select v-model="filters.read" @change="apply"
                  class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
            <option value="">All</option>
            <option value="read">Read</option>
            <option value="unread">Unread</option>
          </select>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-left text-gray-600">
            <tr>
              <th class="px-4 py-3 font-medium">To</th>
              <th class="px-4 py-3 font-medium">Title</th>
              <th class="px-4 py-3 font-medium">Message</th>
              <th class="px-4 py-3 font-medium">Read?</th>
              <th class="px-4 py-3 font-medium">Sent At</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!props.notifications?.length">
              <td colspan="5" class="py-8 text-center text-gray-500">No notifications yet.</td>
            </tr>
            <tr v-for="n in props.notifications" :key="n.id" class="border-t hover:bg-gray-50">
              <td class="px-4 py-2 font-medium text-gray-900">{{ n.user_name }}</td>
              <td class="px-4 py-2">{{ n.title }}</td>
              <td class="px-4 py-2 max-w-[560px] truncate" :title="n.message">{{ n.message }}</td>
              <td class="px-4 py-2">
                <span :class="n.is_read ? 'inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700' : 'inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700'">
                  {{ n.is_read ? 'Read' : 'Unread' }}
                </span>
              </td>
              <td class="px-4 py-2">{{ n.created_at }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Clean layout with filters, badges, and truncation for long messages */
</style>
