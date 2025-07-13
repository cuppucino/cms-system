<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Create a Product',
        href: '/products/create',
    },
];

const form = useForm({
    name: '',
    price: 0,
    description: '',
});

const handleSubmit = () => {
    form.post(route('products.store'))
};
</script>

<template>

    <Head title="Create a Product" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <form @submit.prevent="handleSubmit" class="w-8/12 space-y-4">
                <div class="space-y-4">
                    <Label for="Product name">Name</Label>
                    <Input v-model="form.name" placeholder="Name"></Input>
                    <div class="text-sm text-red-600" v-if="form.errors.name">{{ form.errors.name }}</div>
                </div>

                <div class="space-y-4">
                    <Label for="Product price">Price</Label>
                    <Input v-model="form.price" placeholder="Price"></Input>
                    <div class="text-sm text-red-600" v-if="form.errors.price">{{ form.errors.price }}</div>
                </div>

                <div class="space-y-4">
                    <Label for="Product description">Description</Label>
                    <Input v-model="form.description" placeholder="Description"></Input>
                    <div class="text-sm text-red-600" v-if="form.errors.description">{{ form.errors.description }}</div>
                </div>

                <Button type="submit" :disabled="form.processing">Add a Product</Button>

            </form>
        </div>
    </AppLayout>
</template>
