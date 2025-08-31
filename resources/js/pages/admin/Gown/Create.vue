<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import { Head, useForm } from '@inertiajs/vue3'
import type { BreadcrumbItem } from '@/types'

const form = useForm({ size: '', total: 0 })

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Gown Management', href: '/admin/gowns' },
    { title: 'Add Stock', href: '/admin/gowns/create' },
]

const submit = () => {
    form.post(route('admin.gowns.store'))
}
</script>

<template>

    <Head title="Add Gown Stock" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <h1 class="text-xl font-semibold text-gray-900">Add Gown Stock</h1>

            <form @submit.prevent="submit" class="grid max-w-2xl grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Size -->
                <div>
                    <label for="size" class="block text-sm font-medium text-gray-700">Size</label>
                    <select id="size" v-model="form.size"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                        :class="{ 'border-red-500': form.errors.size }">
                        <option disabled value="">Select size</option>
                        <option value="XS">XS</option>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                    </select>
                    <p v-if="form.errors.size" class="mt-1 text-sm text-red-600">{{ form.errors.size }}</p>
                </div>

                <!-- Total -->
                <div>
                    <label for="total" class="block text-sm font-medium text-gray-700">Total</label>
                    <input id="total" type="number" v-model="form.total" min="1"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                        :class="{ 'border-red-500': form.errors.total }" />
                    <p v-if="form.errors.total" class="mt-1 text-sm text-red-600">{{ form.errors.total }}</p>
                </div>

                <!-- Actions -->
                <div class="col-span-1 md:col-span-2 flex items-center gap-2">
                    <Button type="submit" class="bg-indigo-600 text-white hover:bg-indigo-500">Save</Button>
                    <Button type="reset" class="bg-gray-600 text-white hover:bg-gray-500"
                        @click="form.reset()">Reset</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Two-column form layout consistent with other admin forms */
</style>
