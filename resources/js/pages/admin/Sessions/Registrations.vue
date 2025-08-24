<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { Rocket } from 'lucide-vue-next';

interface Registration {
  id: number;
  student_name: string;
  session_name: string;
  guest_count: number;
}

const props = defineProps<{ registrations: Registration[] }>();
const page = usePage();
</script>

<template>
  <Head title="Session Registrations" />

  <AppLayout :breadcrumbs="[{ title: 'Registrations', href: route('admin.registrations.index') }]">
    <!-- Flash messages -->
    <div v-if="page.props.flash?.message" class="mb-4">
      <Alert class="bg-blue-200 flex items-start gap-3 p-4 rounded-md">
        <Rocket class="h-5 w-5 mt-1 shrink-0 text-blue-700" />
        <div>
          <AlertTitle>Notification</AlertTitle>
          <AlertDescription>{{ page.props.flash.message }}</AlertDescription>
        </div>
      </Alert>
    </div>

    <!-- Registrations Table -->
    <div class="mt-4">
      <table class="w-full border border-gray-300 rounded-md">
        <thead class="bg-gray-100">
          <tr>
            <th class="border px-4 py-2">ID</th>
            <th class="border px-4 py-2">Student</th>
            <th class="border px-4 py-2">Session</th>
            <th class="border px-4 py-2 text-center">Guests</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in props.registrations" :key="r.id" class="hover:bg-gray-50">
            <td class="border px-4 py-2">{{ r.id }}</td>
            <td class="border px-4 py-2">{{ r.student_name }}</td>
            <td class="border px-4 py-2">{{ r.session_name }}</td>
            <td class="border px-4 py-2 text-center">{{ r.guest_count }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </AppLayout>
</template>
