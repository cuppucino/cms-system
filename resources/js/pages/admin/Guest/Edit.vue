<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })

const props = defineProps<{
    guest: {
        id: number
        student_name: string
        guest_name: string
        user_id: number
    }
    students: { id: number; name: string; email: string }[]
}>()

const form = useForm({
    user_id: props.guest.user_id,
    name: props.guest.guest_name,
})

const submit = () => {
    form.put(route('admin.guests.update', props.guest.id))
}
</script>

<template>
  <Head title="Edit Guest" />

  <div class="p-6 bg-white rounded-xl shadow">
    <h1 class="text-xl font-semibold mb-4">Edit Guest for {{ props.guest.student_name }}</h1>

    <form @submit.prevent="submit">
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Student</label>
        <select v-model="form.user_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
          <option v-for="student in students" :key="student.id" :value="student.id">{{ student.name }} ({{ student.email }})</option>
        </select>
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Guest Name</label>
        <input v-model="form.name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
      </div>

      <div class="flex space-x-4">
        <button type="submit" :disabled="form.processing" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Update</button>
        <Link :href="route('admin.guest.destroy', props.guest.id)" method="delete" as="button" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Delete</Link>
        <Link :href="route('admin.guest.index')" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</Link>
      </div>

      <div v-if="form.errors" class="mt-2 text-red-600 text-sm">
        <p v-for="(error, key) in form.errors" :key="key">{{ error }}</p>
      </div>
    </form>
  </div>
</template>
