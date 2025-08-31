<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import AuthBase from '@/layouts/AuthLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { LoaderCircle, Eye, EyeOff, Info } from 'lucide-vue-next'
import { ref } from 'vue'

defineProps<{
    status?: string
    canResetPassword: boolean
}>()

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const showPassword = ref(false)
const capsOn = ref(false)

const onKeyEvent = (e: KeyboardEvent) => {
    // Simple caps detection; not perfect but helpful
    const isLetter = e.key.length === 1 && /[a-z]/i.test(e.key)
    if (isLetter) {
        const shift = e.getModifierState?.('Shift')
        const caps = e.getModifierState?.('CapsLock')
        // If caps is on OR (shift + lower) it might be upper; prefer caps state
        capsOn.value = Boolean(caps)
    }
}

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <AuthBase title="Log in to your account" description="Enter your email and password to continue">

        <Head title="Log in" />

        <!-- Status flash -->
        <div v-if="status"
            class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2 text-center text-sm font-medium text-emerald-700">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-5">
                <!-- Email -->
                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input id="email" type="email" required autofocus :tabindex="1" autocomplete="email"
                        v-model="form.email" placeholder="you@university.edu" />
                    <InputError :message="form.errors.email" />
                </div>

                <!-- Password with show/hide + caps hint -->
                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Password</Label>
                        <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-sm"
                            :tabindex="5">
                            Forgot password?
                        </TextLink>
                    </div>

                    <div class="relative">
                        <Input id="password" :type="showPassword ? 'text' : 'password'" required :tabindex="2"
                            autocomplete="current-password" v-model="form.password" placeholder="••••••••"
                            @keydown="onKeyEvent" />
                        <button type="button" :aria-label="showPassword ? 'Hide password' : 'Show password'"
                            class="absolute inset-y-0 right-2 inline-flex items-center justify-center rounded-md px-2 text-gray-500 hover:text-gray-700"
                            @click="showPassword = !showPassword" :tabindex="2">
                            <Eye v-if="!showPassword" class="h-4 w-4" />
                            <EyeOff v-else class="h-4 w-4" />
                        </button>
                    </div>

                    <div v-if="capsOn" class="flex items-center gap-1.5 text-xs text-amber-700">
                        <Info class="h-3.5 w-3.5" /> Caps Lock is on
                    </div>

                    <InputError :message="form.errors.password" />
                </div>

                <!-- Remember -->
                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" v-model="form.remember" :tabindex="3" />
                        <span>Remember me</span>
                    </Label>
                </div>

                <!-- Submit -->
                <Button type="submit"
                    class="mt-2 w-full bg-indigo-600 text-white hover:bg-indigo-500 focus:ring-2 focus:ring-indigo-400 disabled:opacity-50"
                    :tabindex="4" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="mr-1 h-4 w-4 animate-spin" />
                    Log in
                </Button>
            </div>
        </form>
    </AuthBase>
</template>
