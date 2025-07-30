<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Roles',
        href: '/admin/roles',
    },
    {
        title: 'Create',
        href: '/admin/roles/create',
    },
];

// Inertia form: creating a new user with role
const form = useForm({
    name: '',
    email: '',
    password: '',
    is_admin: '0', // default to User
});
</script>

<template>
    <Head title="Create Role / User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 max-w-md mx-auto">
            <h1 class="text-xl font-bold mb-4">Create User with Role</h1>

            <form @submit.prevent="form.post(route('admin.users.store'))" class="space-y-4">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium">Name</label>
                    <input
                        v-model="form.name"
                        type="text"
                        id="name"
                        class="mt-1 block w-full border border-gray-300 rounded px-3 py-2"
                    />
                    <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">
                        {{ form.errors.name }}
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        id="email"
                        class="mt-1 block w-full border border-gray-300 rounded px-3 py-2"
                    />
                    <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">
                        {{ form.errors.email }}
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium">Password</label>
                    <input
                        v-model="form.password"
                        type="password"
                        id="password"
                        class="mt-1 block w-full border border-gray-300 rounded px-3 py-2"
                    />
                    <div v-if="form.errors.password" class="text-red-600 text-sm mt-1">
                        {{ form.errors.password }}
                    </div>
                </div>

                <!-- Role (is_admin boolean) -->
                <div>
                    <label for="is_admin" class="block text-sm font-medium">Role</label>
                    <select
                        v-model="form.is_admin"
                        id="is_admin"
                        class="mt-1 block w-full border border-gray-300 rounded px-3 py-2"
                    >
                        <option value="0">User</option>
                        <option value="1">Admin</option>
                    </select>
                    <div v-if="form.errors.is_admin" class="text-red-600 text-sm mt-1">
                        {{ form.errors.is_admin }}
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex items-center space-x-2">
                    <Button type="submit" class="bg-blue-600 text-white">Create</Button>
                    <Link :href="route('users.index')">
                        <Button class="bg-gray-600 text-white">Back</Button>
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
