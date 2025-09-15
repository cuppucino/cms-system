<script setup lang="ts">
import { computed, ref, nextTick } from 'vue'
import { Head, useForm, usePage, router } from '@inertiajs/vue3'
import StudentLayout from '@/layouts/StudentLayout.vue'
import { Calendar, MapPin, Users, CheckCircle2 } from 'lucide-vue-next'

defineOptions({ layout: StudentLayout })

const props = defineProps<{
  registration: any | null,
  sessions: { id: number; name: string; date: string | null; location: string | null; quota: number; registered: number }[],
  gowns: { id: number; size: string; total: number; available: number }[],
  gown_sessions: { id: number; date: string; start_time: string | null; end_time: string | null; location: string; quota: number; booked: number; note?: string | null }[]
}>()

const page = usePage()
const alreadyRegistered = computed(() => !!props.registration)

/** Format to strict YYYY-MM-DD.
 *  - If already "YYYY-MM-DD", return as is.
 *  - Else try to parse and format without locale/timezone surprises.
 *  - On failure, return original or 'TBA' when empty.
 */
const fmtDateYMD = (d?: string | null) => {
  if (!d) return 'TBA'
  if (/^\d{4}-\d{2}-\d{2}$/.test(d)) return d
  const dt = new Date(d)
  if (isNaN(dt.getTime())) return d
  const y = dt.getFullYear()
  const m = String(dt.getMonth() + 1).padStart(2, '0')
  const day = String(dt.getDate()).padStart(2, '0')
  return `${y}-${m}-${day}`
}

const fmtTimeRange = (s?: string | null, e?: string | null) =>
  (s || e) ? `${s ?? ''}${e ? '–' + e : ''}` : '—'

const form = useForm({
  user_id: (page.props as any).auth.user.id,
  attendance_confirmed: props.registration?.attendance_confirmed ?? false,
  guest_count: props.registration?.guest_count ?? 0,
  gown_size: props.registration?.gown_size ?? '',
  gown_session_id: null as number | null,
  collection_date: props.registration?.collection_date ?? '' // kept but not used for input
})

const canPickGown = computed(() => form.attendance_confirmed === true)
const errorBox = ref<HTMLElement | null>(null)

// Safely read hood color from shared auth.user.course (avoids TS error)
const hoodColor = computed<string | null>(() => {
  const u = (page.props as any)?.auth?.user
  return u?.course?.hood_color ?? null
})

// Submit
const submit = async () => {
  if (!form.attendance_confirmed) form.setError('attendance_confirmed', 'Please confirm attendance.')
  if (!form.gown_size) form.setError('gown_size', 'Please select a gown size.')
  if (!form.gown_session_id) form.setError('gown_session_id', 'Please choose a gown collection session.')

  if (Object.keys(form.errors).length) {
    await nextTick()
    errorBox.value?.scrollIntoView({ behavior: 'smooth', block: 'start' })
    return
  }

  form.post(route('student.registration.store'), {
    preserveScroll: true,
    onError: async () => {
      await nextTick()
      errorBox.value?.scrollIntoView({ behavior: 'smooth', block: 'start' })
    },
  })
}

// Cancel registration
const cancelRegistration = () => {
  if (confirm('Are you sure you want to cancel your registration?')) {
    router.delete(route('student.registration.destroy'), { preserveScroll: true })
  }
}

// helpers for hood color chips
const splitColors = (val?: string | null) => (val || '').split('&').map(s => s.trim()).filter(Boolean)
const isHex = (s: string) => /^#([0-9A-F]{3}){1,2}$/i.test(s)
</script>

