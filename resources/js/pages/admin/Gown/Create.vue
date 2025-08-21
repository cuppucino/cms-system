<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';

const form = useForm({
  size: '',
  total: 0,
});

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Gown Management', href: '/admin/gowns' },
  { title: 'Add Stock', href: '/admin/gowns/create' },
];

const submit = () => {
  form.post(route('admin.gowns.store'));
};
</script>

<template>
  <Head title="Add Gown Stock" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <form @submit.prevent="submit" class="space-y-4 max-w-md">
      <div>
        <label for="size" class="block font-semibold">Size</label>
        <select v-model="form.size" id="size" class="border p-2 w-full rounded">
          <option disabled value="">Select size</option>
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

      <Button type="submit" class="bg-blue-600 text-white">Save</Button>
    </form>
  </AppLayout>
</template>
