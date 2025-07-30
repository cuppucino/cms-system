<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { Shield } from 'lucide-vue-next';
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
    is_admin: boolean;
}

interface Props {
    admins: User[];
    users: User[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Roles',
        href: '/roles',
    },
];

const page = usePage();

const handleDelete = (id: number) => {
    if (confirm('Are you sure you want to delete this role?')) {
        router.delete(route('roles.destroy', { id }), {
            preserveScroll: true,
            onSuccess: () => {
                // success message or action
            },
        });
    }
};
</script>

<template>
    <Head title="Roles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div>
            <Link :href="route('roles.create')">
                <Button class="cursor-pointer">Create a Role</Button>
            </Link>
        </div>

        <div class="p-4">
            <div v-if="page.props.flash?.message" class="mb-4">
                <Alert class="bg-purple-200 flex items-start gap-3 p-4 rounded-md">
                    <Shield class="h-5 w-5 mt-1 shrink-0 text-purple-700" />
                    <div>
                        <AlertTitle class="text-base font-semibold">Notification</AlertTitle>
                        <AlertDescription class="text-sm text-purple-900">
                            {{ page.props.flash.message }}
                        </AlertDescription>
                    </div>
                </Alert>
            </div>

            <div class="mt-4">
                <Table>
                    <TableCaption>A list of your roles.</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px]">ID</TableHead>
                            <TableHead>Name</TableHead>
                            <TableHead>Guard</TableHead>
                            <TableHead class="text-center">Action</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="user in props.admins" :key="user.id">
                            <TableCell>{{ user.id }}</TableCell>
                            <TableCell class="font-medium">{{ user.name }}</TableCell>
                            <TableCell>{{ user.email }}</TableCell>
                            <TableCell class="text-center space-x-2">
                                <Link :href="route('users.edit', { id: user.id })">
                                    <Button class="bg-slate-600">Edit</Button>
                                </Link>
                                <Link :href="route('users.show', { id: user.id })">
                                    <Button class="bg-green-600">Show</Button>
                                </Link>
                                <Button class="bg-red-600" @click="handleDelete(user.id)">Delete</Button>
                            </TableCell>
                        </TableRow>

                        <TableRow v-for="user in props.users" :key="user.id">
                            <TableCell>{{ user.id }}</TableCell>
                            <TableCell class="font-medium">{{ user.name }}</TableCell>
                            <TableCell>{{ user.email }}</TableCell>
                            <TableCell class="text-center space-x-2">
                                <Link :href="route('users.edit', { id: user.id })">
                                    <Button class="bg-slate-600">Edit</Button>
                                </Link>
                                <Link :href="route('users.show', { id: user.id })">
                                    <Button class="bg-green-600">Show</Button>
                                </Link>
                                <Button class="bg-red-600" @click="handleDelete(user.id)">Delete</Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
