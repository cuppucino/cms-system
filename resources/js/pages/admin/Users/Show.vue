<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users Show',
        href: '/users',
    },
];

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});




</script>

<template>
    <Head title="Users Show" />
    <AppLayout :breadcrumbs="breadcrumbs">


        <div class="flex items-center space-x-2">

                    <Link :href="route('users.index')">
                        <Button class="bg-gray-600 text-white">Back</Button>
                    </Link>
                </div>

                <div>
                    <p><strong>Name:</strong> {{ props.user.name }}</p>
                    <p><strong>Email:</strong> {{ props.user.email }}</p>
                    <p><strong>Role:</strong> {{ props.user.role }}</p>
                </div>
    </AppLayout>
</template>
