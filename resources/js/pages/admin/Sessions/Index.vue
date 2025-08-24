<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { Rocket } from 'lucide-vue-next';

interface Session {
  id: number;
  name: string;
  date: string;
  location: string;
  quota: number;
  users_count?: number;
}

const props = defineProps<{ sessions: Session[] }>();
const page = usePage();

const handleDelete = (id: number) => {
  if (confirm('Are you sure you want to delete this session?')) {
    router.delete(route('admin.sessions.destroy', { session: id }));
  }
};
</script>

<template>
  <Head title="Convocation Sessions" />

  <AppLayout :breadcrumbs="[{ title: 'Sessions', href: route('admin.sessions.index') }]">

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

    <!-- Add Session -->
    <div class="mb-4">
      <Link :href="route('admin.sessions.create')">
        <Button class="bg-blue-600">Create Session</Button>
      </Link>
    </div>

    <!-- Table -->
    <table class="w-full border-collapse border border-gray-300">
      <thead class="bg-gray-100">
        <tr>
          <th class="border px-4 py-2">Name</th>
          <th class="border px-4 py-2">Date</th>
          <th class="border px-4 py-2">Location</th>
          <th class="border px-4 py-2">Quota</th>
          <th class="border px-4 py-2">Registered</th>
          <th class="border px-4 py-2 text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="s in props.sessions" :key="s.id">
          <td class="border px-4 py-2">{{ s.name }}</td>
          <td class="border px-4 py-2">{{ s.date }}</td>
          <td class="border px-4 py-2">{{ s.location }}</td>
          <td class="border px-4 py-2">{{ s.quota }}</td>
          <td class="border px-4 py-2">
            {{ s.users_count ?? 0 }} / {{ s.quota }}
          </td>
          <td class="border px-4 py-2 text-center space-x-2">
            <Link :href="route('admin.sessions.edit', { session: s.id })">
              <Button class="bg-slate-600">Edit</Button>
            </Link>
            <Button class="bg-red-600" @click="handleDelete(s.id)">Delete</Button>
          </td>
        </tr>
      </tbody>
    </table>
  </AppLayout>
</template>
