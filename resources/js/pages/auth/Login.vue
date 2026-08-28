<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, LogIn, Lock, Mail } from 'lucide-vue-next';

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

        <div v-if="status" class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-center text-xs font-semibold text-emerald-700">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
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
                        autofocus
                        tabindex="1"
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
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Kata Sandi
                    </label>
                    <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold" tabindex="5"> 
                        Lupa kata sandi? 
                    </TextLink>
                </div>
                <div class="relative">
                    <Lock class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" />
                    <input
                        id="password"
                        type="password"
                        required
                        tabindex="2"
                        autocomplete="current-password"
                        v-model="form.password"
                        placeholder="••••••••"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm placeholder:text-gray-400 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                    />
                </div>
                <InputError :message="form.errors.password" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between pt-1">
                <label for="remember" class="flex items-center gap-2 cursor-pointer">
                    <input 
                        id="remember" 
                        type="checkbox"
                        v-model="form.remember" 
                        tabindex="3"
                        class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 transition"
                    />
                    <span class="text-xs text-gray-600 font-medium">Ingat Sesi Saya</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button 
                type="submit" 
                class="w-full py-3 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm shadow-md shadow-slate-900/10 transition duration-150 active:scale-[0.99] disabled:opacity-50 flex items-center justify-center gap-2 mt-2" 
                tabindex="4" 
                :disabled="form.processing"
            >
                <LoaderCircle v-if="form.processing" class="w-4 h-4 animate-spin" />
                <LogIn v-else class="w-4 h-4 text-emerald-400" />
                <span>Masuk ke Aplikasi</span>
            </button>

            <!-- Register Link -->
            <div class="text-center text-xs text-gray-500 font-medium pt-2">
                Belum memiliki akun?
                <TextLink :href="route('register')" class="text-emerald-600 hover:text-emerald-700 font-bold ml-1" tabindex="6">
                    Daftar Sekarang
                </TextLink>
            </div>
        </form>
    </AuthBase>
</template>

