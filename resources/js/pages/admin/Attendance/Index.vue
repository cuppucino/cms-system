<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { reactive, computed, ref, watch } from 'vue'
import { Search, Download } from 'lucide-vue-next'

const props = defineProps<{
  records: any,
  filters: { status?: string; search?: string }
}>()

const filters = reactive({
  status: props.filters?.status || '',
  search: props.filters?.search || '',
})

const applyFilters = () => {
  router.get(route('admin.attendance.index'), filters, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}

// optional: debounce search so typing doesn't spam requests
const typing = ref<NodeJS.Timeout | null>(null)
watch(() => filters.search, () => {
  if (typing.value) clearTimeout(typing.value)
  typing.value = setTimeout(applyFilters, 350)
})

const statusBadge = (s: string) => {
  switch (s) {
    case 'checked_in': return 'bg-emerald-100 text-emerald-700'
    case 'registered': return 'bg-indigo-100 text-indigo-700'
    case 'pending': return 'bg-amber-100 text-amber-700'
    default: return 'bg-gray-100 text-gray-700'
  }
}
</script>

<template>
  <Head title="Attendance" />

  <AppLayout :breadcrumbs="[{ title: 'Attendance', href: route('admin.attendance.index') }]">
    <div class="space-y-4 p-4">
      <!-- Header / Actions -->
      <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-gray-900">Attendance</h1>
        <Link :href="route('admin.attendance.export')">
          <Button class="inline-flex items-center gap-2 bg-emerald-600 text-white hover:bg-emerald-500">
            <Download class="h-4 w-4" /> Export Attendance
          </Button>
        </Link>
      </div>

      <!-- Filters -->
      <div class="grid grid-cols-1 gap-3 rounded-xl border border-gray-200 bg-white p-3 shadow-sm sm:grid-cols-3">
        <div class="relative sm:col-span-2">
          <Search class="pointer-events-none absolute left-2 top-2.5 h-4 w-4 text-gray-400" />
          <input v-model="filters.search" type="text" placeholder="Search student name or email"
                 class="w-full rounded-md border border-gray-300 bg-white px-8 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" />
        </div>
        <div>
          <select v-model="filters.status" @change="applyFilters"
                  class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="registered">Registered</option>
            <option value="checked_in">Checked In</option>
          </select>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-left text-gray-600">
            <tr>
              <th class="px-4 py-3 font-medium">Student</th>
              <th class="px-4 py-3 font-medium">Email</th>
              <th class="px-4 py-3 font-medium">Session</th>
              <th class="px-4 py-3 font-medium">Status</th>
              <th class="px-4 py-3 font-medium">Check-in Time</th>
              <th class="px-4 py-3 text-right font-medium">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!props.records?.length">
              <td colspan="6" class="py-8 text-center text-gray-500">No records found.</td>
            </tr>
            <tr v-for="r in props.records" :key="r.id" class="border-t hover:bg-gray-50">
              <td class="px-4 py-2 font-medium text-gray-900">{{ r.user_name }}</td>
              <td class="px-4 py-2">{{ r.user_email }}</td>
              <td class="px-4 py-2">{{ r.session_name }}</td>
              <td class="px-4 py-2">
                <span :class="'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ' + statusBadge(r.status)">{{ r.status?.replace('_', ' ') }}</span>
              </td>
              <td class="px-4 py-2">{{ r.checked_in_at ?? '—' }}</td>
              <td class="px-4 py-2 text-right">
                <Button v-if="r.status !== 'checked_in'"
                        class="h-8 bg-indigo-600 px-3 text-white hover:bg-indigo-500"
                        title="Manual check-in" aria-label="Manual check-in"
                        @click="router.post(route('admin.attendance.manualCheckIn', { id: r.id }))">
                  Manual Check-in
                </Button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Clean filters, badges, and table consistent with the rest of the admin UI */
</style>
