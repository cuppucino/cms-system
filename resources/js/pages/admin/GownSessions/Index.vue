<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { Calendar, MapPin, Users, Plus, Edit3, Trash2 } from 'lucide-vue-next'

const props = defineProps<{
  sessions: Array<{
    id: number
    date: string
    start_time: string | null
    end_time: string | null
    location: string
    quota: number
    collections_count: number
    note?: string | null
  }>
}>()

const delItem = (id:number) => {
  if (confirm('Delete this gown session?')) {
    router.delete(route('admin.gown-sessions.destroy', id))
  }
}
</script>

<template>
  <Head title="Gown Sessions" />
  <AppLayout :breadcrumbs="[{ title: 'Gown Sessions', href: route('admin.gown-sessions.index') }]">
    <div class="space-y-4 p-4">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Gown Sessions</h1>
        <Link :href="route('admin.gown-sessions.create')">
          <Button class="inline-flex items-center gap-2 bg-indigo-600 text-white hover:bg-indigo-500">
            <Plus class="h-4 w-4" /> New Session
          </Button>
        </Link>
      </div>

      <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-left text-gray-600">
            <tr>
              <th class="px-4 py-3 font-medium">Date/Time</th>
              <th class="px-4 py-3 font-medium">Location</th>
              <th class="px-4 py-3 font-medium text-right">Quota</th>
              <th class="px-4 py-3 font-medium text-right">Booked</th>
              <th class="px-4 py-3 text-right font-medium">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!props.sessions.length">
              <td colspan="5" class="py-8 text-center text-gray-500">No sessions yet.</td>
            </tr>
            <tr v-for="s in props.sessions" :key="s.id" class="border-t hover:bg-gray-50">
              <td class="px-4 py-2">
                <div class="flex items-center gap-2 text-gray-800">
                  <Calendar class="h-4 w-4 text-gray-400" />
                  <span>{{ s.date }} <span v-if="s.start_time">• {{ s.start_time }}–{{ s.end_time }}</span></span>
                </div>
              </td>
              <td class="px-4 py-2">
                <div class="flex items-center gap-2">
                  <MapPin class="h-4 w-4 text-gray-400" />
                  <span>{{ s.location }}</span>
                </div>
              </td>
              <td class="px-4 py-2 text-right tabular-nums">{{ s.quota }}</td>
              <td class="px-4 py-2 text-right tabular-nums">
                <div class="inline-flex items-center justify-end gap-1">
                  <Users class="h-4 w-4 text-gray-400" />
                  <span>{{ s.collections_count }}</span>
                </div>
              </td>
              <td class="px-4 py-2 text-right">
                <div class="inline-flex items-center gap-1">
                  <Link :href="route('admin.gown-sessions.edit', s.id)">
                    <Button variant="ghost" class="h-8 px-2 text-gray-700 hover:bg-gray-100">
                      <Edit3 class="h-4 w-4" />
                    </Button>
                  </Link>
                  <Button variant="ghost" class="h-8 px-2 text-red-600 hover:bg-red-50" @click="delItem(s.id)">
                    <Trash2 class="h-4 w-4" />
                  </Button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
