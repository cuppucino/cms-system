<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
import { Rocket, Search, MailPlus, Eye, Pencil, Trash2, UserPlus } from 'lucide-vue-next'
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { ref, computed } from 'vue'
import type { BreadcrumbItem } from '@/types'

interface User {
  id: number
  name: string
  email: string
  role?: string
  student_id?: string | number
  course?: { name?: string }
  convocation_session?: { name?: string }
  status?: string
  is_admin?: boolean
}

interface Props { users: User[] }
const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Users', href: '/users' }]
const page = usePage()

const q = ref('')
const role = ref<'all' | 'admin' | 'user'>('all')
const course = ref<'all' | string>('all')

// Build course list from data
const courseOptions = computed(() => {
  const set = new Set<string>()
  for (const u of props.users || []) { if (u?.course?.name) set.add(u.course.name) }
  return Array.from(set).sort()
})

// Client-side filtering
const filtered = computed(() => {
  const needle = q.value.trim().toLowerCase()
  return (props.users || []).filter(u => {
    const isAdmin = !!u?.is_admin
    if (role.value === 'admin' && !isAdmin) return false
    if (role.value === 'user' && isAdmin) return false
    if (course.value !== 'all' && (u?.course?.name ?? '') !== course.value) return false
    if (!needle) return true
    const hay = `${u?.name ?? ''} ${u?.email ?? ''} ${u?.student_id ?? ''}`.toLowerCase()
    return hay.includes(needle)
  })
})

const handleDelete = (id: number) => {
  if (confirm('Delete this user? This cannot be undone.')) {
    router.delete(route('users.destroy', { id }), { preserveScroll: true })
  }
}
</script>

<template>
  <Head title="Users" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-4 p-4">
      <!-- Header -->
      <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-gray-900">Users</h1>
        <Link :href="route('admin.users.create')">
          <Button class="inline-flex items-center gap-2 bg-indigo-600 text-white hover:bg-indigo-500">
            <UserPlus class="h-4 w-4" /> Create User
          </Button>
        </Link>
      </div>

      <!-- Flash -->
      <div v-if="page.props.flash?.message" class="rounded-xl border border-indigo-200 bg-indigo-50 p-4">
        <Alert class="flex items-start gap-3 p-0">
          <Rocket class="mt-0.5 h-5 w-5 shrink-0 text-indigo-600" />
          <div>
            <AlertTitle class="text-sm font-semibold text-indigo-900">Notification</AlertTitle>
            <AlertDescription class="text-sm text-indigo-800">{{ page.props.flash.message }}</AlertDescription>
          </div>
        </Alert>
      </div>

      <!-- Filters (native controls) -->
      <div class="grid grid-cols-1 gap-3 rounded-xl border border-gray-200 bg-white p-3 shadow-sm sm:grid-cols-3">
        <div class="relative">
          <Search class="pointer-events-none absolute left-2 top-2.5 h-4 w-4 text-gray-400" />
          <input v-model="q" type="text" placeholder="Search name, email, student ID" class="w-full rounded-md border border-gray-300 bg-white px-8 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" />
        </div>
        <div>
          <select v-model="role" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
            <option value="all">All roles</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
          </select>
        </div>
        <div>
          <select v-model="course" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
            <option value="all">All courses</option>
            <option v-for="c in courseOptions" :key="c" :value="c">{{ c }}</option>
          </select>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <Table>
          <TableCaption>All users</TableCaption>
          <TableHeader>
            <TableRow>
              <TableHead class="w-[72px]">ID</TableHead>
              <TableHead>Name</TableHead>
              <TableHead>Email</TableHead>
              <TableHead>Student ID</TableHead>
              <TableHead>Course</TableHead>
              <TableHead>Role</TableHead>
              <TableHead class="text-right">Actions</TableHead>
            </TableRow>
          </TableHeader>

          <TableBody>
            <TableRow v-if="!filtered.length">
              <TableCell colspan="7" class="py-10 text-center text-sm text-gray-500">No users found.</TableCell>
            </TableRow>

            <TableRow v-for="user in filtered" :key="user.id" class="hover:bg-gray-50">
              <TableCell class="text-gray-500">{{ user.id }}</TableCell>
              <TableCell class="font-medium text-gray-900">{{ user.name }}</TableCell>
              <TableCell>{{ user.email }}</TableCell>
              <TableCell>{{ user.student_id ?? '—' }}</TableCell>
              <TableCell>{{ user.course?.name ?? '—' }}</TableCell>
              <TableCell>
                <span :class="user.is_admin ? 'inline-flex items-center rounded-full bg-emerald-600 px-2 py-0.5 text-xs font-medium text-white' : 'inline-flex items-center rounded-full bg-gray-200 px-2 py-0.5 text-xs font-medium text-gray-800'">{{ user.is_admin ? 'Admin' : 'User' }}</span>
              </TableCell>
              <TableCell class="text-right">
                <div class="inline-flex items-center gap-1">
                  <Link :href="route('admin.users.show', { id: user.id })">
                    <Button variant="ghost" class="h-8 px-2 text-gray-700 hover:bg-gray-100"><Eye class="h-4 w-4" /></Button>
                  </Link>
                  <Link :href="route('admin.users.edit', { id: user.id })">
                    <Button variant="ghost" class="h-8 px-2 text-gray-700 hover:bg-gray-100"><Pencil class="h-4 w-4" /></Button>
                  </Link>
                  <Link :href="route('admin.invitations.create', { user_id: user.id })" as="button">
                    <Button variant="ghost" class="h-8 px-2 text-gray-700 hover:bg-gray-100"><MailPlus class="h-4 w-4" /></Button>
                  </Link>
                  <Button variant="ghost" class="h-8 px-2 text-red-600 hover:bg-red-50" @click="handleDelete(user.id)"><Trash2 class="h-4 w-4" /></Button>
                </div>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>

      <p class="text-xs text-gray-500">Tip: Use the search and filters to quickly narrow down users.</p>
    </div>
  </AppLayout>
</template>

<style scoped>
/* No external UI deps; native inputs + Tailwind */
</style>
