<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Lock, LogIn, Mail } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthBase title="Masuk ke Akun Anda" description="Masukkan kredensial akun SIMPADU KOP-UKM Anda untuk melanjutkan">
        <Head title="Masuk Aplikasi" />

        <div v-if="status" class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-center text-xs font-semibold text-emerald-700">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <!-- Email Input -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700"> Alamat Email </label>
                <div class="relative">
                    <Mail class="absolute left-3.5 top-3.5 h-4 w-4 text-gray-400" />
                    <input
                        id="email"
                        type="email"
                        required
                        autofocus
                        tabindex="1"
                        autocomplete="email"
                        v-model="form.email"
                        placeholder="nama@sumutprov.go.id"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-4 text-xs text-gray-900 transition duration-150 placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                    />
                </div>
                <InputError :message="form.errors.email" />
            </div>

            <!-- Password Input -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-700"> Kata Sandi </label>
                    <TextLink
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-xs font-semibold text-emerald-600 hover:text-emerald-700"
                        tabindex="5"
                    >
                        Lupa kata sandi?
                    </TextLink>
                </div>
                <div class="relative">
                    <Lock class="absolute left-3.5 top-3.5 h-4 w-4 text-gray-400" />
                    <input
                        id="password"
                        type="password"
                        required
                        tabindex="2"
                        autocomplete="current-password"
                        v-model="form.password"
                        placeholder="••••••••"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-4 text-xs text-gray-900 transition duration-150 placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                    />
                </div>
                <InputError :message="form.errors.password" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between pt-1">
                <label for="remember" class="flex cursor-pointer items-center gap-2">
                    <input
                        id="remember"
                        type="checkbox"
                        v-model="form.remember"
                        tabindex="3"
                        class="h-4 w-4 rounded border-gray-300 text-emerald-600 transition focus:ring-emerald-500"
                    />
                    <span class="text-xs font-medium text-gray-600">Ingat Sesi Saya</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="mt-2 flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-3 text-xs font-bold text-white shadow-md shadow-slate-900/10 transition duration-150 hover:bg-slate-800 active:scale-[0.99] disabled:opacity-50 sm:text-sm"
                tabindex="4"
                :disabled="form.processing"
            >
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                <LogIn v-else class="h-4 w-4 text-emerald-400" />
                <span>Masuk ke Aplikasi</span>
            </button>

            <!-- Register Link -->
            <div class="pt-2 text-center text-xs font-medium text-gray-500">
                Belum memiliki akun?
                <TextLink :href="route('register')" class="ml-1 font-bold text-emerald-600 hover:text-emerald-700" tabindex="6">
                    Daftar Sekarang
                </TextLink>
            </div>
        </form>
    </AuthBase>
</template>
