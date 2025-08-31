<script setup lang="ts">
import { computed, ref } from 'vue'
import { Head, useForm, usePage, router } from '@inertiajs/vue3'
import StudentLayout from '@/layouts/StudentLayout.vue'
import { Calendar, MapPin, Users, CheckCircle2 } from 'lucide-vue-next'

defineOptions({ layout: StudentLayout })

const props = defineProps<{
  registration: any | null,
  sessions: { id: number; name: string; date: string | null; location: string | null; quota: number; registered: number }[],
  gowns: { id: number; size: string; total: number; available: number }[]
}>()

const page = usePage()

const alreadyRegistered = computed(() => !!props.registration)

const fmtDate = (d?: string | null) => (d ? new Date(d).toLocaleDateString() : 'TBA')
const pct = (num: number, den: number) => (den > 0 ? Math.round((num / den) * 100) : 0)

const form = useForm({
  user_id: page.props.auth.user.id,
  attendance_confirmed: props.registration?.attendance_confirmed ?? false,
  convocation_session_id: props.registration?.convocation_session_id ?? '',
  guest_count: props.registration?.guest_count ?? 0,
  gown_size: props.registration?.gown_size ?? '',
  collection_date: props.registration?.collection_date ?? ''
})

// soft validation helpers
const canPickSession = computed(() => form.attendance_confirmed === true)
const canPickGown    = computed(() => canPickSession.value && !!form.convocation_session_id)

// submit
const submit = () => {
  if (!form.attendance_confirmed) {
    alert('Please confirm you will attend before submitting.')
    return
  }
  if (!form.convocation_session_id) {
    alert('Please select a session.')
    return
  }
  if (!form.gown_size) {
    alert('Please select a gown size.')
    return
  }
  form.post(route('student.registration.store'))
}

// cancel registration
const cancelRegistration = () => {
  const id = props.registration?.id
  if (!id) return
  if (confirm('Are you sure you want to cancel your registration?')) {
    router.delete(route('student.registration.destroy', { id }), { preserveScroll: true })
  }
}
</script>

