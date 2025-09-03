<script setup lang="ts">
import { computed, ref, nextTick } from 'vue'
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

// helpers
const canPickSession = computed(() => form.attendance_confirmed === true)
const canPickGown = computed(() => canPickSession.value && !!form.convocation_session_id)

const errorBox = ref<HTMLElement | null>(null)
const isFull = (s: { quota: number; registered: number }) => (s.registered >= s.quota)
const left = (s: { quota: number; registered: number }) => Math.max(0, s.quota - s.registered)

// submit
const submit = async () => {
    // inline validations to surface errors near fields
    if (!form.attendance_confirmed) form.setError('attendance_confirmed', 'Please confirm attendance.')
    if (!form.convocation_session_id) form.setError('convocation_session_id', 'Please select a session.')
    if (!form.gown_size) form.setError('gown_size', 'Please select a gown size.')

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

// cancel registration
const cancelRegistration = () => {
    if (confirm('Are you sure you want to cancel your registration?')) {
        router.delete(route('student.registration.destroy'), { preserveScroll: true })
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
                <span
                    class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
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
    <div v-if="alreadyRegistered"
        class="mb-6 flex flex-col items-start justify-between gap-3 rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-900 dark:border-yellow-900/40 dark:bg-yellow-900/30 dark:text-yellow-100 sm:flex-row">
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
        <button type="button" @click="cancelRegistration"
            class="inline-flex items-center rounded-lg bg-red-600 px-3 py-2 text-xs font-medium text-white hover:bg-red-700">
            Cancel Registration
        </button>
    </div>

    <!-- Error summary -->
    <div v-if="Object.keys(form.errors).length" ref="errorBox"
        class="mb-6 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
        <div class="mb-1 font-medium">Please fix the following:</div>
        <ul class="list-disc pl-5">
            <li v-for="(msg, key) in form.errors" :key="key">{{ msg }}</li>
        </ul>
    </div>

    <!-- Form (hidden when already registered) -->
    <form v-if="!alreadyRegistered" @submit.prevent="submit" class="space-y-6">
        <!-- Step 1: Attendance -->
        <section class="rounded-xl border bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h2 class="mb-2 text-sm font-medium">Step 1 · Attendance Confirmation</h2>
            <div class="grid gap-3 sm:grid-cols-2">
                <label
                    class="flex cursor-pointer items-center gap-2 rounded-lg border p-3 hover:bg-gray-50 dark:hover:bg-gray-800"
                    :class="form.attendance_confirmed === true ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-gray-200 dark:border-gray-800'">
                    <input type="radio" class="h-4 w-4" :value="true" v-model="form.attendance_confirmed" />
                    <span class="text-sm">Yes, I will attend</span>
                </label>
                <label
                    class="flex cursor-pointer items-center gap-2 rounded-lg border p-3 hover:bg-gray-50 dark:hover:bg-gray-800"
                    :class="form.attendance_confirmed === false ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-gray-200 dark:border-gray-800'">
                    <input type="radio" class="h-4 w-4" :value="false" v-model="form.attendance_confirmed" />
                    <span class="text-sm">No, I cannot attend</span>
                </label>
            </div>
            <p v-if="form.errors.attendance_confirmed" class="mt-2 text-xs text-red-600">
                {{ form.errors.attendance_confirmed }}
            </p>
        </section>

        <!-- Step 2: Session -->
        <section class="rounded-xl border bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="mb-2 flex items-center justify-between">
                <h2 class="text-sm font-medium">Step 2 · Select Session</h2>
                <span v-if="!canPickSession" class="text-xs text-gray-500">Confirm attendance first</span>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <button v-for="s in props.sessions" :key="s.id" type="button" :disabled="!canPickSession || isFull(s)"
                    @click="form.convocation_session_id = s.id"
                    class="text-left rounded-xl border p-4 transition hover:shadow-sm disabled:cursor-not-allowed disabled:opacity-60 dark:border-gray-800"
                    :class="[
                        form.convocation_session_id === s.id ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-gray-200',
                        isFull(s) ? 'bg-gray-50 dark:bg-gray-800' : ''
                    ]" :aria-disabled="!canPickSession || isFull(s)">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-semibold">{{ s.name }}</div>
                        <span v-if="isFull(s)"
                            class="rounded-full bg-gray-200 px-2 py-0.5 text-xxs font-medium text-gray-700">
                            Full
                        </span>
                        <span v-else
                            class="rounded-full bg-emerald-50 px-2 py-0.5 text-xxs font-medium text-emerald-700">
                            {{ left(s) }} left
                        </span>
                    </div>

                    <div class="mt-1 flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                        <Calendar class="h-4 w-4 text-gray-400" /> {{ fmtDate(s.date) }}
                    </div>
                    <div class="mt-1 flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                        <MapPin class="h-4 w-4 text-gray-400" /> {{ s.location || 'TBA' }}
                    </div>

                    <div class="mt-2">
                        <div class="h-1 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-800">
                            <div class="h-1 rounded-full bg-indigo-500"
                                :style="{ width: pct(s.registered, s.quota) + '%' }"></div>
                        </div>
                        <div class="mt-1 flex items-center justify-between text-xs text-gray-600 dark:text-gray-300">
                            <span class="inline-flex items-center gap-1">
                                <Users class="h-3.5 w-3.5 text-gray-400" />
                                {{ s.registered }} / {{ s.quota }} filled
                            </span>
                            <span :class="isFull(s) ? 'text-red-600' : 'text-gray-500'">
                                {{ isFull(s) ? 'Full' : left(s) + ' left' }}
                            </span>
                        </div>
                    </div>
                </button>
            </div>
            <p v-if="form.errors.convocation_session_id" class="mt-2 text-xs text-red-600">
                {{ form.errors.convocation_session_id }}
            </p>
        </section>

        <!-- Step 3: Guests -->
        <section class="rounded-xl border bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h2 class="mb-2 text-sm font-medium">Step 3 · Guest Passes</h2>
            <div class="grid max-w-xs grid-cols-3 items-center gap-2">
                <label class="col-span-3 text-xs text-gray-600 dark:text-gray-300">Number of guests</label>
                <input type="number" min="0" max="2" v-model.number="form.guest_count" :disabled="!canPickSession"
                    class="col-span-3 w-full rounded-lg border px-3 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-60 dark:border-gray-700 dark:bg-gray-800" />
            </div>
            <p v-if="form.errors.guest_count" class="mt-2 text-xs text-red-600">{{ form.errors.guest_count }}</p>

            <!-- Reminder about maximum capacity -->
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                Note: The maximum number of guests you can add is <strong>2</strong>.
            </p>
        </section>


        <!-- Step 4: Gown -->
        <section class="rounded-xl border bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="mb-2 flex items-center justify-between">
                <h2 class="text-sm font-medium">Step 4 · Select Gown Size</h2>
                <span v-if="!canPickGown" class="text-xs text-gray-500">Pick a session first</span>
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
                <label v-for="g in props.gowns" :key="g.id"
                    class="flex cursor-pointer items-center justify-between rounded-lg border p-3 text-sm hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-60 dark:border-gray-800 dark:hover:bg-gray-800"
                    :class="[
                        form.gown_size === g.size ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-gray-200',
                        (!canPickGown || g.available <= 0) ? 'opacity-60' : ''
                    ]">
                    <div class="flex items-center gap-2">
                        <input type="radio" :value="g.size" v-model="form.gown_size"
                            :disabled="!canPickGown || g.available <= 0" class="h-4 w-4" />
                        <span class="font-medium">{{ g.size }}</span>
                    </div>
                    <span class="rounded-full px-2 py-0.5 text-xs"
                        :class="g.available > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500'">
                        {{ g.available > 0 ? g.available + ' left' : 'Out of stock' }}
                    </span>
                </label>
            </div>
            <p v-if="form.errors.gown_size" class="mt-2 text-xs text-red-600">{{ form.errors.gown_size }}</p>
        </section>

        <!-- Step 5: Collection date -->
        <section class="rounded-xl border bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h2 class="mb-2 text-sm font-medium">Step 5 · Gown Collection Date</h2>
            <input type="date" v-model="form.collection_date" :disabled="!canPickGown"
                class="w-full max-w-xs rounded-lg border px-3 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-60 dark:border-gray-700 dark:bg-gray-800" />
            <p v-if="form.errors.collection_date" class="mt-2 text-xs text-red-600">{{ form.errors.collection_date }}
            </p>
        </section>

        <!-- Submit -->
        <div class="flex justify-end">
            <button type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 disabled:opacity-50"
                :disabled="form.processing">
                <svg v-if="form.processing" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 animate-spin"
                    viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.25" />
                    <path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                </svg>
                {{ form.processing ? 'Saving...' : 'Save Registration' }}
            </button>
        </div>
    </form>
</template>
