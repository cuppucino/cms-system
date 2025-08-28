<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import StudentLayout from '@/layouts/StudentLayout.vue'
defineOptions({ layout: StudentLayout })

const props = defineProps<{
  user: { name:string; email:string; student_id?:string|null; course_id?:number|null; gown_size?:string|null },
  courses: Array<{ id:number; name:string }>,
  sizes: string[],
}>()

const form = useForm({
  name: props.user.name ?? '',
  student_id: props.user.student_id ?? '',
  course_id: props.user.course_id ?? null,
  gown_size: props.user.gown_size ?? null,
})

const submit = () => form.put(route('student.profile.update'))
</script>

<template>
  <Head title="Profile" />
  <div class="rounded-xl border bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
    <h1 class="text-lg font-semibold">Profile</h1>

    <form @submit.prevent="submit" class="mt-4 grid gap-4 md:grid-cols-2">
      <div>
        <label class="block text-sm font-medium">Full Name</label>
        <input v-model="form.name" type="text" class="mt-1 w-full rounded-lg border px-3 py-2 dark:bg-gray-900" />
        <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Email</label>
        <input :value="props.user.email" type="email" disabled class="mt-1 w-full rounded-lg border bg-gray-100 px-3 py-2 dark:bg-gray-800" />
      </div>

      <div>
        <label class="block text-sm font-medium">Student ID</label>
        <input v-model="form.student_id" type="text" class="mt-1 w-full rounded-lg border px-3 py-2 dark:bg-gray-900" />
        <div v-if="form.errors.student_id" class="mt-1 text-sm text-red-600">{{ form.errors.student_id }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Course</label>
        <select v-model="form.course_id" class="mt-1 w-full rounded-lg border px-3 py-2 dark:bg-gray-900">
          <option :value="null">-- Select --</option>
          <option v-for="c in props.courses" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
        <div v-if="form.errors.course_id" class="mt-1 text-sm text-red-600">{{ form.errors.course_id }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Gown Size</label>
        <select v-model="form.gown_size" class="mt-1 w-full rounded-lg border px-3 py-2 dark:bg-gray-900">
          <option :value="null">-- Select --</option>
          <option v-for="s in props.sizes" :key="s" :value="s">{{ s }}</option>
        </select>
        <div v-if="form.errors.gown_size" class="mt-1 text-sm text-red-600">{{ form.errors.gown_size }}</div>
      </div>

      <div class="md:col-span-2">
        <button :disabled="form.processing" class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-60">
          Save changes
        </button>
      </div>
    </form>
  </div>
</template>
