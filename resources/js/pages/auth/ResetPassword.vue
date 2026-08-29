<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { KeyRound, LoaderCircle, Lock, Mail } from 'lucide-vue-next';

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
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700"> Alamat Email </label>
                <div class="relative">
                    <Mail class="absolute left-3.5 top-3.5 h-4 w-4 text-gray-400" />
                    <input
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="email"
                        v-model="form.email"
                        readonly
                        class="w-full cursor-not-allowed rounded-xl border border-gray-200 bg-gray-100 py-2.5 pl-10 pr-4 font-mono text-xs text-gray-600 sm:text-sm"
                    />
                </div>
                <InputError :message="form.errors.email" />
            </div>

            <!-- New Password -->
            <div class="space-y-1.5">
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-700"> Kata Sandi Baru </label>
                <div class="relative">
                    <Lock class="absolute left-3.5 top-3.5 h-4 w-4 text-gray-400" />
                    <input
                        id="password"
                        type="password"
                        name="password"
                        autocomplete="new-password"
                        v-model="form.password"
                        autofocus
                        placeholder="••••••••"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-4 text-xs text-gray-900 transition duration-150 placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                    />
                </div>
                <InputError :message="form.errors.password" />
            </div>

            <!-- Confirm Password -->
            <div class="space-y-1.5">
                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                    Konfirmasi Kata Sandi Baru
                </label>
                <div class="relative">
                    <Lock class="absolute left-3.5 top-3.5 h-4 w-4 text-gray-400" />
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
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
                :disabled="form.processing"
            >
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                <KeyRound v-else class="h-4 w-4 text-emerald-400" />
                <span>Simpan Kata Sandi Baru</span>
            </button>
        </form>
    </AuthLayout>
</template>
