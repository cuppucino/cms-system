<script setup lang="ts">
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, GraduationCap, Users, Notebook, CheckSquare, Calendar, Bell } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

// Define the User interface
interface User {
    id: number;
    name: string;
    email: string;
    is_admin: boolean;
    // Add other user properties as needed
}

// Get the user object from Inertia props with proper typing
const { props } = usePage();
const user = props.auth.user as User | undefined; // Type assertion with fallback to undefined

// Check if the logged-in user is an admin
const isAdmin = ref(user?.is_admin ?? false); // Use optional chaining and default to false

// Admin and student-specific navigation items
const adminNavItems = [
    {
        title: 'Dashboard',
        href: route('admin.dashboard'),
        icon: LayoutGrid,
    },
    {
        title: 'Users',
        href: route('admin.users.index'),
        icon: Users,
    },
    {
        title: 'Roles',
        href: route('admin.roles.index'),
        icon: Notebook,
    },
    {
        title: 'Gown',
        href: route('admin.gowns.index'),
        icon: GraduationCap,
    },
    {
        title: 'Sessions',
        href: route('admin.sessions.index'),
        icon: Calendar,
    },
    {
        title: 'Gown Collections',
        href: route('admin.gown-collections.index'),
        icon: CheckSquare,
    },
    {
        title: 'Attendance',
        href: route('admin.attendance.index'),
        icon: CheckSquare,
    },

    {
        title: 'Reports',
        href: route('admin.reports.index'),
        icon: BookOpen,
    },

    {
        title: 'Notifications',
        href: route('admin.notifications.index'),
        icon: Bell, // import Bell from lucide-vue-next
    },

];

const studentNavItems = [
    {
        title: 'Dashboard',
        href: route('student.dashboard'),
        icon: LayoutGrid,
    },

];

// Footer Nav Items
const footerNavItems = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
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
