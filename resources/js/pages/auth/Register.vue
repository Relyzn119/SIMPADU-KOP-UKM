<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Lock, Mail, User, UserPlus } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'bidang_pengawasan',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthBase title="Buat Akun Baru" description="Daftarkan akun petugas / pengawas untuk mengakses aplikasi SIMPADU KOP-UKM">
        <Head title="Registrasi Akun" />

        <form @submit.prevent="submit" class="space-y-4">
            <!-- Name Input -->
            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-gray-700"> Nama Lengkap </label>
                <div class="relative">
                    <User class="absolute left-3.5 top-3.5 h-4 w-4 text-gray-400" />
                    <input
                        id="name"
                        type="text"
                        required
                        autofocus
                        tabindex="1"
                        autocomplete="name"
                        v-model="form.name"
                        placeholder="Nama Lengkap Petugas"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-4 text-xs text-gray-900 transition duration-150 placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                    />
                </div>
                <InputError :message="form.errors.name" />
            </div>

            <!-- Email Input -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700"> Alamat Email </label>
                <div class="relative">
                    <Mail class="absolute left-3.5 top-3.5 h-4 w-4 text-gray-400" />
                    <input
                        id="email"
                        type="email"
                        required
                        tabindex="2"
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
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-700"> Kata Sandi </label>
                <div class="relative">
                    <Lock class="absolute left-3.5 top-3.5 h-4 w-4 text-gray-400" />
                    <input
                        id="password"
                        type="password"
                        required
                        tabindex="3"
                        autocomplete="new-password"
                        v-model="form.password"
                        placeholder="••••••••"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-4 text-xs text-gray-900 transition duration-150 placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                    />
                </div>
                <InputError :message="form.errors.password" />
            </div>

            <!-- Confirm Password Input -->
            <div class="space-y-1.5">
                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                    Konfirmasi Kata Sandi
                </label>
                <div class="relative">
                    <Lock class="absolute left-3.5 top-3.5 h-4 w-4 text-gray-400" />
                    <input
                        id="password_confirmation"
                        type="password"
                        required
                        tabindex="4"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="••••••••"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-4 text-xs text-gray-900 transition duration-150 placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                    />
                </div>
                <InputError :message="form.errors.password_confirmation" />
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-3 text-xs font-bold text-white shadow-md shadow-slate-900/10 transition duration-150 hover:bg-slate-800 active:scale-[0.99] disabled:opacity-50 sm:text-sm"
                tabindex="5"
                :disabled="form.processing"
            >
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                <UserPlus v-else class="h-4 w-4 text-emerald-400" />
                <span>Daftarkan Akun</span>
            </button>

            <!-- Login Link -->
            <div class="pt-2 text-center text-xs font-medium text-gray-500">
                Sudah memiliki akun?
                <TextLink :href="route('login')" class="ml-1 font-bold text-emerald-600 hover:text-emerald-700" tabindex="6">
                    Masuk Sekarang
                </TextLink>
            </div>
        </form>
    </AuthBase>
</template>
