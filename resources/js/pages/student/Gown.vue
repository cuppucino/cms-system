<script setup lang="ts">
import StudentLayout from '@/layouts/StudentLayout.vue'
import { Head } from '@inertiajs/vue3'
defineOptions({ layout: StudentLayout })

/**
 * If you later want to drive this from the DB, pass a `schemes` prop
 * with the same shape and v-for will Just Work™.
 */
const schemes = [
  {
    course: 'Software Engineering',
    palette: { robe: '#1e293b', hood: '#06b6d4', trim: '#0ea5e9', tassel: '#334155' },
    note: 'Faculty of Computing & Informatics'
  },
  {
    course: 'Accounting',
    palette: { robe: '#1f2937', hood: '#f59e0b', trim: '#fbbf24', tassel: '#6b7280' },
    note: 'Faculty of Business & Finance'
  },
  {
    course: 'Marketing',
    palette: { robe: '#0f172a', hood: '#a78bfa', trim: '#8b5cf6', tassel: '#64748b' },
    note: 'Faculty of Business & Management'
  },
  {
    course: 'Mechanical Engineering',
    palette: { robe: '#111827', hood: '#10b981', trim: '#34d399', tassel: '#4b5563' },
    note: 'Faculty of Engineering'
  },
  {
    course: 'Nursing',
    palette: { robe: '#111827', hood: '#f43f5e', trim: '#fb7185', tassel: '#6b7280' },
    note: 'Faculty of Health Sciences'
  },
  {
    course: 'Education',
    palette: { robe: '#1f2937', hood: '#ef4444', trim: '#f87171', tassel: '#6b7280' },
    note: 'Faculty of Education'
  },
  {
    course: 'Law',
    palette: { robe: '#0b1220', hood: '#eab308', trim: '#fde047', tassel: '#6b7280' },
    note: 'Faculty of Law'
  }
]
</script>

<template>
  <Head title="Gown Colours by Course" />

  <div class="rounded-xl border bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
    <h1 class="text-xl font-semibold">Gown Colours by Course</h1>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
      This page lists the official robe, hood/lining and trim colours for each programme.
      It’s for reference only — actual allocation and stock are handled during registration.
    </p>
  </div>

  <!-- Legend -->
  <div class="rounded-xl border bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
    <h2 class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-200">Legend</h2>
    <div class="flex flex-wrap gap-3 text-sm">
      <span class="inline-flex items-center gap-2 rounded-lg border px-3 py-1">
        <span class="size-3 rounded-sm" style="background:#1f2937"></span> Robe (base)
      </span>
      <span class="inline-flex items-center gap-2 rounded-lg border px-3 py-1">
        <span class="size-3 rounded-sm" style="background:#06b6d4"></span> Hood / Lining
      </span>
      <span class="inline-flex items-center gap-2 rounded-lg border px-3 py-1">
        <span class="size-3 rounded-sm" style="background:#f59e0b"></span> Trim / Piping
      </span>
      <span class="inline-flex items-center gap-2 rounded-lg border px-3 py-1">
        <span class="size-3 rounded-sm" style="background:#64748b"></span> Tassel
      </span>
    </div>
  </div>

  <!-- Schemes -->
  <div class="grid gap-6 md:grid-cols-2">
    <div v-for="s in schemes" :key="s.course"
         class="rounded-2xl border bg-white shadow-sm transition hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
      <!-- colour header -->
      <div class="rounded-t-2xl p-4"
           :style="{ background: `linear-gradient(90deg, ${s.palette.hood} 0%, ${s.palette.trim} 100%)` }">
        <h3 class="text-base font-semibold text-white drop-shadow-sm">
          {{ s.course }}
        </h3>
        <p class="text-xs/relaxed text-white/90">{{ s.note }}</p>
      </div>

      <!-- details -->
      <div class="p-4">
        <div class="grid grid-cols-2 gap-3 text-sm">
          <div class="flex items-center gap-2">
            <span class="size-4 rounded-sm ring-1 ring-black/10" :style="{ background: s.palette.robe }"></span>
            <span class="text-gray-600 dark:text-gray-300">Robe</span>
            <code class="ml-auto rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-300">
              {{ s.palette.robe }}
            </code>
          </div>
          <div class="flex items-center gap-2">
            <span class="size-4 rounded-sm ring-1 ring-black/10" :style="{ background: s.palette.hood }"></span>
            <span class="text-gray-600 dark:text-gray-300">Hood / Lining</span>
            <code class="ml-auto rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-300">
              {{ s.palette.hood }}
            </code>
          </div>
          <div class="flex items-center gap-2">
            <span class="size-4 rounded-sm ring-1 ring-black/10" :style="{ background: s.palette.trim }"></span>
            <span class="text-gray-600 dark:text-gray-300">Trim / Piping</span>
            <code class="ml-auto rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-300">
              {{ s.palette.trim }}
            </code>
          </div>
          <div class="flex items-center gap-2">
            <span class="size-4 rounded-sm ring-1 ring-black/10" :style="{ background: s.palette.tassel }"></span>
            <span class="text-gray-600 dark:text-gray-300">Tassel</span>
            <code class="ml-auto rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-300">
              {{ s.palette.tassel }}
            </code>
          </div>
        </div>

        <div class="mt-4 rounded-lg border bg-gray-50 p-3 text-xs text-gray-600 dark:border-gray-800 dark:bg-gray-800/50 dark:text-gray-300">
          Note: Colours may vary slightly depending on dye lots and lighting conditions. The final gown set will be
          issued by the university on your collection day.
        </div>
      </div>
    </div>
  </div>

  <!-- footer note -->
  <div class="rounded-xl border bg-white p-4 text-sm text-gray-600 shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
    To select a session and reserve a gown, proceed to
    <router-link to="/student/registration" class="font-medium text-indigo-600 hover:underline">Registration</router-link>.
    This page is reference-only.
  </div>
</template>
