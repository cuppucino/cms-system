<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'

const breadcrumbs = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
]
</script>

<template>
  <Head title="Student Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-4 p-4">
      <h1 class="text-xl font-bold text-blue-600">🎓 Student Dashboard</h1>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="aspect-video bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center text-gray-500">
          Placeholder 1
        </div>
        <div class="aspect-video bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center text-gray-500">
          Placeholder 2
        </div>
        <div class="aspect-video bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center text-gray-500">
          Placeholder 3
        </div>
      </div>

      <div class="min-h-[300px] bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center text-gray-500 mt-4">
        Main Panel Placeholder
      </div>
    </div>
  </AppLayout>
</template>
