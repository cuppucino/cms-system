<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
  Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow,
} from '@/components/ui/table';
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { Rocket } from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';

interface Stock {
  id: number;
  size: string;
  total: number;
  issued: number;
  available: number;
}

interface Props {
  stock: Stock[];
}

const props = defineProps<Props>();
const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Gown Management', href: '/admin/gowns' },
];

const handleDelete = (id: number) => {
  if (confirm('Are you sure you want to delete this stock?')) {
    router.delete(route('admin.gowns.destroy', { gown: id }), { preserveScroll: true });
  }
};
</script>

<template>
  <Head title="Gown Management" />
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
    <!-- End Flash Notification -->

    <div class="mb-4">
      <Link :href="route('admin.gowns.create')">
        <Button class="cursor-pointer">Add Gown Stock</Button>
      </Link>
    </div>

    <div class="mt-4">
      <Table>
        <TableCaption>A list of gown stock.</TableCaption>
        <TableHeader>
          <TableRow>
            <TableHead>Size</TableHead>
            <TableHead>Total</TableHead>
            <TableHead>Issued</TableHead>
            <TableHead>Available</TableHead>
            <TableHead class="text-center">Action</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="s in props.stock" :key="s.id">
            <TableCell>{{ s.size }}</TableCell>
            <TableCell>{{ s.total }}</TableCell>
            <TableCell>{{ s.issued }}</TableCell>
            <TableCell>{{ s.available }}</TableCell>
            <TableCell class="text-center space-x-2">
              <Link :href="route('admin.gowns.edit', { gown: s.id })">
                <Button class="bg-slate-600">Edit</Button>
              </Link>
              <Button class="bg-red-600" @click="handleDelete(s.id)">Delete</Button>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
  </AppLayout>
</template>
