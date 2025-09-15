<script setup lang="ts">
import StudentLayout from '@/layouts/StudentLayout.vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

defineOptions({ layout: StudentLayout })

/**
 * Types
 */
interface Palette {
  robe: string
  hood: string
  trim: string
  tassel: string
}
interface Scheme {
  course: string
  palette: Palette
  note?: string
}

/**
 * Optional prop: pass real/accurate schemes from the backend.
 * If omitted, we fall back to the static defaults below.
 */
const props = defineProps<{
  schemes?: Scheme[]
}>()

/**
 * Fallback static data (used only if backend doesn't pass `schemes`)
 */
const defaultSchemes: Scheme[] = [
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

/**
 * Use backend data if provided; otherwise fallback.
 */
const useSchemes = computed<Scheme[]>(() => props.schemes?.length ? props.schemes : defaultSchemes)

/**
 * Access user + course from Inertia props
 * (using `any` here so TS doesn't complain if your global typings don't include course)
 */
const page = usePage()
const user = computed<any>(() => (page.props as any)?.auth?.user ?? null)
const userCourseName = computed<string | null>(() => user.value?.course?.name ?? null)
const hoodColor = computed<string | null>(() => user.value?.course?.hood_color ?? null)

/**
 * Try to find the current user's course in the schemes list
 * to display full robe/hood/trim/tassel palette for that course.
 */
const myScheme = computed<Scheme | null>(() => {
  if (!userCourseName.value) return null
  const needle = userCourseName.value.toLowerCase()
  return useSchemes.value.find(s => s.course.toLowerCase() === needle) ?? null
})

/**
 * Helpers
 */
const isHex = (s?: string | null) => !!s && /^#([0-9A-F]{3}){1,2}$/i.test(s)
</script>

<template>
  <Head title="Gown Colours by Course" />

  <!-- Top: Your Gown Colour (accurate to the logged-in student's course) -->
  <div class="rounded-xl border bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
    <h1 class="text-xl font-semibold">Your Gown Colour</h1>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
      Based on your programme: <strong>{{ userCourseName || '—' }}</strong>
    </p>

    <!-- If we know their full scheme (matching course found), show robe/hood/trim/tassel -->
    <div v-if="myScheme" class="mt-4 rounded-lg border bg-gray-50 p-4 text-sm dark:border-gray-800 dark:bg-gray-800/40">
      <div class="mb-2">
        <div class="text-sm font-semibold">{{ myScheme.course }}</div>
        <div v-if="myScheme.note" class="text-xs text-gray-600 dark:text-gray-300">{{ myScheme.note }}</div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div class="flex items-center gap-2">
          <span class="size-4 rounded-sm ring-1 ring-black/10" :style="{ background: myScheme.palette.robe }"></span>
          <span>Robe</span>
          <code class="ml-auto rounded bg-white/70 px-1.5 py-0.5 text-xs text-gray-700 dark:bg-gray-900/40 dark:text-gray-200">
            {{ myScheme.palette.robe }}
          </code>
        </div>
        <div class="flex items-center gap-2">
          <span class="size-4 rounded-sm ring-1 ring-black/10" :style="{ background: myScheme.palette.hood }"></span>
          <span>Hood / Lining</span>
          <code class="ml-auto rounded bg-white/70 px-1.5 py-0.5 text-xs text-gray-700 dark:bg-gray-900/40 dark:text-gray-200">
            {{ myScheme.palette.hood }}
          </code>
        </div>
        <div class="flex items-center gap-2">
          <span class="size-4 rounded-sm ring-1 ring-black/10" :style="{ background: myScheme.palette.trim }"></span>
          <span>Trim / Piping</span>
          <code class="ml-auto rounded bg-white/70 px-1.5 py-0.5 text-xs text-gray-700 dark:bg-gray-900/40 dark:text-gray-200">
            {{ myScheme.palette.trim }}
          </code>
        </div>
        <div class="flex items-center gap-2">
          <span class="size-4 rounded-sm ring-1 ring-black/10" :style="{ background: myScheme.palette.tassel }"></span>
          <span>Tassel</span>
          <code class="ml-auto rounded bg-white/70 px-1.5 py-0.5 text-xs text-gray-700 dark:bg-gray-900/40 dark:text-gray-200">
            {{ myScheme.palette.tassel }}
          </code>
        </div>
      </div>
    </div>

    <!-- Else, if we only know the hood color string, show it (name + optional swatch if hex) -->
    <div v-else class="mt-4 rounded-lg border bg-gray-50 p-4 text-sm dark:border-gray-800 dark:bg-gray-800/40">
      <div class="text-sm font-semibold">Hood / Lining</div>
      <div class="mt-2">
        <template v-if="hoodColor">
          <span class="font-medium">{{ hoodColor }}</span>
          <span v-if="isHex(hoodColor)"
                :style="{ backgroundColor: hoodColor }"
                class="ml-2 inline-block h-3.5 w-3.5 rounded ring-1 ring-gray-300 align-[-1px]" />
        </template>
        <span v-else class="text-gray-500">TBA</span>
      </div>
      <p class="mt-2 text-xs text-gray-500">
        Full scheme (robe/trim/tassel) will appear here when available for your course.
      </p>
    </div>

    <p class="mt-4 text-xs text-gray-500">
      Final colours and allocation are confirmed during gown collection.
    </p>
  </div>

  <!-- Legend -->
  <div class="mt-6 rounded-xl border bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
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

  <!-- Schemes (accurate if passed from backend; otherwise fallback list) -->
  <div class="mt-6 grid gap-6 md:grid-cols-2">
    <div v-for="s in useSchemes" :key="s.course"
         class="rounded-2xl border bg-white shadow-sm transition hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
      <!-- colour header -->
      <div class="rounded-t-2xl p-4"
           :style="{ background: `linear-gradient(90deg, ${s.palette.hood} 0%, ${s.palette.trim} 100%)` }">
        <h3 class="text-base font-semibold text-white drop-shadow-sm">
          {{ s.course }}
        </h3>
        <p v-if="s.note" class="text-xs/relaxed text-white/90">{{ s.note }}</p>
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
  <div class="mt-6 rounded-xl border bg-white p-4 text-sm text-gray-600 shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
    To select a session and reserve a gown, proceed to
    <Link href="/student/registration" class="font-medium text-indigo-600 hover:underline">Registration</Link>.
    This page is reference-only.
  </div>
</template>
