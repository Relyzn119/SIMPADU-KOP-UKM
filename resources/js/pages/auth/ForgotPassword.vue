<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { KeyRound, LoaderCircle, Mail } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <AuthLayout title="Lupa Kata Sandi" description="Masukkan alamat email akun Anda untuk menerima tautan pemulihan kata sandi">
        <Head title="Lupa Kata Sandi" />

        <div v-if="status" class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-center text-xs font-semibold text-emerald-700">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700"> Alamat Email </label>
                <div class="relative">
                    <Mail class="absolute left-3.5 top-3.5 h-4 w-4 text-gray-400" />
                    <input
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="off"
                        v-model="form.email"
                        autofocus
                        placeholder="nama@sumutprov.go.id"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-4 text-xs text-gray-900 transition duration-150 placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                    />
                </div>
                <InputError :message="form.errors.email" />
            </div>

            <button
                type="submit"
                class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-3 text-xs font-bold text-white shadow-md shadow-slate-900/10 transition duration-150 hover:bg-slate-800 active:scale-[0.99] disabled:opacity-50 sm:text-sm"
                :disabled="form.processing"
            >
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                <KeyRound v-else class="h-4 w-4 text-emerald-400" />
                <span>Kirim Tautan Reset Password</span>
            </button>

            <div class="pt-2 text-center text-xs font-medium text-gray-500">
                Kembali ke halaman
                <TextLink :href="route('login')" class="ml-1 font-bold text-emerald-600 hover:text-emerald-700"> Masuk Akun </TextLink>
            </div>
        </form>
    </AuthLayout>
</template>
