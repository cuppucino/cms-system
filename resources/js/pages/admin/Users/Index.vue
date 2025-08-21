<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { Rocket } from 'lucide-vue-next';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

interface User {
    id: number;
    name: string;
    email: string;
    role: string; // Example field for role (e.g., Admin, User)
}

interface Props {
    users: User[];
}

// Get props from Inertia
const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
];

const page = usePage()

const handleDelete = (id: number) => {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(route('users.destroy', { id }), {
            preserveScroll: true,
            onSuccess: () => {
                // Optionally, you can show a success message or perform other actions
            },
        });
    }
};
</script>

<template>

    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div>
            <Link :href="route('admin.users.create')">
            <Button class="cursor-pointer">Create a User</Button></Link>
        </div>

        <div class="p-4">
            <div v-if="page.props.flash?.message" class="mb-4">
                <!-- Pop out Notification-->
                <Alert class="bg-blue-200 flex items-start gap-3 p-4 rounded-md">
                    <Rocket class="h-5 w-5 mt-1 shrink-0 text-blue-700" />

                    <div>
                        <AlertTitle class="text-base font-semibold">Notification</AlertTitle>
                        <AlertDescription class="text-sm text-blue-900">
                            {{ page.props.flash.message }}
                        </AlertDescription>
                    </div>
                </Alert>
                <!-- End Pop out Notification-->
            </div>



            <div class="mt-4">
                <Table>
                    <TableCaption>A list of your users.</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px]">
                                ID
                            </TableHead>
                            <TableHead>Name</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Role</TableHead>
                            <TableHead class="text-center">
                                Action
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="user in props.users" :key="user.id">
                            <TableCell>{{ user.id }}</TableCell>
                            <TableCell class="font-medium">{{ user.name }}</TableCell>
                            <TableCell>{{ user.email }}</TableCell>
                            <TableCell>{{ user.role }}</TableCell>
                            <TableCell class="text-center space-x-2">
                                <!-- Edit Button -->
                                <Link :href="route('admin.users.edit', { id: user.id })">
                                <Button class="bg-slate-600">Edit</Button>
                                </Link>

                                <!-- Show Button -->
                                <Link :href="route('admin.users.show', { id: user.id })">
                                <Button class="bg-green-600">Show</Button>
                                </Link>

                                <!-- Delete Button -->
                                <Button class="bg-red-600" @click="handleDelete(user.id)">Delete</Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

        </div>
    </AppLayout>
</template>
