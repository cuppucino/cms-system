<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { ref, onMounted, onBeforeUnmount } from 'vue'

interface Notification {
  id: number
  title: string
  message: string
  is_read: boolean
  created_at: string
}
interface User {
  id?: number
  name?: string
  unread_notifications?: number
  recent_notifications?: Notification[]
}

const page = usePage()
const user = (page.props.auth?.user ?? {}) as User

const nav = [
  { label: 'Dashboard',     href: '/student' },
  { label: 'Registration',  href: '/student/registration' },
  { label: 'Payments',      href: '/student/payments' },
  { label: 'Invitation',    href: '/student/invitation' },
]

// exact / prefix-aware active checker (prevents dashboard from matching others)
const isActive = (href: string) => {
  const url = page.url
  return href === '/student' ? url === '/student' : url.startsWith(href)
}

// Profile dropdown
const showProfileMenu = ref(false)
const profileMenuRef = ref<HTMLElement | null>(null)

// Notifications dropdown
const showNotifMenu = ref(false)
const notifMenuRef = ref<HTMLElement | null>(null)

function toggleProfileMenu() { showProfileMenu.value = !showProfileMenu.value }
function toggleNotifMenu() { showNotifMenu.value = !showNotifMenu.value }

// Close dropdowns on outside click
function handleClickOutside(event: MouseEvent) {
  if (profileMenuRef.value && !profileMenuRef.value.contains(event.target as Node)) showProfileMenu.value = false
  if (notifMenuRef.value && !notifMenuRef.value.contains(event.target as Node)) showNotifMenu.value = false
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside))
</script>

<template>
  <div class="min-h-screen bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100">
    <!-- Top bar with nav -->
    <header class="sticky top-0 z-40 border-b bg-white/70 backdrop-blur dark:bg-gray-900/70">
      <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3">

        <!-- Branding -->
        <div class="flex items-center gap-2">
          <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white">🎓</span>
          <span class="font-semibold">Convocation</span>
          <span class="ml-2 rounded-full bg-indigo-50 px-2 py-0.5 text-xs text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200">Student</span>
        </div>

        <!-- Navigation -->
        <nav class="hidden gap-2 text-sm md:flex">
          <Link
            v-for="item in nav"
            :key="item.href"
            :href="item.href"
            class="rounded-lg px-3 py-2 transition hover:bg-gray-100 dark:hover:bg-gray-800"
            :class="isActive(item.href)
              ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200'
              : ''"
          >
            {{ item.label }}
          </Link>
        </nav>

        <!-- Right side: notifications + profile -->
        <div class="relative flex items-center gap-4">
          <!-- Notifications -->
          <div class="relative" ref="notifMenuRef">
            <button
              @click="toggleNotifMenu"
              class="relative inline-flex h-9 w-9 items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-800"
            >
              🔔
              <span
                v-if="user.unread_notifications && user.unread_notifications > 0"
                class="absolute right-1 top-1 inline-flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-xs text-white"
              >
                {{ user.unread_notifications }}
              </span>
            </button>

            <div
              v-if="showNotifMenu"
              class="absolute right-0 z-50 mt-2 w-80 overflow-hidden rounded-lg border bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900"
            >
              <div class="max-h-60 overflow-y-auto p-2">
                <div
                  v-if="!user.recent_notifications || user.recent_notifications.length === 0"
                  class="px-3 py-2 text-sm text-gray-500"
                >
                  No notifications yet.
                </div>

                <div
                  v-for="n in user.recent_notifications"
                  :key="n.id"
                  class="border-b px-3 py-2 last:border-none"
                  :class="n.is_read ? 'bg-gray-50' : 'bg-indigo-50'"
                >
                  <h3 class="text-sm font-semibold">{{ n.title }}</h3>
                  <p class="truncate text-xs text-gray-600">{{ n.message }}</p>
                  <span class="text-xs text-gray-400">{{ n.created_at }}</span>
                </div>
              </div>

              <div class="border-t">
                <Link
                  href="/student/notifications"
                  class="block py-2 text-center text-sm text-indigo-600 hover:bg-gray-100 dark:hover:bg-gray-800"
                >
                  View all notifications
                </Link>
              </div>
            </div>
          </div>

          <!-- Profile -->
          <div class="relative" ref="profileMenuRef">
            <button
              @click="toggleProfileMenu"
              class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-800"
            >
              <span class="hidden md:inline">{{ user.name }}</span>
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-white">
                {{ user.name?.charAt(0) ?? 'U' }}
              </span>
            </button>

            <div
              v-if="showProfileMenu"
              class="absolute right-0 mt-2 w-40 overflow-hidden rounded-lg border bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900"
            >
              <Link href="/student/profile" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-800">
                Profile
              </Link>
              <Link
                href="/logout"
                method="post"
                as="button"
                class="block w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-800"
              >
                Logout
              </Link>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Main -->
    <main class="mx-auto max-w-7xl space-y-6 px-4 py-6">
      <slot />
    </main>
  </div>
</template>
