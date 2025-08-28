<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import { Head, router, usePage } from '@inertiajs/vue3'

const props = defineProps<{ gown: { id: number; name: string; fee: number } }>()

const handlePay = () => {
  router.post(route('student.payments.store'), {
    gown_id: props.gown.id,
    amount: props.gown.fee,
  })
}
</script>

<template>
  <Head title="Make Payment" />

  <AppLayout :breadcrumbs="[{ title: 'Payment', href: '#' }]">
    <div class="max-w-lg mx-auto bg-white shadow rounded-lg p-6">
      <h2 class="text-xl font-semibold mb-4">Payment for {{ props.gown.name }}</h2>

      <p class="mb-2">Fee: <span class="font-bold">RM {{ props.gown.fee }}</span></p>
      <p class="mb-4 text-gray-600">This is a dummy payment simulation.</p>

      <Button class="bg-blue-600 text-white px-4 py-2" @click="handlePay">
        Pay Now
      </Button>
    </div>
  </AppLayout>
</template>
