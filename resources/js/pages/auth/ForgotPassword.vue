<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Mail, KeyRound } from 'lucide-vue-next';

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

        <div v-if="status" class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-center text-xs font-semibold text-emerald-700">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
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
                        autocomplete="off" 
                        v-model="form.email" 
                        autofocus 
                        placeholder="nama@sumutprov.go.id" 
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm placeholder:text-gray-400 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                    />
                </div>
                <InputError :message="form.errors.email" />
            </div>

            <button 
                type="submit" 
                class="w-full py-3 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm shadow-md shadow-slate-900/10 transition duration-150 active:scale-[0.99] disabled:opacity-50 flex items-center justify-center gap-2 mt-4" 
                :disabled="form.processing"
            >
                <LoaderCircle v-if="form.processing" class="w-4 h-4 animate-spin" />
                <KeyRound v-else class="w-4 h-4 text-emerald-400" />
                <span>Kirim Tautan Reset Password</span>
            </button>

            <div class="text-center text-xs text-gray-500 font-medium pt-2">
                Kembali ke halaman
                <TextLink :href="route('login')" class="text-emerald-600 hover:text-emerald-700 font-bold ml-1">
                    Masuk Akun
                </TextLink>
            </div>
        </form>
    </AuthLayout>
</template>

