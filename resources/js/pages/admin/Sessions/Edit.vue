<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'

interface Session {
  id: number
  name: string
  date: string
  location: string
  quota: number
}

const props = defineProps<{ session: Session }>()

const form = useForm({
  name: props.session.name,
  date: props.session.date,
  location: props.session.location,
  quota: props.session.quota,
})

const submit = () => {
  form.put(route('admin.sessions.update', { session: props.session.id }))
}
</script>

<template>
  <Head title="Edit Session" />

  <AppLayout :breadcrumbs="[{ title: 'Sessions', href: route('admin.sessions.index') }, { title: 'Edit', href: '#' }]">
    <div class="space-y-6 p-6">
      <h2 class="text-xl font-semibold text-gray-900">Edit Convocation Session</h2>

      <form @submit.prevent="submit" class="grid max-w-3xl grid-cols-1 gap-6 md:grid-cols-2">
        <!-- Name -->
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700" for="name">Name</label>
          <input id="name" v-model="form.name" type="text"
                 class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                 :class="{ 'border-red-500': form.errors.name }" required />
          <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
        </div>

        <!-- Date -->
        <div>
          <label class="block text-sm font-medium text-gray-700" for="date">Date</label>
          <input id="date" v-model="form.date" type="date"
                 class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                 :class="{ 'border-red-500': form.errors.date }" required />
          <p v-if="form.errors.date" class="mt-1 text-sm text-red-600">{{ form.errors.date }}</p>
        </div>

        <!-- Location -->
        <div>
          <label class="block text-sm font-medium text-gray-700" for="location">Location</label>
          <input id="location" v-model="form.location" type="text"
                 class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                 :class="{ 'border-red-500': form.errors.location }" required />
          <p v-if="form.errors.location" class="mt-1 text-sm text-red-600">{{ form.errors.location }}</p>
        </div>

        <!-- Quota -->
        <div>
          <label class="block text-sm font-medium text-gray-700" for="quota">Quota</label>
          <input id="quota" v-model.number="form.quota" type="number" min="1"
                 class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                 :class="{ 'border-red-500': form.errors.quota }" required />
          <p v-if="form.errors.quota" class="mt-1 text-sm text-red-600">{{ form.errors.quota }}</p>
        </div>

        <!-- Actions -->
        <div class="md:col-span-2 flex items-center gap-2">
          <Button type="submit" class="bg-indigo-600 text-white hover:bg-indigo-500">Update</Button>
          <Link :href="route('admin.sessions.index')">
            <Button type="button" class="bg-gray-600 text-white hover:bg-gray-500">Back</Button>
          </Link>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Two-column edit form consistent with create page */
</style>
