<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import type { BreadcrumbItem } from '@/types'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Users', href: route('admin.users.index') },
  { title: 'Edit User', href: '#' },
]

interface Session { id: number | string; name: string }

const props = defineProps<{
  user: Record<string, any>
  courses: Array<{ id: number | string; name: string }>
  sessions: Session[]
}>()

const form = useForm({
  name: props.user.name ?? '',
  email: props.user.email ?? '',
  password: '',
  is_admin: props.user.is_admin ? '1' : '0',
  student_id: props.user.student_id ?? '',
  course_id: props.user.course_id ?? '',
})

const submit = () => {
  form.put(route('admin.users.update', props.user.id), {
    preserveScroll: true,
    onSuccess: () => form.reset('password'),
  })
}
</script>

<template>
  <Head title="Edit User" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <h1 class="text-xl font-semibold text-gray-900">Edit User</h1>

      <form @submit.prevent="submit" class="grid max-w-4xl grid-cols-1 gap-6 md:grid-cols-2">
        <!-- Left Column -->
        <div class="space-y-5">
          <!-- Name -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input id="name" v-model="form.name" type="text"
                   class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                   :class="{ 'border-red-500': form.errors.name }" />
            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input id="email" v-model="form.email" type="email"
                   class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                   :class="{ 'border-red-500': form.errors.email }" />
            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password <span class="text-gray-500">(leave blank to keep current)</span></label>
            <input id="password" v-model="form.password" type="password"
                   class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                   :class="{ 'border-red-500': form.errors.password }" />
            <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
          </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-5">
          <!-- Student ID -->
          <div>
            <label for="student_id" class="block text-sm font-medium text-gray-700">Student ID</label>
            <input id="student_id" v-model="form.student_id" type="text"
                   class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                   :class="{ 'border-red-500': form.errors.student_id }" />
            <p v-if="form.errors.student_id" class="mt-1 text-sm text-red-600">{{ form.errors.student_id }}</p>
          </div>

          <!-- Course -->
          <div>
            <label for="course_id" class="block text-sm font-medium text-gray-700">Course</label>
            <select id="course_id" v-model="form.course_id"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                    :class="{ 'border-red-500': form.errors.course_id }">
              <option value="">Select a course</option>
              <option v-for="course in props.courses" :key="course.id" :value="course.id">{{ course.name }}</option>
            </select>
            <p v-if="form.errors.course_id" class="mt-1 text-sm text-red-600">{{ form.errors.course_id }}</p>
          </div>

          <!-- Role -->
          <div>
            <label for="is_admin" class="block text-sm font-medium text-gray-700">Role</label>
            <select id="is_admin" v-model="form.is_admin"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
              <option value="0">User</option>
              <option value="1">Admin</option>
            </select>
          </div>
        </div>

        <!-- Actions -->
        <div class="col-span-1 flex items-center gap-2 md:col-span-2">
          <Button type="submit" class="bg-indigo-600 text-white hover:bg-indigo-500">Update</Button>
          <Link :href="route('admin.users.index')">
            <Button class="bg-gray-600 text-white hover:bg-gray-500">Back</Button>
          </Link>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Two-column edit layout matching Create page */
</style>
