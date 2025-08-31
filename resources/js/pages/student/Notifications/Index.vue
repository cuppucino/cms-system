<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'

defineProps<{
  notifications: Array<any>
}>();
</script>

<template>
  <div class="max-w-2xl mx-auto space-y-4">
    <h1 class="text-xl font-bold mb-4">Notifications</h1>

    <div v-if="notifications.length === 0" class="text-gray-500">
      No notifications yet.
    </div>

    <div v-for="n in notifications" :key="n.id"
         class="p-4 rounded-lg border shadow-sm"
         :class="n.is_read ? 'bg-gray-50' : 'bg-blue-50'">
      <h2 class="font-semibold">{{ n.title }}</h2>
      <p class="text-sm text-gray-700">{{ n.message }}</p>
      <div class="flex justify-between items-center mt-2 text-xs text-gray-500">
        <span>{{ n.created_at }}</span>
        <form :action="route('student.notifications.read', n.id)" method="post">
          <button type="submit" class="text-blue-600 hover:underline" v-if="!n.is_read">
            Mark as read
          </button>
        </form>
      </div>
    </div>
  </div>
</template>
