<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import {
    Lock,
    KeyRound,
    CheckCircle2,
    ShieldCheck,
    Save,
    Eye,
    EyeOff
} from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const page = usePage();
const authUser = computed(() => page.props.auth?.user as any);
const roleLabel = computed(() => page.props.auth?.role_label || 'Administrasi Koperasi');

const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Ubah Password Akun" />

        <div class="space-y-6 max-w-4xl mx-auto">
            
            <!-- HEADER TITLE & TAB NAVIGATION -->
            <div class="bg-white rounded-3xl p-6 border border-gray-200/70 shadow-2xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 tracking-tight">Pengaturan Keamanan Akun</h1>
                        <p class="text-xs text-gray-500 mt-0.5">Perbarui kata sandi Anda secara berkala demi keamanan akses data sistem</p>
                    </div>

                    <!-- TAB SELECTOR -->
                    <div class="inline-flex p-1 rounded-2xl bg-gray-100/80 border border-gray-200/60 text-xs font-bold">
                        <Link
                            :href="route('profile.edit')"
                            class="px-4 py-2 rounded-xl text-gray-500 hover:text-gray-900 transition"
                        >
                            Profil Pengguna
                        </Link>
                        <Link
                            :href="route('password.edit')"
                            class="px-4 py-2 rounded-xl bg-white text-gray-900 shadow-2xs transition"
                        >
                            Ubah Password
                        </Link>
                    </div>
                </div>
            </div>

            <!-- MAIN PASSWORD FORM CARD -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/70 shadow-2xs space-y-6 max-w-2xl">
                
                <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                    <div class="w-10 h-10 rounded-2xl bg-slate-900 text-white flex items-center justify-center shadow-2xs">
                        <KeyRound class="w-5 h-5 text-emerald-400" />
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Perbarui Kata Sandi</h2>
                        <p class="text-xs text-gray-400">Gunakan kombinasi minimal 8 karakter dengan huruf dan angka</p>
                    </div>
                </div>

                <!-- FORM FIELDS -->
                <form @submit.prevent="updatePassword" class="space-y-5">
                    
                    <!-- Current Password -->
                    <div class="space-y-1.5">
                        <label for="current_password" class="block text-xs font-bold text-gray-700">
                            Password Saat Ini <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <Lock class="w-4 h-4 text-gray-400 absolute left-3.5 top-3 pointer-events-none" />
                            <input
                                id="current_password"
                                v-model="form.current_password"
                                :type="showCurrentPassword ? 'text' : 'password'"
                                required
                                autocomplete="current-password"
                                placeholder="Masukkan password lama Anda"
                                class="w-full pl-10 pr-10 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm placeholder:text-gray-400 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition"
                            />
                            <button
                                type="button"
                                @click="showCurrentPassword = !showCurrentPassword"
                                class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 transition"
                            >
                                <EyeOff v-if="showCurrentPassword" class="w-4 h-4" />
                                <Eye v-else class="w-4 h-4" />
                            </button>
                        </div>
                        <div v-if="form.errors.current_password" class="text-xs text-rose-600 font-medium mt-1">
                            {{ form.errors.current_password }}
                        </div>
                    </div>

                    <!-- New Password -->
                    <div class="space-y-1.5">
                        <label for="password" class="block text-xs font-bold text-gray-700">
                            Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <KeyRound class="w-4 h-4 text-gray-400 absolute left-3.5 top-3 pointer-events-none" />
                            <input
                                id="password"
                                v-model="form.password"
                                :type="showNewPassword ? 'text' : 'password'"
                                required
                                autocomplete="new-password"
                                placeholder="Masukkan password baru Anda"
                                class="w-full pl-10 pr-10 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm placeholder:text-gray-400 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition"
                            />
                            <button
                                type="button"
                                @click="showNewPassword = !showNewPassword"
                                class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 transition"
                            >
                                <EyeOff v-if="showNewPassword" class="w-4 h-4" />
                                <Eye v-else class="w-4 h-4" />
                            </button>
                        </div>
                        <div v-if="form.errors.password" class="text-xs text-rose-600 font-medium mt-1">
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="space-y-1.5">
                        <label for="password_confirmation" class="block text-xs font-bold text-gray-700">
                            Konfirmasi Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <KeyRound class="w-4 h-4 text-gray-400 absolute left-3.5 top-3 pointer-events-none" />
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                :type="showConfirmPassword ? 'text' : 'password'"
                                required
                                autocomplete="new-password"
                                placeholder="Ketik ulang password baru Anda"
                                class="w-full pl-10 pr-10 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm placeholder:text-gray-400 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition"
                            />
                            <button
                                type="button"
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 transition"
                            >
                                <EyeOff v-if="showConfirmPassword" class="w-4 h-4" />
                                <Eye v-else class="w-4 h-4" />
                            </button>
                        </div>
                        <div v-if="form.errors.password_confirmation" class="text-xs text-rose-600 font-medium mt-1">
                            {{ form.errors.password_confirmation }}
                        </div>
                    </div>

                    <!-- SUBMIT ACTION -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition disabled:opacity-50"
                        >
                            <Save class="w-4 h-4 text-emerald-400" />
                            <span>{{ form.processing ? 'Menyimpan...' : 'Perbarui Password' }}</span>
                        </button>
                    </div>
                </form>

            </div>

        </div>
    </AuthenticatedLayout>
</template>
