<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { QrCode, User, Mail, ShieldOff, CheckCircle2, Search, Copy } from 'lucide-vue-next'
import moment from 'moment-timezone'

interface Invitation {
    id: number
    user_name: string
    user_email: string
    session_name?: string
    code: string
    issued_by?: string
    issued_at?: string
    revoked_at?: string
    is_active: boolean
    qr_base64: string
}

const props = defineProps<{ invitations: Invitation[] }>()
const page = usePage()

// search
const q = ref('')
const filtered = computed(() => {
    const needle = q.value.trim().toLowerCase()
    if (!needle) return props.invitations || []
    return (props.invitations || []).filter(i => (
        `${i.user_name} ${i.user_email} ${i.session_name ?? ''} ${i.code}`.toLowerCase().includes(needle)
    ))
})

const copyCode = async (code: string) => {
    try { await navigator.clipboard.writeText(code) } catch { }
}

const revoke = (id: number) => {
    if (confirm('Revoke this invitation?')) {
        router.post(route('admin.invitations.revoke', id), {}, { preserveScroll: true })
    }
}

// Function to convert time to Malaysia Time
const getMalaysiaTime = (time: string | null) => {
    if (time) {
        return moment.utc(time).tz('Asia/Kuala_Lumpur').format('YYYY-MM-DD HH:mm:ss') // Convert to MYT
    }
    return '—' // Return '—' if no time is available
}
</script>

<template>

    <Head title="Invitations" />
    <AppLayout :breadcrumbs="[{ title: 'Invitations', href: route?.('admin.invitations.index') ?? '#' }]">
        <div class="space-y-4 p-4">
            <!-- Header -->
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h1 class="text-xl font-semibold text-gray-900">Invitations</h1>
                <div class="flex items-center gap-2">
                    <div class="relative hidden sm:block">
                        <Search class="pointer-events-none absolute left-2 top-2.5 h-4 w-4 text-gray-400" />
                        <input v-model="q" type="text" placeholder="Search by name, email, code, or session"
                            class="w-72 rounded-md border border-gray-300 bg-white px-8 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" />
                    </div>
                    <Link :href="route('admin.invitations.create')">
                    <Button class="inline-flex items-center gap-2 bg-indigo-600 text-white hover:bg-indigo-500">
                        <QrCode class="h-4 w-4" /> New Invitation
                    </Button>
                    </Link>
                </div>
            </div>

            <!-- Flash message -->
            <div v-if="page.props.flash?.message"
                class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm text-emerald-800">
                {{ page.props.flash.message }}
            </div>

            <!-- Table -->
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-3 py-3 font-medium">QR</th>
                            <th class="px-3 py-3 font-medium">Student</th>
                            <th class="px-3 py-3 font-medium">Email</th>
                            <th class="px-3 py-3 font-medium">Session</th>
                            <th class="px-3 py-3 font-medium">Code</th>
                            <th class="px-3 py-3 font-medium">Issued</th>
                            <th class="px-3 py-3 font-medium">Status</th>
                            <th class="px-3 py-3 text-right font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!filtered.length">
                            <td colspan="8" class="py-8 text-center text-gray-500">No invitations found.</td>
                        </tr>

                        <tr v-for="inv in filtered" :key="inv.id" class="border-t hover:bg-gray-50">
                            <td class="px-3 py-2">
                                <img :src="`data:image/png;base64,${inv.qr_base64}`" alt="QR"
                                    class="h-12 w-12 rounded bg-white" />
                            </td>
                            <td class="px-3 py-2 font-medium text-gray-900">{{ inv.user_name }}</td>
                            <td class="px-3 py-2 text-gray-700">{{ inv.user_email }}</td>
                            <td class="px-3 py-2">{{ inv.session_name ?? '—' }}</td>
                            <td class="px-3 py-2">
                                <span class="font-mono text-xs">{{ inv.code }}</span>
                                <Button variant="ghost" class="ml-1 h-7 px-2 text-gray-600 hover:bg-gray-100"
                                    title="Copy code" aria-label="Copy code" @click="copyCode(inv.code)">
                                    <Copy class="h-4 w-4" />
                                </Button>
                            </td>
                            <td class="px-3 py-2">{{ getMalaysiaTime(inv.issued_at) }}</td>
                            <td class="px-3 py-2">
                                <span v-if="inv.is_active"
                                    class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700">
                                    <CheckCircle2 class="h-3.5 w-3.5" /> Active
                                </span>
                                <span v-else
                                    class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-2 py-0.5 text-xs font-medium text-rose-700">
                                    <ShieldOff class="h-3.5 w-3.5" /> Revoked
                                </span>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <Button v-if="inv.is_active" variant="ghost"
                                        class="h-8 px-2 text-rose-600 hover:bg-rose-50" title="Revoke"
                                        aria-label="Revoke" @click="revoke(inv.id)">
                                        <ShieldOff class="h-4 w-4" />
                                    </Button>
                                    <span v-else class="text-gray-400">—</span>
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
/* Polished invitations table with search and action buttons */
</style>
