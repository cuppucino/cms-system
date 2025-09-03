<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import { Head, router, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { Users, Mail, Search, X } from 'lucide-vue-next'

interface Student { id: number; name: string; email: string }
const props = defineProps<{ students: Student[] }>()

const form = ref({ user_ids: [] as number[], title: '', message: '', send_email: false, send_to_all: false })


// --- recipient picker state ---
const q = ref('')
const filtered = computed(() => {
    const needle = q.value.trim().toLowerCase()
    if (!needle) return props.students || []
    return (props.students || []).filter(s => `${s.name} ${s.email}`.toLowerCase().includes(needle))
})

const isSelected = (id: number) => form.value.user_ids.includes(id)
const toggle = (id: number) => {
    const set = new Set(form.value.user_ids)
    set.has(id) ? set.delete(id) : set.add(id)
    form.value.user_ids = Array.from(set)
}
const addAllFiltered = () => {
    const set = new Set(form.value.user_ids)
    for (const s of filtered.value) set.add(s.id)
    form.value.user_ids = Array.from(set)
}
const clearAll = () => { form.value.user_ids = [] }
const removeOne = (id: number) => { form.value.user_ids = form.value.user_ids.filter(x => x !== id) }

const submit = () => router.post(route('admin.notifications.store'), form.value)
</script>

<template>

    <Head title="Send Notification" />

    <AppLayout
        :breadcrumbs="[{ title: 'Notifications', href: route('admin.notifications.index') }, { title: 'Send', href: '#' }]">
        <div class="space-y-6 p-6">
            <h1 class="text-xl font-semibold text-gray-900">Send Notification</h1>

            <form @submit.prevent="submit" class="grid max-w-3xl grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Recipient picker -->
                <div class="md:col-span-2 space-y-3">
                    <label class="block text-sm font-medium text-gray-700">Recipients</label>
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" v-model="(form as any).send_to_all" />
                        Send to all students (ignores selection)
                    </label>

                    <!-- Search + bulk add -->
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div class="relative sm:col-span-2">
                            <Search class="pointer-events-none absolute left-2 top-2.5 h-4 w-4 text-gray-400" />
                            <input v-model="q" type="text" placeholder="Search student name or email"
                                class="w-full rounded-md border border-gray-300 bg-white px-8 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" />
                        </div>
                        <Button type="button" @click="addAllFiltered"
                            class="inline-flex items-center justify-center gap-2 bg-indigo-600 text-white hover:bg-indigo-500">
                            <Users class="h-4 w-4" /> Add all filtered ({{ filtered.length }})
                        </Button>
                    </div>

                    <!-- Results list -->
                    <div class="max-h-64 overflow-auto rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div v-for="s in filtered" :key="s.id"
                            class="flex items-center gap-3 border-b px-3 py-2 last:border-b-0 hover:bg-gray-50">
                            <input type="checkbox" :checked="isSelected(s.id)" @change="toggle(s.id)"
                                aria-label="Select recipient" />
                            <div class="min-w-0 flex-1">
                                <div class="truncate font-medium text-gray-900">{{ s.name }}</div>
                                <div class="truncate text-xs text-gray-500">{{ s.email }}</div>
                            </div>
                            <Button type="button" variant="ghost"
                                class="h-7 px-2 text-xs text-indigo-700 hover:bg-indigo-50" @click="toggle(s.id)">
                                {{ isSelected(s.id) ? 'Remove' : 'Add' }}
                            </Button>
                        </div>
                        <div v-if="!filtered.length" class="py-6 text-center text-sm text-gray-500">No matches.</div>
                    </div>

                    <!-- Selected chips -->
                    <div class="flex flex-wrap gap-2">
                        <template v-if="form.user_ids.length">
                            <span v-for="id in form.user_ids" :key="id"
                                class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2 py-1 text-xs text-indigo-700">
                                {{(props.students.find(s => s.id === id) || {}).name || id}}
                                <button type="button" class="ml-1 text-indigo-500 hover:text-indigo-700"
                                    @click="removeOne(id)" aria-label="Remove recipient">
                                    <X class="h-3.5 w-3.5" />
                                </button>
                            </span>
                            <button type="button" class="text-xs text-gray-600 underline hover:text-gray-800"
                                @click="clearAll">Clear all</button>
                        </template>
                        <span v-else class="text-xs text-gray-500">No recipients selected yet.</span>
                    </div>
                </div>

                <!-- Title -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Title</label>
                    <input v-model="form.title" type="text"
                        class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" />
                </div>

                <!-- Message -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Message</label>
                    <textarea v-model="form.message" rows="4"
                        class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"></textarea>
                </div>

                <!-- Email Option -->
                <div class="flex items-center gap-2 md:col-span-2">
                    <input id="send_email" type="checkbox" v-model="form.send_email"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                    <label for="send_email" class="text-sm text-gray-700 inline-flex items-center gap-1">
                        <Mail class="h-4 w-4 text-gray-400" /> Also send via Email
                    </label>
                </div>

                <!-- Actions -->
                <div class="md:col-span-2 flex items-center gap-2">
                    <Button type="submit" class="bg-indigo-600 text-white hover:bg-indigo-500">Send</Button>
                    <Link :href="route('admin.notifications.index')">
                    <Button type="button" class="bg-gray-600 text-white hover:bg-gray-500">Back</Button>
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Search + chips recipient picker, consistent styling */
</style>
