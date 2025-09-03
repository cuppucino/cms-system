<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })

const props = defineProps<{
    registrations: {
        id: number
        guest_count: number
        attendance_confirmed: boolean
        user: { id: number; name: string; email: string }
        session: { id: number; name: string; date: string }
    }[]
}>()
</script>

<template>
  <Head title="Guests" />

  <div class="p-6 bg-white rounded-xl shadow">
    <h1 class="text-xl font-semibold mb-4">Student Registrations</h1>

    <table class="min-w-full border text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 text-left">Student</th>
          <th class="px-4 py-2 text-left">Email</th>
          <th class="px-4 py-2 text-left">Session</th>
          <th class="px-4 py-2 text-left">Guests</th>
          <th class="px-4 py-2 text-left">Attendance</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="r in props.registrations" :key="r.id" class="border-t">
          <td class="px-4 py-2">{{ r.user.name }}</td>
          <td class="px-4 py-2">{{ r.user.email }}</td>
          <td class="px-4 py-2">
            {{ r.session.name }} ({{ new Date(r.session.date).toLocaleDateString() }})
          </td>
          <td class="px-4 py-2">{{ r.guest_count }}</td>
          <td class="px-4 py-2">
            <span v-if="r.attendance_confirmed" class="text-green-600">✔ Confirmed</span>
            <span v-else class="text-gray-500">Not confirmed</span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
