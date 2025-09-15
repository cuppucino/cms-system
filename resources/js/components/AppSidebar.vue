<script setup lang="ts">
import { computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'

import NavFooter from '@/components/NavFooter.vue'
import NavMain from '@/components/NavMain.vue'
import NavUser from '@/components/NavUser.vue'
import AppLogo from './AppLogo.vue'

import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from '@/components/ui/sidebar'

// Lucide icons
import {
  LayoutGrid,     // Dashboard
  Calendar,       // Sessions
  Ticket,         // Invitations
  Users,          // Users
  BookOpen,       // Courses
  GraduationCap,  // Gown stock
  Archive,        // Gown collections
  ClipboardCheck, // Attendance
  DollarSign,     // Payments
  Bell,           // Notifications
  BarChart2,      // Reports
  UserPlus,       // Guests
  CalendarRange,  // Gown Sessions (NEW)
} from 'lucide-vue-next'

// --- Read auth once per render and keep reactive via computed ---
const page = usePage()
const isAdmin = computed<boolean>(() => Boolean(page.props.auth?.user?.is_admin))

// --- Nav definitions ---
const adminNavItems = [
  // Overview
  { title: 'Dashboard', href: route('admin.dashboard'), icon: LayoutGrid },

  // Event
  { title: 'Sessions', href: route('admin.sessions.index'), icon: Calendar },
  { title: 'Invitations', href: route('admin.invitations.index'), icon: Ticket },

  // People
  { title: 'Users', href: route('admin.users.index'), icon: Users },
  { title: 'Courses', href: route('admin.courses.index'), icon: BookOpen },
  { title: 'Guests', href: route('admin.guest.index'), icon: UserPlus },

  // Operations
  { title: 'Gown', href: route('admin.gowns.index'), icon: GraduationCap },
  { title: 'Gown Sessions', href: route('admin.gown-sessions.index'), icon: CalendarRange },
  { title: 'Gown Collections', href: route('admin.gown-collections.index'), icon: Archive },
  { title: 'Attendance', href: route('admin.attendance.index'), icon: ClipboardCheck },

  // Finance
  { title: 'Payments', href: route('admin.payments.index'), icon: DollarSign },

  // Comms
  { title: 'Notifications', href: route('admin.notifications.index'), icon: Bell },

  // Analytics
  { title: 'Reports', href: route('admin.reports.index'), icon: BarChart2 },
]

// Student menu:
// Keep it lean and only include confirmed routes you already have:
// - student.dashboard
// - student.registration.index
// - student.session.show
const studentNavItems = [
  { title: 'Dashboard', href: route('student.dashboard'), icon: LayoutGrid },
  { title: 'Registration', href: route('student.registration.index'), icon: ClipboardCheck },
  { title: 'Your Session', href: route('student.session.show'), icon: Calendar },
]

// Always provide an array for footer to avoid prop warnings
const footerNavItems: Array<{ title: string; href: string; icon?: any }> = []

// Single computed source passed down
const navItems = computed(() => (isAdmin.value ? adminNavItems : studentNavItems))
</script>

<template>
  <Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <!-- Smart /dashboard route on your server can redirect to admin/student accordingly -->
            <Link :href="route('dashboard')">
              <AppLogo />
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
      <NavMain :items="navItems" />
    </SidebarContent>

    <SidebarFooter>
      <NavFooter :items="footerNavItems" />
      <NavUser />
    </SidebarFooter>
  </Sidebar>

  <!-- Render page content next to the sidebar -->
  <slot />
</template>
