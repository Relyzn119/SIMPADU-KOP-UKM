<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, KeyRound, Lock, Mail } from 'lucide-vue-next';

interface Props {
    token: string;
    email: string;
}

const props = defineProps<Props>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <AuthLayout title="Atur Ulang Kata Sandi" description="Masukkan kata sandi baru Anda untuk memperbarui akses akun">
        <Head title="Reset Kata Sandi" />

        <form @submit.prevent="submit" class="space-y-4">
            <!-- Email Readonly -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                    Alamat Email
                </label>
                <div class="relative">
                    <Mail class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" />
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        autocomplete="email" 
                        v-model="form.email" 
                        readonly 
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-gray-100 border border-gray-200 text-gray-600 text-xs sm:text-sm font-mono cursor-not-allowed"
                    />
                </div>
                <InputError :message="form.errors.email" />
            </div>

            <!-- New Password -->
            <div class="space-y-1.5">
                <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                    Kata Sandi Baru
                </label>
                <div class="relative">
                    <Lock class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" />
                    <input
                        id="password"
                        type="password"
                        name="password"
                        autocomplete="new-password"
                        v-model="form.password"
                        autofocus
                        placeholder="••••••••"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm placeholder:text-gray-400 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                    />
                </div>
                <InputError :message="form.errors.password" />
            </div>

            <!-- Confirm Password -->
            <div class="space-y-1.5">
                <label for="password_confirmation" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                    Konfirmasi Kata Sandi Baru
                </label>
                <div class="relative">
                    <Lock class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" />
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
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
                :disabled="form.processing"
            >
                <LoaderCircle v-if="form.processing" class="w-4 h-4 animate-spin" />
                <KeyRound v-else class="w-4 h-4 text-emerald-400" />
                <span>Simpan Kata Sandi Baru</span>
            </button>
        </form>
    </AuthLayout>
</template>

