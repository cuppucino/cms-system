<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Student {
  id: number;
  name: string;
  email: string;
}

const props = defineProps<{ students: Student[] }>();

const form = ref({
  user_ids: [] as number[],
  title: '',
  message: '',
  send_email: false,
});

const submit = () => {
  router.post(route('admin.notifications.store'), form.value);
};
</script>

<template>
  <Head title="Send Notification" />

  <AppLayout :breadcrumbs="[{ title: 'Notifications', href: route('admin.notifications.index') }]">

    <h1 class="text-xl font-bold mb-4">Send Notification</h1>

    <form @submit.prevent="submit" class="space-y-4">

      <!-- Recipients -->
      <div>
        <label class="block font-medium">Select Students</label>
        <select v-model="form.user_ids" multiple class="w-full border rounded p-2">
          <option v-for="s in props.students" :key="s.id" :value="s.id">
            {{ s.name }} ({{ s.email }})
          </option>
        </select>
        <small class="text-gray-500">Hold CTRL/CMD to select multiple</small>
      </div>

      <!-- Title -->
      <div>
        <label class="block font-medium">Title</label>
        <input v-model="form.title" type="text" class="w-full border rounded p-2" />
      </div>

      <!-- Message -->
      <div>
        <label class="block font-medium">Message</label>
        <textarea v-model="form.message" rows="4" class="w-full border rounded p-2"></textarea>
      </div>

      <!-- Email Option -->
      <div>
        <label class="flex items-center space-x-2">
          <input type="checkbox" v-model="form.send_email" />
          <span>Also send via Email</span>
        </label>
      </div>

      <!-- Submit -->
      <div>
        <Button type="submit" class="bg-blue-600">Send</Button>
      </div>

    </form>
  </AppLayout>
</template>