<template>
  <Head title="Registration" />

  <!-- Header -->
  <div class="mb-6 rounded-xl border bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-xl font-semibold">Convocation Registration</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
          Confirm your attendance, select a session, and reserve your gown.
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
        — {{ fmtDate(props.registration?.session?.date) }}
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

  <!-- Form (hidden when already registered) -->
  <form v-if="!alreadyRegistered" @submit.prevent="submit" class="space-y-6">
    <!-- Step 1: Attendance -->
    <section class="rounded-xl border bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <h2 class="mb-2 text-sm font-medium">Step 1 · Attendance Confirmation</h2>
      <div class="grid gap-3 sm:grid-cols-2">
        <label class="flex cursor-pointer items-center gap-2 rounded-lg border p-3 hover:bg-gray-50 dark:hover:bg-gray-800"
               :class="form.attendance_confirmed === true ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-gray-200 dark:border-gray-800'">
          <input type="radio" class="h-4 w-4" :value="true" v-model="form.attendance_confirmed" />
          <span class="text-sm">Yes, I will attend</span>
        </label>
        <label class="flex cursor-pointer items-center gap-2 rounded-lg border p-3 hover:bg-gray-50 dark:hover:bg-gray-800"
               :class="form.attendance_confirmed === false ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-gray-200 dark:border-gray-800'">
          <input type="radio" class="h-4 w-4" :value="false" v-model="form.attendance_confirmed" />
          <span class="text-sm">No, I cannot attend</span>
        </label>
      </div>
      <p v-if="form.errors.attendance_confirmed" class="mt-2 text-xs text-red-600">{{ form.errors.attendance_confirmed }}</p>
    </section>

    <!-- Step 2: Session -->
    <section class="rounded-xl border bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <div class="mb-2 flex items-center justify-between">
        <h2 class="text-sm font-medium">Step 2 · Select Session</h2>
        <span v-if="!canPickSession" class="text-xs text-gray-500">Confirm attendance first</span>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <button
          v-for="s in props.sessions"
          :key="s.id"
          type="button"
          :disabled="!canPickSession"
          @click="form.convocation_session_id = s.id"
          class="text-left rounded-xl border p-4 transition hover:shadow-sm disabled:cursor-not-allowed disabled:opacity-60 dark:border-gray-800"
          :class="form.convocation_session_id === s.id ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-gray-200'"
        >
          <div class="text-sm font-semibold">{{ s.name }}</div>
          <div class="mt-1 text-sm text-gray-700 dark:text-gray-200 flex items-center gap-2">
            <Calendar class="h-4 w-4 text-gray-400" /> {{ fmtDate(s.date) }}
          </div>
          <div class="mt-1 text-sm text-gray-700 dark:text-gray-200 flex items-center gap-2">
            <MapPin class="h-4 w-4 text-gray-400" /> {{ s.location || 'TBA' }}
          </div>
          <div class="mt-2">
            <div class="h-1 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-800">
              <div class="h-1 rounded-full bg-indigo-500" :style="{ width: pct(s.registered, s.quota) + '%' }"></div>
            </div>
            <div class="mt-1 flex items-center justify-between text-xs text-gray-600 dark:text-gray-300">
              <span class="inline-flex items-center gap-1">
                <Users class="h-3.5 w-3.5 text-gray-400" />
                {{ s.registered }} / {{ s.quota }} filled
              </span>
              <span :class="(s.quota - s.registered) <= 0 ? 'text-red-600' : 'text-gray-500'">
                {{ (s.quota - s.registered) <= 0 ? 'Full' : (s.quota - s.registered) + ' left' }}
              </span>
            </div>
          </div>
        </button>
      </div>
      <p v-if="form.errors.convocation_session_id" class="mt-2 text-xs text-red-600">{{ form.errors.convocation_session_id }}</p>
    </section>

    <!-- Step 3: Guests -->
    <section class="rounded-xl border bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <h2 class="mb-2 text-sm font-medium">Step 3 · Guest Passes</h2>
      <div class="grid max-w-xs grid-cols-3 items-center gap-2">
        <label class="text-xs text-gray-600 dark:text-gray-300 col-span-3">Number of guests</label>
        <input
          type="number"
          min="0"
          v-model.number="form.guest_count"
          :disabled="!canPickSession"
          class="col-span-3 w-full rounded-lg border px-3 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-60 dark:border-gray-700 dark:bg-gray-800"
        />
      </div>
      <p v-if="form.errors.guest_count" class="mt-2 text-xs text-red-600">{{ form.errors.guest_count }}</p>
    </section>

    <!-- Step 4: Gown -->
    <section class="rounded-xl border bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <div class="mb-2 flex items-center justify-between">
        <h2 class="text-sm font-medium">Step 4 · Select Gown Size</h2>
        <span v-if="!canPickGown" class="text-xs text-gray-500">Pick a session first</span>
      </div>

      <div class="grid gap-3 sm:grid-cols-3">
        <label
          v-for="g in props.gowns"
          :key="g.id"
          class="flex cursor-pointer items-center justify-between rounded-lg border p-3 text-sm hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-60 dark:border-gray-800 dark:hover:bg-gray-800"
          :class="[
            form.gown_size === g.size ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-gray-200',
            (!canPickGown || g.available <= 0) ? 'opacity-60' : ''
          ]"
        >
          <div class="flex items-center gap-2">
            <input
              type="radio"
              :value="g.size"
              v-model="form.gown_size"
              :disabled="!canPickGown || g.available <= 0"
              class="h-4 w-4"
            />
            <span class="font-medium">{{ g.size }}</span>
          </div>
          <span
            class="rounded-full px-2 py-0.5 text-xs"
            :class="g.available > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500'"
          >
            {{ g.available > 0 ? g.available + ' left' : 'Out of stock' }}
          </span>
        </label>
      </div>
      <p v-if="form.errors.gown_size" class="mt-2 text-xs text-red-600">{{ form.errors.gown_size }}</p>
    </section>

    <!-- Step 5: Collection date -->
    <section class="rounded-xl border bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <h2 class="mb-2 text-sm font-medium">Step 5 · Gown Collection Date</h2>
      <input
        type="date"
        v-model="form.collection_date"
        :disabled="!canPickGown"
        class="w-full max-w-xs rounded-lg border px-3 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-60 dark:border-gray-700 dark:bg-gray-800"
      />
      <p v-if="form.errors.collection_date" class="mt-2 text-xs text-red-600">{{ form.errors.collection_date }}</p>
    </section>

    <!-- Submit -->
    <div class="flex justify-end">
      <button
        type="submit"
        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 disabled:opacity-50"
        :disabled="form.processing"
      >
        Save Registration
      </button>
    </div>
  </form>
</template>
