<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm} from '@inertiajs/vue3';



const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users Create',
        href: '/users',
    },
];

// Using Inertia useForm for reactive form state and errors
const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'User',
    is_admin: '0', // Add is_admin property, default to '0' (User)
});

// Submit handler
// const submit = () => {
//     form.post(route('users.store'), {
//         onSuccess: () => {
//             form.reset(); // Clear form after success
//         },
//     });
// };
</script>


<template>

    <Head title="Users Create" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 max-w-md mx-auto">
            <h1 class="text-xl font-bold mb-4">Create User</h1>

            <form @submit.prevent="form.post(route('users.store'))" class="space-y-4">

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

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium">Role</label>
                    <select
                        v-model="form.is_admin"
                        id="is_admin"
                        class="mt-1 block w-full border border-gray-300 rounded px-3 py-2"
                    >
                        <option value="0">User</option>
                        <option value="1">Admin</option>
                        <!-- Add other roles if needed -->
                    </select>
                    <div v-if="form.errors.role" class="text-red-600 text-sm mt-1">
                        {{ form.errors.role }}
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
