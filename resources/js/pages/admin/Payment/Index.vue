<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'

const props = defineProps<{
  payments: {
    id: number
    reference: string
    student: string
    gown: string
    amount: number
    status: string
    created_at: string
  }[]
}>()
</script>

<template>
  <Head title="Payments" />

  <AppLayout :breadcrumbs="[{ title: 'Payments', href: '#' }]">
    <div class="p-6 bg-white rounded-lg shadow">
      <h2 class="text-xl font-semibold mb-4">Payment Records</h2>

      <Table>
        <TableCaption>All gown payments by students</TableCaption>
        <TableHeader>
          <TableRow>
            <TableHead>ID</TableHead>
            <TableHead>Reference</TableHead>
            <TableHead>Student</TableHead>
            <TableHead>Gown Size</TableHead>
            <TableHead>Amount</TableHead>
            <TableHead>Status</TableHead>
            <TableHead>Date</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="p in props.payments" :key="p.id">
            <TableCell>{{ p.id }}</TableCell>
            <TableCell>{{ p.reference }}</TableCell>
            <TableCell>{{ p.student }}</TableCell>
            <TableCell>{{ p.gown }}</TableCell>
            <TableCell>RM {{ p.amount }}</TableCell>
            <TableCell>
              <span :class="p.status === 'paid' ? 'text-green-600' : 'text-red-600'">
                {{ p.status }}
              </span>
            </TableCell>
            <TableCell>{{ p.created_at }}</TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
  </AppLayout>
</template>
