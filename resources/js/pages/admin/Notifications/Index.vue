<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { Rocket } from 'lucide-vue-next';

interface Notification {
  id: number;
  user_name: string;
  title: string;
  message: string;
  is_read: boolean;
  created_at: string;
}

const props = defineProps<{ notifications: Notification[] }>();
const page = usePage();
</script>

<template>
  <Head title="Notifications" />

  <AppLayout :breadcrumbs="[{ title: 'Notifications', href: route('admin.notifications.index') }]">

    <!-- Flash message -->
    <div v-if="page.props.flash?.message" class="mb-4">
      <Alert class="bg-blue-200 flex items-start gap-3 p-4 rounded-md">
        <Rocket class="h-5 w-5 mt-1 shrink-0 text-blue-700" />
        <div>
          <AlertTitle>Notification</AlertTitle>
          <AlertDescription>{{ page.props.flash.message }}</AlertDescription>
        </div>
      </Alert>
    </div>

    <!-- Add Notification -->
    <div class="mb-4">
      <Link :href="route('admin.notifications.create')">
        <Button class="bg-blue-600">Send Notification</Button>
      </Link>
    </div>

    <!-- Table -->
    <table class="w-full border-collapse border border-gray-300">
      <thead class="bg-gray-100">
        <tr>
          <th class="border px-4 py-2">To</th>
          <th class="border px-4 py-2">Title</th>
          <th class="border px-4 py-2">Message</th>
          <th class="border px-4 py-2">Read?</th>
          <th class="border px-4 py-2">Sent At</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="n in props.notifications" :key="n.id">
          <td class="border px-4 py-2">{{ n.user_name }}</td>
          <td class="border px-4 py-2">{{ n.title }}</td>
          <td class="border px-4 py-2">{{ n.message }}</td>
          <td class="border px-4 py-2">
            <span :class="n.is_read ? 'text-green-600' : 'text-red-600'">
              {{ n.is_read ? 'Read' : 'Unread' }}
            </span>
          </td>
          <td class="border px-4 py-2">{{ n.created_at }}</td>
        </tr>
      </tbody>
    </table>
  </AppLayout>
</template>
