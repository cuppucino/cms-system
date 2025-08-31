<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'

const props = defineProps<{ course: { id:number; name:string; code:string; faculty?:string } }>()

const form = useForm({
  name: props.course.name,
  code: props.course.code,
  faculty: props.course.faculty || ''
})
</script>

<template>
  <Head title="Edit Course" />

  <AppLayout :breadcrumbs="[{ title: 'Courses', href: route?.('admin.courses.index') ?? '#' }, { title: 'Edit', href: '#' }]">
    <div class="space-y-6 p-6 max-w-2xl">
      <h1 class="text-xl font-semibold text-gray-900">Edit Course</h1>

      <form @submit.prevent="form.put(route('admin.courses.update', props.course.id))" class="grid grid-cols-1 gap-6">
        <!-- Name -->
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
          <input id="name" v-model="form.name" type="text"
                 class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" />
          <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
        </div>

        <!-- Code -->
        <div>
          <label for="code" class="block text-sm font-medium text-gray-700">Code</label>
          <input id="code" v-model="form.code" type="text"
                 class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" />
          <p v-if="form.errors.code" class="mt-1 text-sm text-red-600">{{ form.errors.code }}</p>
        </div>

        <!-- Faculty -->
        <div>
          <label for="faculty" class="block text-sm font-medium text-gray-700">Faculty</label>
          <input id="faculty" v-model="form.faculty" type="text"
                 class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" />
          <p v-if="form.errors.faculty" class="mt-1 text-sm text-red-600">{{ form.errors.faculty }}</p>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-2 pt-2">
          <Button type="submit" class="bg-indigo-600 text-white hover:bg-indigo-500">Update</Button>
          <Link :href="route('admin.courses.index')">
            <Button type="button" class="bg-gray-600 text-white hover:bg-gray-500">Cancel</Button>
          </Link>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Consistent with Create form styling */
</style>