<template>
  <Head title="Registration" />

  <!-- Header -->
  <div class="mb-6 rounded-xl border bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-xl font-semibold">Convocation Registration</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
          Confirm your attendance and complete the steps below.
        </p>
      </div>
      <div v-if="alreadyRegistered" class="hidden sm:block">
        <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
          <CheckCircle2 class="mr-1 h-3.5 w-3.5" /> Registered
        </span>
      </div>
    </div>
  </div>

  <!-- Flash -->
  <div v-if="$page.props.flash?.message" class="mb-6">
    <div class="rounded-lg bg-blue-50 p-3 text-sm text-blue-700 dark:bg-blue-900/40 dark:text-blue-200">
      {{ $page.props.flash.message }}
    </div>
  </div>

  <!-- Already registered banner -->
  <div
    v-if="alreadyRegistered"
    class="mb-6 flex flex-col items-start justify-between gap-3 rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-900 dark:border-yellow-900/40 dark:bg-yellow-900/30 dark:text-yellow-100 sm:flex-row"
  >
    <div>
      <div class="font-medium">You’re registered for:</div>
      <div class="mt-0.5">
        <strong>{{ props.registration?.session?.name ?? 'Session' }}</strong>
        — {{ fmtDateYMD(props.registration?.session?.date) }}
      </div>
      <div class="mt-2 text-xs text-yellow-800/80 dark:text-yellow-200/80">
        Need changes? Contact an administrator or cancel your registration.
      </div>
    </div>
    <button
      type="button"
      @click="cancelRegistration"
      class="inline-flex items-center rounded-lg bg-red-600 px-3 py-2 text-xs font-medium text-white hover:bg-red-700"
    >
      Cancel Registration
    </button>
  </div>

  <!-- Error summary -->
  <div
    v-if="Object.keys(form.errors).length"
    ref="errorBox"
    class="mb-6 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700"
  >
    <div class="mb-1 font-medium">Please fix the following:</div>
    <ul class="list-disc pl-5">
      <li v-for="(msg, key) in form.errors" :key="key">{{ msg }}</li>
    </ul>
  </div>

  <!-- Form (hidden when already registered) -->
  <form v-if="!alreadyRegistered" @submit.prevent="submit" class="space-y-6">
    <!-- Assigned Session Info -->
    <section
      class="rounded-2xl border border-indigo-200/70 bg-white p-6 md:p-7 shadow-sm ring-1 ring-indigo-100/50 dark:border-indigo-900/30 dark:bg-gray-900 dark:ring-0"
    >
      <h2 class="mb-3 text-sm font-semibold tracking-wide text-indigo-900/90 dark:text-indigo-200">
        Your Assigned Session
      </h2>

      <div v-if="props.sessions?.length" class="space-y-3">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <p class="text-lg font-bold leading-tight text-gray-900 dark:text-gray-100">
            {{ props.sessions[0].name }}
          </p>
          <span
            class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-200 dark:ring-indigo-900/50"
          >
            <Users class="mr-1.5 h-4 w-4 opacity-70" />
            {{ props.sessions[0].registered }} / {{ props.sessions[0].quota }} seats
          </span>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
          <span
            class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-700"
          >
            <Calendar class="h-4 w-4 opacity-60" />
            {{ fmtDateYMD(props.sessions[0].date) }}
          </span>
          <span
            class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-700"
          >
            <MapPin class="h-4 w-4 opacity-60" />
            {{ props.sessions[0].location || 'TBA' }}
          </span>
        </div>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
          This session is automatically assigned by your course and cannot be changed.
        </p>
      </div>

      <div
        v-else
        class="rounded-md bg-yellow-50 p-3 text-sm text-yellow-900 ring-1 ring-yellow-200 dark:bg-yellow-900/20 dark:text-yellow-100 dark:ring-yellow-900/30"
      >
        Your course has not been assigned a session yet. Please check back later or contact the convocation desk.
      </div>

      <div class="my-4 h-px w-full bg-gradient-to-r from-transparent via-indigo-200/70 to-transparent dark:via-indigo-900/40"></div>

      <!-- Hood color row -->
      <div class="flex items-center gap-2 text-sm">
        <span class="text-gray-600 dark:text-gray-300">Hood color:</span>
        <template v-if="hoodColor">
          <strong class="text-gray-900 dark:text-gray-100">{{ hoodColor }}</strong>
          <span
            v-if="isHex(hoodColor)"
            :style="{ backgroundColor: hoodColor }"
            class="ml-1 inline-block h-4 w-4 rounded-md ring-1 ring-inset ring-gray-300 dark:ring-gray-700"
            title="Hood swatch"
          />
        </template>
        <span v-else class="text-gray-500">TBA</span>
      </div>
    </section>

    <!-- Step 1: Attendance -->
    <section class="rounded-xl border bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <h2 class="mb-2 text-sm font-medium">Step 1 · Attendance Confirmation</h2>
      <div class="grid gap-3 sm:grid-cols-2">
        <label
          class="flex cursor-pointer items-center gap-2 rounded-lg border p-3 hover:bg-gray-50 dark:hover:bg-gray-800"
          :class="form.attendance_confirmed === true ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-gray-200 dark:border-gray-800'"
        >
          <input type="radio" class="h-4 w-4" :value="true" v-model="form.attendance_confirmed" />
          <span class="text-sm">Yes, I will attend</span>
        </label>
        <label
          class="flex cursor-pointer items-center gap-2 rounded-lg border p-3 hover:bg-gray-50 dark:hover:bg-gray-800"
          :class="form.attendance_confirmed === false ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-gray-200 dark:border-gray-800'"
        >
          <input type="radio" class="h-4 w-4" :value="false" v-model="form.attendance_confirmed" />
          <span class="text-sm">No, I cannot attend</span>
        </label>
      </div>
      <p v-if="form.errors.attendance_confirmed" class="mt-2 text-xs text-red-600">
        {{ form.errors.attendance_confirmed }}
      </p>
    </section>

    <!-- Step 2: Guest Passes -->
    <section class="rounded-xl border bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <h2 class="mb-2 text-sm font-medium">Step 2 · Guest Passes</h2>

      <div v-if="form.attendance_confirmed" class="space-y-3">
        <p class="text-xs text-gray-600 dark:text-gray-300">Select the number of guest passes (max 2):</p>

        <!-- Stepper -->
        <div class="inline-flex items-center rounded-lg border dark:border-gray-700">
          <button
            type="button"
            class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 disabled:opacity-50"
            :disabled="form.guest_count <= 0"
            @click="form.guest_count = Math.max(0, form.guest_count - 1)"
          >
            –
          </button>
          <span class="px-4 py-2 text-sm font-semibold">{{ form.guest_count }}</span>
          <button
            type="button"
            class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 disabled:opacity-50"
            :disabled="form.guest_count >= 2"
            @click="form.guest_count = Math.min(2, form.guest_count + 1)"
          >
            +
          </button>
        </div>

        <p v-if="form.errors.guest_count" class="mt-1 text-xs text-red-600">{{ form.errors.guest_count }}</p>
      </div>

      <div class="rounded-md bg-gray-50 p-3 text-sm text-gray-500 dark:bg-gray-800 dark:text-gray-400" v-else>
        You did not confirm attendance, so guest passes are not available.
      </div>
    </section>

    <!-- Step 3: Gown -->
    <section class="rounded-xl border bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <div class="mb-2 flex items-center justify-between">
        <h2 class="text-sm font-medium">Step 3 · Select Gown Size</h2>
        <span v-if="!canPickGown" class="text-xs text-gray-500">Confirm attendance first</span>
      </div>

      <div v-if="canPickGown">
        <div class="grid gap-3 sm:grid-cols-3">
          <label
            v-for="g in props.gowns"
            :key="g.id"
            class="flex cursor-pointer items-center justify-between rounded-lg border p-3 text-sm hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800"
            :class="[form.gown_size === g.size ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-gray-200', g.available <= 0 ? 'opacity-60' : '']"
          >
            <div class="flex items-center gap-2">
              <input type="radio" :value="g.size" v-model="form.gown_size" :disabled="g.available <= 0" class="h-4 w-4" />
              <span class="font-medium">{{ g.size }}</span>
            </div>
            <span class="rounded-full px-2 py-0.5 text-xs" :class="g.available > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500'">
              {{ g.available > 0 ? g.available + ' left' : 'Out of stock' }}
            </span>
          </label>
        </div>

        <p v-if="form.errors.gown_size" class="mt-2 text-xs text-red-600">{{ form.errors.gown_size }}</p>

        <p class="mt-3 text-xs text-gray-500">
          Your hood color:
          <template v-if="hoodColor">
            <strong>{{ hoodColor }}</strong>
            <span v-if="isHex(hoodColor)" :style="{ backgroundColor: hoodColor }" class="ml-1 inline-block h-3 w-3 rounded ring-1 ring-gray-300 align-[-1px]" />
          </template>
          <span v-else>TBA</span>
        </p>
      </div>

      <div v-else class="rounded-md bg-gray-50 p-3 text-sm text-gray-500 dark:bg-gray-800 dark:text-gray-400">
        You did not confirm attendance, so gown selection is not available.
      </div>
    </section>

    <!-- Step 4: Gown Collection Session -->
    <section class="rounded-xl border bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <div class="mb-2 flex items-center justify-between">
        <h2 class="text-sm font-medium">Step 4 · Choose Gown Collection Session</h2>
        <span v-if="!canPickGown" class="text-xs text-gray-500">Confirm attendance first</span>
      </div>

      <div v-if="canPickGown">
        <div v-if="!props.gown_sessions?.length" class="rounded-md bg-yellow-50 p-3 text-sm text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-100">
          No gown sessions are published yet. Please check again later.
        </div>

        <div v-else class="grid gap-3 md:grid-cols-2">
          <label
            v-for="gs in props.gown_sessions"
            :key="gs.id"
            class="flex cursor-pointer items-start gap-3 rounded-lg border p-4 text-sm hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800"
            :class="[form.gown_session_id === gs.id ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-gray-200']"
          >
            <input type="radio" class="mt-1 h-4 w-4" :value="gs.id" v-model="form.gown_session_id" :disabled="gs.booked >= gs.quota" />
            <div class="w-full">
              <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="font-medium">
                  {{ fmtDateYMD(gs.date) }} • {{ fmtTimeRange(gs.start_time, gs.end_time) }}
                </div>
                <span class="rounded-full px-2 py-0.5 text-xxs" :class="(gs.booked >= gs.quota) ? 'bg-gray-200 text-gray-700' : 'bg-emerald-50 text-emerald-700'">
                  {{ (gs.quota - gs.booked) > 0 ? (gs.quota - gs.booked) + ' spots left' : 'Full' }}
                </span>
              </div>
              <div class="mt-1 text-gray-700 dark:text-gray-200">
                <MapPin class="mr-1 inline-block h-4 w-4 text-gray-400" /> {{ gs.location }}
              </div>
              <div v-if="gs.note" class="mt-1 text-xs text-gray-500">{{ gs.note }}</div>
            </div>
          </label>
        </div>

        <p v-if="form.errors.gown_session_id" class="mt-2 text-xs text-red-600">{{ form.errors.gown_session_id }}</p>
      </div>

      <div v-else class="rounded-md bg-gray-50 p-3 text-sm text-gray-500 dark:bg-gray-800 dark:text-gray-400">
        You must confirm attendance and pick a gown size before choosing a collection session.
      </div>
    </section>

    <!-- Submit -->
    <div class="flex justify-end">
      <button
        type="submit"
        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 disabled:opacity-50"
        :disabled="form.processing"
      >
        <svg v-if="form.processing" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
          <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.25" />
          <path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
        </svg>
        {{ form.processing ? 'Saving...' : 'Save Registration' }}
      </button>
    </div>
  </form>
</template>
