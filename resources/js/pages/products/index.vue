<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert"
import { Rocket } from 'lucide-vue-next';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/Table';

interface Product {
    id: number,
    name: string,
    price: number,
    description: string,
}

interface Props {
    products: Product[];
}

//Get props from Inertia
const props = defineProps<Props>();


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: '/products',
    },
];

const page = usePage()

const handleDelete = (id: number) => {
    if (confirm('Are you sure you want to delete this product?')) {
        router.delete(route('products.destroy', { id }), {
            preserveScroll: true,
            onSuccess: () => {
                // Optionally, you can show a success message or perform other actions
            },
        });
    }
};

</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
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

            <div>
                <Link :href="route('products.create')"><Button>Create a product</Button></Link>
            </div>

            <div>
                <Table>
                    <TableCaption>A list of your recent products.</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px]">
                                ID
                            </TableHead>
                            <TableHead>Name</TableHead>
                            <TableHead>Price</TableHead>
                            <TableHead>Description</TableHead>
                            <TableHead class="text-center">
                                Action
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="product in props.products" :key="product.id">
                            <TableCell>{{ product.id }}</TableCell>
                            <TableCell class="font-medium">{{ product.name }}</TableCell>
                            <TableCell>{{ product.price }}</TableCell>
                            <TableCell>{{ product.description }}</TableCell>
                            <TableCell class="text-center space-x-2">
                                <Link :href="route('products.edit', {id: product.id})"><Button class="bg-slate-600">Edit</Button></Link>
                                <Button class="bg-red-600" @click="handleDelete(product.id)">Delete</Button>
                            </TableCell>

                        </TableRow>
                    </TableBody>
                </Table>

            </div>

        </div>
    </AppLayout>
</template>
