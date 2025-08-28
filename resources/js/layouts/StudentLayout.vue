<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { ref, onMounted, onBeforeUnmount } from 'vue'

const page = usePage()
const user = page.props.auth?.user ?? {}

const nav = [
  { label: 'Dashboard', href: '/student' },
  { label: 'Registration', href: '/student/registration' },
  { label: 'Session', href: '/student/session' },
  { label: 'Invitation', href: '/student/invitation' },
  { label: 'Gown', href: '/student/gown' },
  { label: 'Payments', href: '/student/payments' },
]

const showProfileMenu = ref(false)
const profileMenuRef = ref<HTMLElement | null>(null)

function toggleProfileMenu() {
  showProfileMenu.value = !showProfileMenu.value
}

// Close dropdown on outside click
function handleClickOutside(event: MouseEvent) {
  if (profileMenuRef.value && !profileMenuRef.value.contains(event.target as Node)) {
    showProfileMenu.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
  <div class="min-h-screen bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100">
    <!-- Top bar with nav -->
    <header class="sticky top-0 z-40 border-b bg-white/70 backdrop-blur dark:bg-gray-900/70">
      <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3">

        <!-- Branding -->
        <div class="flex items-center gap-2">
          <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 text-white">🎓</span>
          <span class="font-semibold">Convocation</span>
          <span class="ml-2 rounded-full bg-blue-50 px-2 py-0.5 text-xs text-blue-700 dark:bg-blue-900/40 dark:text-blue-200">Student</span>
        </div>

        <!-- Navigation -->
        <nav class="hidden md:flex gap-2 text-sm">
          <Link v-for="item in nav" :key="item.href" :href="item.href"
                class="rounded-lg px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-800"
                :class="{ 'bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-200': $page.url.startsWith(item.href) }">
            {{ item.label }}
          </Link>
        </nav>

        <!-- Right side: notifications + profile -->
        <div class="flex items-center gap-4 relative">
          <!-- Notifications icon -->
          <Link href="/student/notifications" class="relative">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-800">
              🔔
            </span>
            <!-- Example: red dot -->
            <span class="absolute top-1 right-1 inline-flex h-2 w-2 rounded-full bg-red-500"></span>
          </Link>

          <!-- Profile dropdown -->
          <div class="relative" ref="profileMenuRef">
            <button @click="toggleProfileMenu"
                    class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-800">
              <span class="hidden md:inline">{{ user.name }}</span>
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white">
                {{ user.name?.charAt(0) ?? "U" }}
              </span>
            </button>

            <!-- Dropdown menu -->
            <div v-if="showProfileMenu"
                 class="absolute right-0 mt-2 w-40 rounded-lg border bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
              <Link href="/student/profile" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-800">
                Profile
              </Link>
              <Link href="/logout" method="post" as="button"
                    class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-800">
                Logout
              </Link>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Main page slot -->
    <main class="mx-auto max-w-7xl px-4 py-6 space-y-6">
      <slot />
    </main>
  </div>
</template>
