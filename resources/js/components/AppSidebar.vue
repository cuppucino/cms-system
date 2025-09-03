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

// Lucide icons (ensure these exist in lucide-vue-next)
import {
    LayoutGrid,     // Dashboard
    Calendar,       // Sessions
    Ticket,         // Invitations
    Users,          // Users
    BookOpen,       // Courses
    GraduationCap,  // Gown
    Archive,        // Gown Collections
    ClipboardCheck, // Attendance
    DollarSign,     // Payments
    Bell,           // Notifications
    BarChart2,      // Reports
} from 'lucide-vue-next'

// --- Read auth once per render and keep reactive via computed ---
const page = usePage()
const isAdmin = computed<boolean>(() => Boolean(page.props.auth?.user?.is_admin))

// --- Nav definitions (plain arrays are fine; we only pass them via a computed) ---
const adminNavItems = [
    // Overview
    { title: 'Dashboard', href: route('admin.dashboard'), icon: LayoutGrid },

    // Event
    { title: 'Sessions', href: route('admin.sessions.index'), icon: Calendar },
    { title: 'Invitations', href: route('admin.invitations.index'), icon: Ticket },

    // People
    { title: 'Users', href: route('admin.users.index'), icon: Users },
    { title: 'Courses', href: route('admin.courses.index'), icon: BookOpen },

    // Operations
    { title: 'Gown', href: route('admin.gowns.index'), icon: GraduationCap },
    { title: 'Gown Collections', href: route('admin.gown-collections.index'), icon: Archive },
    { title: 'Attendance', href: route('admin.attendance.index'), icon: ClipboardCheck },

    // Finance
    { title: 'Payments', href: route('admin.payments.index'), icon: DollarSign },

    // Comms
    { title: 'Notifications', href: route('admin.notifications.index'), icon: Bell },

    // Analytics
    { title: 'Reports', href: route('admin.reports.index'), icon: BarChart2 },
]

const studentNavItems = [
    { title: 'Dashboard', href: route('student.dashboard'), icon: LayoutGrid },
]

// ✅ Always provide an array for footer to avoid “undefined” prop/type warnings
const footerNavItems: Array<{ title: string; href: string; icon?: any }> = [
    // Example:
    // { title: 'Help', href: 'https://example.com/help' },
]

// ✅ Single computed source passed down (prevents “toRefs expects reactive object” if children do toRefs on props)
const navItems = computed(() => (isAdmin.value ? adminNavItems : studentNavItems))
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <!-- This smart /dashboard route redirects to admin or student based on server-side logic -->
                        <Link :href="route('dashboard')">
                        <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <!-- ✅ Always pass an Array (via computed) -->
            <NavMain :items="navItems" />
        </SidebarContent>

        <SidebarFooter>
            <!-- ✅ Always pass an Array (even if empty) -->
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>

    <!-- Render page content next to the sidebar -->
    <slot />
</template>
