<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';

interface Gown {
  id: number;
  size: string;
  total: number;
  issued: number;
  available: number;
}

const props = defineProps<{ gown: Gown }>();

const form = useForm({
  size: props.gown.size,
  total: props.gown.total,
});

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Gown Management', href: '/admin/gowns' },
  { title: 'Edit Stock', href: `/admin/gowns/${props.gown.id}/edit` },
];

const submit = () => {
  form.put(route('admin.gowns.update', { gown: props.gown.id }));
};
</script>

<template>
  <Head title="Edit Gown Stock" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <form @submit.prevent="submit" class="space-y-4 max-w-md">
      <div>
        <label for="size" class="block font-semibold">Size</label>
        <select v-model="form.size" id="size" class="border p-2 w-full rounded">
          <option value="XS">XS</option>
          <option value="S">S</option>
          <option value="M">M</option>
          <option value="L">L</option>
          <option value="XL">XL</option>
        </select>
        <div v-if="form.errors.size" class="text-red-600">{{ form.errors.size }}</div>
      </div>

      <div>
        <label for="total" class="block font-semibold">Total</label>
        <input
          type="number"
          id="total"
          v-model="form.total"
          class="border p-2 w-full rounded"
          min="1"
        />
        <div v-if="form.errors.total" class="text-red-600">{{ form.errors.total }}</div>
      </div>

      <Button type="submit" class="bg-blue-600 text-white">Update</Button>
    </form>
  </AppLayout>
</template>
