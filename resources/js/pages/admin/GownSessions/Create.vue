<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'

const form = useForm({
  date: '',
  start_time: '',
  end_time: '',
  location: '',
  quota: 0,
  note: ''
})

const submit = () => form.post(route('admin.gown-sessions.store'))
</script>

<template>
  <Head title="Create Gown Session" />
  <AppLayout :breadcrumbs="[{ title: 'Gown Sessions', href: route('admin.gown-sessions.index') }, { title:'Create', href:'#' }]">
    <div class="space-y-6 p-6 max-w-3xl">
      <h2 class="text-xl font-semibold">Create Gown Session</h2>

      <form @submit.prevent="submit" class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div>
          <label class="block text-sm font-medium text-gray-700">Date</label>
          <input type="date" v-model="form.date" class="mt-1 w-full rounded-md border px-3 py-2 text-sm" />
          <p v-if="form.errors.date" class="text-xs text-red-600 mt-1">{{ form.errors.date }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Location</label>
          <input type="text" v-model="form.location" class="mt-1 w-full rounded-md border px-3 py-2 text-sm" />
          <p v-if="form.errors.location" class="text-xs text-red-600 mt-1">{{ form.errors.location }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Start time (optional)</label>
          <input type="time" v-model="form.start_time" class="mt-1 w-full rounded-md border px-3 py-2 text-sm" />
          <p v-if="form.errors.start_time" class="text-xs text-red-600 mt-1">{{ form.errors.start_time }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">End time (optional)</label>
          <input type="time" v-model="form.end_time" class="mt-1 w-full rounded-md border px-3 py-2 text-sm" />
          <p v-if="form.errors.end_time" class="text-xs text-red-600 mt-1">{{ form.errors.end_time }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Quota</label>
          <input type="number" min="1" v-model.number="form.quota" class="mt-1 w-full rounded-md border px-3 py-2 text-sm" />
          <p v-if="form.errors.quota" class="text-xs text-red-600 mt-1">{{ form.errors.quota }}</p>
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Note (optional)</label>
          <textarea v-model="form.note" rows="3" class="mt-1 w-full rounded-md border px-3 py-2 text-sm"></textarea>
          <p v-if="form.errors.note" class="text-xs text-red-600 mt-1">{{ form.errors.note }}</p>
        </div>

        <div class="md:col-span-2 flex items-center gap-2">
          <Button type="submit" class="bg-indigo-600 text-white hover:bg-indigo-500">Create</Button>
          <Link :href="route('admin.gown-sessions.index')">
            <Button type="button" class="bg-gray-600 text-white hover:bg-gray-500">Back</Button>
          </Link>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
