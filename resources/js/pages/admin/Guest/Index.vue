<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { Users, Search } from 'lucide-vue-next'

const props = defineProps<{
  guests: {
    id: number
    student_name: string
    student_id?: string | number
    guests_registered: number
  }[]
  totals?: {
    total_registered: number
    slots_left: number
  }
}>()

// Compute totals if not provided
const computedTotals = computed(() => {
  if (props.totals) return props.totals
  const totalRegistered = props.guests.reduce((sum, g) => sum + (g.guests_registered || 0), 0)
  return { total_registered: totalRegistered, slots_left: 0 }
})

// Helper
const pct = (n: number, d: number) => (d > 0 ? Math.round((n / d) * 100) : 0)

const q = ref('')
const filtered = computed(() => {
  const needle = q.value.trim().toLowerCase()
  if (!needle) return props.guests
  return props.guests.filter(g => {
    const hay = `${g.student_name ?? ''} ${g.student_id ?? ''}`.toLowerCase()
    return hay.includes(needle)
  })
})
</script>

<template>
  <Head title="Guest Registrations" />

  <AppLayout :breadcrumbs="[{ title: 'Guests', href: route('admin.guest.index') }]">
    <div class="space-y-4 p-4">
      <!-- Header -->
      <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-gray-900">Guest Registrations</h1>
        <div class="relative hidden sm:block">
          <Search class="pointer-events-none absolute left-2 top-2.5 h-4 w-4 text-gray-400" />
          <input
            v-model="q"
            type="text"
            placeholder="Search by student name or ID"
            class="w-64 rounded-md border border-gray-300 bg-white px-8 py-2 text-sm shadow-sm
                   focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
          />
        </div>
      </div>

      <!-- Summary -->
      <section class="grid grid-cols-1 gap-3 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
          <p class="text-sm text-gray-500">Total Guests Registered</p>
          <p class="mt-1 text-2xl font-semibold text-gray-800">{{ computedTotals.total_registered }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
          <p class="text-sm text-gray-500">Slots Left</p>
          <p class="mt-1 text-2xl font-semibold text-gray-800">{{ computedTotals.slots_left }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
          <p class="text-sm text-gray-500">Fill Rate</p>
          <p class="mt-1 text-2xl font-semibold text-gray-800">
            {{ pct(computedTotals.total_registered, computedTotals.total_registered + computedTotals.slots_left) }}%
          </p>
          <div class="mt-3 h-1 w-full overflow-hidden rounded-full bg-gray-200">
            <div
              class="h-1 rounded-full bg-gradient-to-r from-emerald-500 to-indigo-600"
              :style="{ width: pct(computedTotals.total_registered, computedTotals.total_registered + computedTotals.slots_left) + '%' }"
            />
          </div>
        </div>
      </section>

      <!-- Table -->
      <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-left text-gray-600">
            <tr>
              <th class="px-4 py-3 font-medium">Student</th>
              <th class="px-4 py-3 font-medium">Student ID</th>
              <th class="px-4 py-3 font-medium text-right">Guests Registered</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!filtered.length">
              <td colspan="3" class="py-8 text-center text-gray-500">
                No guests registered yet.
              </td>
            </tr>
            <tr
              v-for="guest in filtered"
              :key="guest.id"
              class="border-t hover:bg-gray-50"
            >
              <td class="px-4 py-2 font-medium text-gray-900">{{ guest.student_name }}</td>
              <td class="px-4 py-2 text-gray-700">{{ guest.student_id ?? '—' }}</td>
              <td class="px-4 py-2 text-right tabular-nums">
                <div class="inline-flex items-center justify-end gap-1">
                  <Users class="h-4 w-4 text-gray-400" />
                  <span>{{ guest.guests_registered }}</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
