<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { Plus, Edit3, Trash2, Search, BookOpen } from 'lucide-vue-next'

interface Course { id:number; name:string; code:string; faculty?:string }
const props = defineProps<{ courses: { data: Course[]; links?: any[]; meta?: any } }>()

const q = ref('')
const filtered = computed(() => {
  const needle = q.value.trim().toLowerCase()
  if (!needle) return props.courses?.data || []
  return (props.courses?.data || []).filter(c => `${c.name} ${c.code} ${c.faculty ?? ''}`.toLowerCase().includes(needle))
})

const deleteCourse = (id:number) => {
  if (confirm('Delete this course?')) router.delete(route('admin.courses.destroy', id), { preserveScroll: true })
}
</script>

<template>
  <Head title="Courses" />

  <AppLayout :breadcrumbs="[{ title: 'Courses', href: route?.('admin.courses.index') ?? '#' }]">
    <div class="space-y-4 p-4">
      <!-- Header -->
      <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-gray-900 flex items-center gap-2"><BookOpen class="h-5 w-5 text-indigo-600"/> Courses</h1>
        <Link :href="route('admin.courses.create')">
          <Button class="inline-flex items-center gap-2 bg-indigo-600 text-white hover:bg-indigo-500">
            <Plus class="h-4 w-4"/> Add Course
          </Button>
        </Link>
      </div>

      <!-- Filters -->
      <div class="grid grid-cols-1 gap-3 rounded-xl border border-gray-200 bg-white p-3 shadow-sm sm:w-[520px]">
        <div class="relative">
          <Search class="pointer-events-none absolute left-2 top-2.5 h-4 w-4 text-gray-400" />
          <input v-model="q" type="text" placeholder="Search by name, code, or faculty"
                 class="w-full rounded-md border border-gray-300 bg-white px-8 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" />
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-left text-gray-600">
            <tr>
              <th class="px-4 py-3 font-medium">Name</th>
              <th class="px-4 py-3 font-medium">Code</th>
              <th class="px-4 py-3 font-medium">Faculty</th>
              <th class="px-4 py-3 text-right font-medium">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!filtered.length">
              <td colspan="4" class="py-8 text-center text-gray-500">No courses found.</td>
            </tr>
            <tr v-for="course in filtered" :key="course.id" class="border-t hover:bg-gray-50">
              <td class="px-4 py-2 font-medium text-gray-900">{{ course.name }}</td>
              <td class="px-4 py-2">{{ course.code }}</td>
              <td class="px-4 py-2">
                <span v-if="course.faculty" class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700">{{ course.faculty }}</span>
                <span v-else class="text-gray-400">—</span>
              </td>
              <td class="px-4 py-2 text-right">
                <div class="inline-flex items-center gap-1">
                  <Link :href="route('admin.courses.edit', course.id)">
                    <Button variant="ghost" class="h-8 px-2 text-gray-700 hover:bg-gray-100" title="Edit" aria-label="Edit"><Edit3 class="h-4 w-4"/></Button>
                  </Link>
                  <Button variant="ghost" class="h-8 px-2 text-red-600 hover:bg-red-50" title="Delete" aria-label="Delete" @click="deleteCourse(course.id)"><Trash2 class="h-4 w-4"/></Button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination (if provided by server) -->
      <nav v-if="props.courses?.links?.length" class="flex flex-wrap gap-2 pt-2">
        <Link v-for="l in props.courses.links" :key="l.label" :href="l.url || '#'" :class="[
              'rounded-md px-3 py-1 text-sm ring-1 ring-gray-300',
              l.active ? 'bg-indigo-600 text-white ring-indigo-600' : 'bg-white text-gray-700 hover:bg-gray-50'
            ]" preserve-scroll v-html="l.label"/>
      </nav>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Clean table + search consistent with the admin theme */
</style>
