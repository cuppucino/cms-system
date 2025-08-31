<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { Plus, Edit3, Trash2, Calendar, MapPin, Users, Search } from 'lucide-vue-next'
import { computed, ref } from 'vue'

const props = defineProps<{
  sessions: Array<{
    id: number
    name: string
    date: string
    location: string
    quota: number
    registrations_count: number
  }>
}>()

const deleteSession = (id: number) => {
  if (confirm('Are you sure you want to delete this session?')) {
    router.delete(route('admin.sessions.destroy', id))
  }
}

// --- totals & helpers ---
const totals = computed(() => {
  const t = { quota: 0, registered: 0 }
  for (const s of props.sessions || []) {
    t.quota += Number(s.quota || 0)
    t.registered += Number(s.registrations_count || 0)
  }
  return t
})
const pct = (n: number, d: number) => (d > 0 ? Math.round((n / d) * 100) : 0)

// --- filter ---
const q = ref('')
const filtered = computed(() => {
  const needle = q.value.trim().toLowerCase()
  if (!needle) return props.sessions || []
  return (props.sessions || []).filter(s => `${s.name} ${s.location}`.toLowerCase().includes(needle))
})
</script>

<template>
  <Head title="Convocation Sessions" />

  <AppLayout :breadcrumbs="[{ title: 'Sessions', href: route('admin.sessions.index') }]">
    <div class="space-y-4 p-4">
      <!-- Header -->
      <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-gray-900">Convocation Sessions</h1>
        <div class="flex items-center gap-2">
          <div class="relative hidden sm:block">
            <Search class="pointer-events-none absolute left-2 top-2.5 h-4 w-4 text-gray-400" />
            <input v-model="q" type="text" placeholder="Search by name or location"
                   class="w-64 rounded-md border border-gray-300 bg-white px-8 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" />
          </div>
          <Link :href="route('admin.sessions.create')">
            <Button class="inline-flex items-center gap-2 bg-indigo-600 text-white hover:bg-indigo-500">
              <Plus class="h-4 w-4" /> Create Session
            </Button>
          </Link>
        </div>
      </div>

      <!-- Summary -->
      <section class="grid grid-cols-1 gap-3 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
          <p class="text-sm text-gray-500">Total Quota</p>
          <p class="mt-1 text-2xl font-semibold text-gray-800">{{ totals.quota.toLocaleString() }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
          <p class="text-sm text-gray-500">Total Registered</p>
          <p class="mt-1 text-2xl font-semibold text-gray-800">{{ totals.registered.toLocaleString() }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
          <p class="text-sm text-gray-500">Fill Rate</p>
          <p class="mt-1 text-2xl font-semibold text-gray-800">{{ pct(totals.registered, totals.quota) }}%</p>
          <div class="mt-3 h-1 w-full overflow-hidden rounded-full bg-gray-200">
            <div class="h-1 rounded-full bg-gradient-to-r from-emerald-500 to-indigo-600" :style="{ width: pct(totals.registered, totals.quota) + '%' }" />
          </div>
        </div>
      </section>

      <!-- Table -->
      <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-left text-gray-600">
            <tr>
              <th class="px-4 py-3 font-medium">Name</th>
              <th class="px-4 py-3 font-medium">Date</th>
              <th class="px-4 py-3 font-medium">Location</th>
              <th class="px-4 py-3 font-medium text-right">Quota</th>
              <th class="px-4 py-3 font-medium text-right">Registered</th>
              <th class="px-4 py-3 text-right font-medium">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!filtered.length">
              <td colspan="6" class="py-8 text-center text-gray-500">No sessions available.</td>
            </tr>
            <tr v-for="s in filtered" :key="s.id" class="border-t hover:bg-gray-50">
              <td class="px-4 py-2 font-medium text-gray-900">{{ s.name }}</td>
              <td class="px-4 py-2 text-gray-700">
                <div class="flex items-center gap-1">
                  <Calendar class="h-4 w-4 text-gray-400" />
                  <span>{{ s.date }}</span>
                </div>
              </td>
              <td class="px-4 py-2 text-gray-700">
                <div class="flex items-center gap-1">
                  <MapPin class="h-4 w-4 text-gray-400" />
                  <span>{{ s.location }}</span>
                </div>
              </td>
              <td class="px-4 py-2 text-right tabular-nums">{{ s.quota }}</td>
              <td class="px-4 py-2 text-right tabular-nums">
                <div class="inline-flex items-center justify-end gap-1">
                  <Users class="h-4 w-4 text-gray-400" />
                  <span>{{ s.registrations_count }}</span>
                </div>
              </td>
              <td class="px-4 py-2 text-right">
                <div class="inline-flex items-center gap-1">
                  <Link :href="route('admin.sessions.edit', s.id)">
                    <Button variant="ghost" class="h-8 px-2 text-gray-700 hover:bg-gray-100"><Edit3 class="h-4 w-4" /></Button>
                  </Link>
                  <Button variant="ghost" class="h-8 px-2 text-red-600 hover:bg-red-50" @click="deleteSession(s.id)"><Trash2 class="h-4 w-4" /></Button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Right-align numeric columns and keep layout consistent */
</style>
