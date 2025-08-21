<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { Rocket } from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';

interface Collection {
  id: number;
  size: string;
  status: string;
  collection_date: string | null;
  return_date: string | null;
  user: {
    id: number;
    name: string;
    email: string;
  };
}

const props = defineProps<{ collections: Collection[] }>();
const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Gown Collections', href: '/admin/gown-collections' },
];

const updateStatus = (id: number, status: string) => {
  router.put(route('admin.gown-collections.update', { gown_collection: id }), { status });
};
</script>

<template>
  <Head title="Gown Collections" />

  <AppLayout :breadcrumbs="breadcrumbs">

    <!-- Flash Notification -->
    <div v-if="page.props.flash?.message" class="mb-4">
      <Alert class="bg-blue-200 flex items-start gap-3 p-4 rounded-md">
        <Rocket class="h-5 w-5 mt-1 shrink-0 text-blue-700" />
        <div>
          <AlertTitle class="text-base font-semibold">Notification</AlertTitle>
          <AlertDescription class="text-sm text-blue-900">
            {{ page.props.flash.message }}
          </AlertDescription>
        </div>
      </Alert>
    </div>

    <table class="w-full border-collapse border border-gray-300">
      <thead class="bg-gray-100">
        <tr>
          <th class="border px-4 py-2">Student</th>
          <th class="border px-4 py-2">Size</th>
          <th class="border px-4 py-2">Status</th>
          <th class="border px-4 py-2">Collected At</th>
          <th class="border px-4 py-2">Returned At</th>
          <th class="border px-4 py-2 text-center">Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="c in props.collections" :key="c.id">
          <td class="border px-4 py-2">{{ c.user.name }} ({{ c.user.email }})</td>
          <td class="border px-4 py-2">{{ c.size }}</td>
          <td class="border px-4 py-2">{{ c.status }}</td>
          <td class="border px-4 py-2">{{ c.collection_date ?? '-' }}</td>
          <td class="border px-4 py-2">{{ c.return_date ?? '-' }}</td>
          <td class="border px-4 py-2 text-center space-x-2">
            <Button class="bg-green-600" @click="updateStatus(c.id, 'collected')">Mark Collected</Button>
            <Button class="bg-yellow-600" @click="updateStatus(c.id, 'returned')">Mark Returned</Button>
            <Button class="bg-red-600" @click="updateStatus(c.id, 'late')">Mark Late</Button>
          </td>
        </tr>
      </tbody>
    </table>

  </AppLayout>
</template>
