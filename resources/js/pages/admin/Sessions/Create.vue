<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = ref({
  name: '',
  date: '',
  location: '',
  quota: 0,
});

const submit = () => {
  router.post(route('admin.sessions.store'), form.value);
};
</script>

<template>
  <Head title="Create Session" />

  <AppLayout :breadcrumbs="[{ title: 'Sessions', href: route('admin.sessions.index') }, { title: 'Create', href: '#' }]">
    <div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
      <h2 class="text-xl font-semibold mb-4">Create Convocation Session</h2>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium">Name</label>
          <input v-model="form.name" type="text" class="w-full border p-2 rounded" required />
        </div>

        <div>
          <label class="block text-sm font-medium">Date</label>
          <input v-model="form.date" type="date" class="w-full border p-2 rounded" required />
        </div>

        <div>
          <label class="block text-sm font-medium">Location</label>
          <input v-model="form.location" type="text" class="w-full border p-2 rounded" required />
        </div>

        <div>
          <label class="block text-sm font-medium">Quota</label>
          <input v-model="form.quota" type="number" min="1" class="w-full border p-2 rounded" required />
        </div>

        <Button class="bg-blue-600 w-full">Create</Button>
      </form>
    </div>
  </AppLayout>
</template>
