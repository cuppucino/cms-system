<script setup lang="ts">
import { ref } from 'vue'
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

// Lucide icons (unique; all exist in lucide-vue-next)
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
    Folder,         // Footer links
} from 'lucide-vue-next'

// --- types ---
interface User {
    id: number
    name: string
    email: string
    is_admin: boolean
}

// --- auth ---
const { props } = usePage()
const user = props.auth?.user as User | undefined
const isAdmin = ref(Boolean(user?.is_admin))

// --- nav items (grouped + ordered) ---
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
    { title: 'Payments', href: route('admin.payment.index'), icon: DollarSign },

    // Comms
    { title: 'Notifications', href: route('admin.notifications.index'), icon: Bell },

    // Analytics
    { title: 'Reports', href: route('admin.reports.index'), icon: BarChart2 },
]

const studentNavItems = [
    { title: 'Dashboard', href: route('student.dashboard'), icon: LayoutGrid },
]

// Footer links
// const footerNavItems = [

// ]
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                        <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="isAdmin ? adminNavItems : studentNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
