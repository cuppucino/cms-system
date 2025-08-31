<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>

    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="flex min-h-screen flex-col bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <!-- Top nav -->
        <header class="w-full p-6 lg:px-12 flex justify-between items-center">
            <h1 class="text-lg font-bold tracking-tight">
                Convocation Management System
            </h1>
            <nav class="flex items-center gap-4 text-sm">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                    class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white shadow hover:bg-indigo-500">
                Dashboard
                </Link>
                <template v-else>
                    <Link :href="route('login')"
                        class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white shadow hover:bg-indigo-500">
                    Log in
                    </Link>
                </template>
            </nav>
        </header>

        <!-- Hero -->
        <main class="flex flex-1 flex-col items-center justify-center text-center p-6">
            <h2 class="text-3xl font-extrabold sm:text-4xl lg:text-5xl">
                Welcome to Your Convocation Portal
            </h2>
            <p class="mt-4 max-w-2xl text-gray-600 dark:text-gray-400">
                Manage registrations, gown collections, invitations, and attendance —
                all in one simple platform designed for graduates and administrators.
            </p>

            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <Link :href="route('login')"
                    class="rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white shadow hover:bg-indigo-500"
                    v-if="!$page.props.auth.user">
                Get Started
                </Link>
                <Link :href="route('dashboard')" v-else
                    class="rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white shadow hover:bg-indigo-500">
                Go to Dashboard
                </Link>
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-4 text-center text-xs text-gray-500 dark:text-gray-400">
            &copy; {{ new Date().getFullYear() }} Convocation Management System. All
            rights reserved.
        </footer>
    </div>
</template>
