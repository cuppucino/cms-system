<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
import { Rocket, User, Shirt, CheckCircle2, RotateCcw, AlertTriangle } from 'lucide-vue-next'
import type { BreadcrumbItem } from '@/types'

interface Collection {
    id: number
    size: string
    status: string
    collection_date: string | null
    return_date: string | null
    user: { id: number; name: string; email: string }
}

const props = defineProps<{ collections: Collection[] }>()
const page = usePage()

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Gown Collections', href: '/admin/gown-collections' }]

const updateStatus = (id: number, status: string) => {
    router.put(route('admin.gown-collections.update', { gown_collection: id }), { status })
}
</script>

<template>

    <Head title="Gown Collections" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4 p-4">
            <!-- Flash Notification -->
            <div v-if="page.props.flash?.message" class="rounded-xl border border-indigo-200 bg-indigo-50 p-4">
                <Alert class="flex items-start gap-3 p-0">
                    <Rocket class="mt-0.5 h-5 w-5 shrink-0 text-indigo-600" />
                    <div>
                        <AlertTitle class="text-sm font-semibold text-indigo-900">Notification</AlertTitle>
                        <AlertDescription class="text-sm text-indigo-800">{{ page.props.flash.message }}
                        </AlertDescription>
                    </div>
                </Alert>
            </div>

            <!-- Table -->
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3 font-medium">Student</th>
                            <th class="px-4 py-3 font-medium w-[110px]">Size</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Collected At</th>
                            <th class="px-4 py-3 font-medium">Returned At</th>
                            <th class="px-4 py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!props.collections.length">
                            <td colspan="6" class="py-8 text-center text-gray-500">No gown collections found.</td>
                        </tr>
                        <tr v-for="c in props.collections" :key="c.id" class="border-t hover:bg-gray-50">
                            <td class="px-4 py-2">
                                <div class="font-medium text-gray-900 flex items-center gap-1">
                                    <User class="h-4 w-4 text-gray-400" /> {{ c.user.name }}
                                </div>
                                <div class="text-xs text-gray-500">{{ c.user.email }}</div>
                            </td>
                            <td class="px-4 py-2 text-gray-700 whitespace-nowrap">
                                <div class="flex items-center gap-1">
                                    <Shirt class="h-4 w-4 text-gray-400" aria-hidden="true" />
                                    <span class="align-middle">{{ c.size }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                <span v-if="c.status === 'collected'"
                                    class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">Collected</span>
                                <span v-else-if="c.status === 'returned'"
                                    class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">Returned</span>
                                <span v-else-if="c.status === 'late'"
                                    class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700">Late</span>
                                <span v-else
                                    class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">Pending</span>
                            </td>
                            <td class="px-4 py-2">{{ c.collection_date ?? '—' }}</td>
                            <td class="px-4 py-2">{{ c.return_date ?? '—' }}</td>
                            <td class="px-4 py-2 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <Button variant="ghost" class="h-8 px-2 text-emerald-600 hover:bg-emerald-50"
                                        :disabled="c.status !== 'reserved'" @click="updateStatus(c.id, 'collected')"
                                        title="Mark collected" aria-label="Mark collected">
                                        <CheckCircle2 class="h-4 w-4" />
                                    </Button>

                                    <Button variant="ghost" class="h-8 px-2 text-blue-600 hover:bg-blue-50"
                                        :disabled="!['collected', 'late'].includes(c.status)"
                                        @click="updateStatus(c.id, 'returned')" title="Mark returned"
                                        aria-label="Mark returned">
                                        <RotateCcw class="h-4 w-4" />
                                    </Button>

                                    <Button variant="ghost" class="h-8 px-2 text-amber-600 hover:bg-amber-50"
                                        :disabled="c.status !== 'collected'" @click="updateStatus(c.id, 'late')"
                                        title="Mark late" aria-label="Mark late">
                                        <AlertTriangle class="h-4 w-4" />
                                    </Button>

                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Clean, consistent table with icon-only action buttons */
</style>
