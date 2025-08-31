<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import { Search, Users, X } from 'lucide-vue-next'

interface Student { id:number; name:string; email:string }
interface Session { id:number|string; name:string }

const props = defineProps<{
  students: Student[],
  sessions: Session[],
  prefill_user_id?: number
}>()

const form = useForm({
  user_ids: props.prefill_user_id ? [props.prefill_user_id] : [] as number[],
  convocation_session_id: '' as string|number|''
})

// ------- Search + filtering -------
const q = ref('')
const filtered = computed(() => {
  const needle = q.value.trim().toLowerCase()
  if (!needle) return props.students || []
  return (props.students || []).filter(s =>
    `${s.name} ${s.email}`.toLowerCase().includes(needle)
  )
})

// ------- Select helpers -------
const isSelected = (id:number) => form.user_ids.includes(id)
const toggleId = (id:number) => {
  if (isSelected(id)) form.user_ids = form.user_ids.filter(i => i !== id)
  else form.user_ids.push(id)
}
const selectAllFiltered = () => {
  const set = new Set(form.user_ids)
  for (const s of filtered.value) set.add(s.id)
  form.user_ids = Array.from(set)
}
const clearAll = () => { form.user_ids = [] }
const removeOne = (id:number) => { form.user_ids = form.user_ids.filter(x => x !== id) }

// Optional: auto-highlight prefill in list on load
watch(() => props.prefill_user_id, (val) => {
  if (val && !form.user_ids.includes(val)) form.user_ids.push(val)
})
</script>

<template>
  <Head title="New Invitation" />
  <AppLayout :breadcrumbs="[
    { title:'Invitations', href: route('admin.invitations.index') },
    { title:'Create', href: '#' }
  ]">

    <div class="grid gap-6 md:grid-cols-3">
      <!-- Left: picker -->
      <div class="md:col-span-2 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="mb-3 flex items-center justify-between gap-3">
          <h2 class="font-semibold text-gray-900">Select Students</h2>
          <div class="text-xs text-gray-500">
            Selected: <span class="font-medium text-indigo-700">{{ form.user_ids.length }}</span>
          </div>
        </div>

        <!-- Search + bulk -->
        <div class="mb-3 flex flex-wrap items-center gap-2">
          <div class="relative grow">
            <Search class="pointer-events-none absolute left-2 top-2.5 h-4 w-4 text-gray-400" />
            <input
              v-model="q"
              type="text"
              placeholder="Search by name or email"
              class="w-full rounded-md border border-gray-300 bg-white px-8 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
            />
          </div>
          <Button
            type="button"
            class="inline-flex items-center gap-2 bg-indigo-600 text-white hover:bg-indigo-500"
            @click="selectAllFiltered"
            :disabled="!filtered.length"
            title="Add all currently filtered results"
          >
            <Users class="h-4 w-4" />
            Add all ({{ filtered.length }})
          </Button>
          <Button
            type="button"
            variant="ghost"
            class="h-9 px-3 text-gray-700 hover:bg-gray-100"
            @click="clearAll"
            :disabled="!form.user_ids.length"
            title="Clear all selected recipients"
          >
            Clear
          </Button>
        </div>

        <!-- Selected chips -->
        <div class="mb-3 flex flex-wrap gap-2">
          <template v-if="form.user_ids.length">
            <span
              v-for="id in form.user_ids"
              :key="id"
              class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2 py-1 text-xs text-indigo-700"
            >
              {{ (props.students.find(s => s.id === id) || {}).name || id }}
              <button
                type="button"
                class="ml-1 text-indigo-500 hover:text-indigo-700"
                @click="removeOne(id)"
                aria-label="Remove recipient"
                title="Remove"
              >
                <X class="h-3.5 w-3.5" />
              </button>
            </span>
          </template>
          <span v-else class="text-xs text-gray-500">No recipients selected yet.</span>
        </div>

        <!-- Table -->
        <div class="max-h-96 overflow-y-auto rounded-lg border border-gray-200">
          <table class="w-full text-sm">
            <thead class="sticky top-0 bg-gray-50 text-left text-gray-600">
              <tr>
                <th class="px-3 py-2 w-14">Pick</th>
                <th class="px-3 py-2">Name</th>
                <th class="px-3 py-2">Email</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="s in filtered"
                :key="s.id"
                class="border-t"
                :class="isSelected(s.id) ? 'bg-indigo-50/40' : 'hover:bg-gray-50'"
              >
                <td class="px-3 py-2">
                  <input
                    type="checkbox"
                    :checked="isSelected(s.id)"
                    @change="toggleId(s.id)"
                    :aria-label="`Select ${s.name}`"
                  />
                </td>
                <td class="px-3 py-2 font-medium text-gray-900">{{ s.name }}</td>
                <td class="px-3 py-2 text-gray-700">{{ s.email }}</td>
              </tr>

              <tr v-if="!filtered.length">
                <td colspan="3" class="py-8 text-center text-gray-500">No matches.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <p v-if="form.errors.user_ids" class="mt-2 text-sm text-red-600">
          {{ form.errors.user_ids }}
        </p>
      </div>

      <!-- Right: session + actions -->
      <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <h2 class="mb-3 font-semibold text-gray-900">Session & Issue</h2>

        <label class="block text-sm text-gray-700 mb-1">Convocation Session (optional)</label>
        <select
          v-model="form.convocation_session_id"
          class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
        >
          <option value="">— Not set —</option>
          <option v-for="sess in props.sessions" :key="sess.id" :value="sess.id">{{ sess.name }}</option>
        </select>
        <p v-if="form.errors.convocation_session_id" class="mt-2 text-sm text-red-600">
          {{ form.errors.convocation_session_id }}
        </p>

        <!-- Summary -->
        <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm">
          <div class="flex justify-between">
            <span class="text-gray-600">Recipients</span>
            <span class="font-medium text-gray-900">{{ form.user_ids.length }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Session</span>
            <span class="font-medium text-gray-900">
              {{ (props.sessions.find(s => s.id == form.convocation_session_id) || {}).name || 'Not set' }}
            </span>
          </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex flex-wrap gap-2">
          <Button
            class="bg-indigo-600 text-white hover:bg-indigo-500"
            :disabled="!form.user_ids.length"
            @click="form.post(route('admin.invitations.store'))"
            title="Generate invitations for selected students"
          >
            Generate Invitations
          </Button>
          <Link :href="route('admin.invitations.index')">
            <Button class="bg-gray-600 text-white hover:bg-gray-500">Cancel</Button>
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
