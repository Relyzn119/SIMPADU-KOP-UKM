<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, UserPlus, User, Mail, Lock } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
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
                <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                    Nama Lengkap
                </label>
                <div class="relative">
                    <User class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" />
                    <input 
                        id="name" 
                        type="text" 
                        required 
                        autofocus 
                        tabindex="1" 
                        autocomplete="name" 
                        v-model="form.name" 
                        placeholder="Nama Lengkap Petugas" 
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm placeholder:text-gray-400 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                    />
                </div>
                <InputError :message="form.errors.name" />
            </div>

            <!-- Email Input -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                    Alamat Email
                </label>
                <div class="relative">
                    <Mail class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" />
                    <input 
                        id="email" 
                        type="email" 
                        required 
                        tabindex="2" 
                        autocomplete="email" 
                        v-model="form.email" 
                        placeholder="nama@sumutprov.go.id" 
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm placeholder:text-gray-400 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                    />
                </div>
                <InputError :message="form.errors.email" />
            </div>

            <!-- Password Input -->
            <div class="space-y-1.5">
                <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                    Kata Sandi
                </label>
                <div class="relative">
                    <Lock class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" />
                    <input
                        id="password"
                        type="password"
                        required
                        tabindex="3"
                        autocomplete="new-password"
                        v-model="form.password"
                        placeholder="••••••••"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm placeholder:text-gray-400 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                    />
                </div>
                <InputError :message="form.errors.password" />
            </div>

            <!-- Confirm Password Input -->
            <div class="space-y-1.5">
                <label for="password_confirmation" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                    Konfirmasi Kata Sandi
                </label>
                <div class="relative">
                    <Lock class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" />
                    <input
                        id="password_confirmation"
                        type="password"
                        required
                        tabindex="4"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="••••••••"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm placeholder:text-gray-400 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                    />
                </div>
                <InputError :message="form.errors.password_confirmation" />
            </div>

            <!-- Submit Button -->
            <button 
                type="submit" 
                class="w-full py-3 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm shadow-md shadow-slate-900/10 transition duration-150 active:scale-[0.99] disabled:opacity-50 flex items-center justify-center gap-2 mt-4" 
                tabindex="5" 
                :disabled="form.processing"
            >
                <LoaderCircle v-if="form.processing" class="w-4 h-4 animate-spin" />
                <UserPlus v-else class="w-4 h-4 text-emerald-400" />
                <span>Daftarkan Akun</span>
            </button>

            <!-- Login Link -->
            <div class="text-center text-xs text-gray-500 font-medium pt-2">
                Sudah memiliki akun?
                <TextLink :href="route('login')" class="text-emerald-600 hover:text-emerald-700 font-bold ml-1" tabindex="6">
                    Masuk Sekarang
                </TextLink>
            </div>
        </form>
    </AuthBase>
</template>

