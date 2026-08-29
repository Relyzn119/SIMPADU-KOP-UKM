<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Eye, EyeOff, KeyRound, Lock, Save } from 'lucide-vue-next';
import { computed, ref } from 'vue';

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

        <div class="mx-auto max-w-4xl space-y-6">
            <!-- HEADER TITLE & TAB NAVIGATION -->
            <div class="shadow-2xs space-y-4 rounded-3xl border border-gray-200/70 bg-white p-6">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-gray-900">Pengaturan Keamanan Akun</h1>
                        <p class="mt-0.5 text-xs text-gray-500">Perbarui kata sandi Anda secara berkala demi keamanan akses data sistem</p>
                    </div>

                    <!-- TAB SELECTOR -->
                    <div class="inline-flex rounded-2xl border border-gray-200/60 bg-gray-100/80 p-1 text-xs font-bold">
                        <Link :href="route('profile.edit')" class="rounded-xl px-4 py-2 text-gray-500 transition hover:text-gray-900">
                            Profil Pengguna
                        </Link>
                        <Link :href="route('password.edit')" class="shadow-2xs rounded-xl bg-white px-4 py-2 text-gray-900 transition">
                            Ubah Password
                        </Link>
                    </div>
                </div>
            </div>

            <!-- MAIN PASSWORD FORM CARD -->
            <div class="shadow-2xs max-w-2xl space-y-6 rounded-3xl border border-gray-200/70 bg-white p-6 sm:p-8">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                    <div class="shadow-2xs flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-900 text-white">
                        <KeyRound class="h-5 w-5 text-emerald-400" />
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
                            <Lock class="pointer-events-none absolute left-3.5 top-3 h-4 w-4 text-gray-400" />
                            <input
                                id="current_password"
                                v-model="form.current_password"
                                :type="showCurrentPassword ? 'text' : 'password'"
                                required
                                autocomplete="current-password"
                                placeholder="Masukkan password lama Anda"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-10 text-xs text-gray-900 transition placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                            <button
                                type="button"
                                @click="showCurrentPassword = !showCurrentPassword"
                                class="absolute right-3 top-3 text-gray-400 transition hover:text-gray-600"
                            >
                                <EyeOff v-if="showCurrentPassword" class="h-4 w-4" />
                                <Eye v-else class="h-4 w-4" />
                            </button>
                        </div>
                        <div v-if="form.errors.current_password" class="mt-1 text-xs font-medium text-rose-600">
                            {{ form.errors.current_password }}
                        </div>
                    </div>

                    <!-- New Password -->
                    <div class="space-y-1.5">
                        <label for="password" class="block text-xs font-bold text-gray-700">
                            Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <KeyRound class="pointer-events-none absolute left-3.5 top-3 h-4 w-4 text-gray-400" />
                            <input
                                id="password"
                                v-model="form.password"
                                :type="showNewPassword ? 'text' : 'password'"
                                required
                                autocomplete="new-password"
                                placeholder="Masukkan password baru Anda"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-10 text-xs text-gray-900 transition placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                            <button
                                type="button"
                                @click="showNewPassword = !showNewPassword"
                                class="absolute right-3 top-3 text-gray-400 transition hover:text-gray-600"
                            >
                                <EyeOff v-if="showNewPassword" class="h-4 w-4" />
                                <Eye v-else class="h-4 w-4" />
                            </button>
                        </div>
                        <div v-if="form.errors.password" class="mt-1 text-xs font-medium text-rose-600">
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="space-y-1.5">
                        <label for="password_confirmation" class="block text-xs font-bold text-gray-700">
                            Konfirmasi Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <KeyRound class="pointer-events-none absolute left-3.5 top-3 h-4 w-4 text-gray-400" />
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                :type="showConfirmPassword ? 'text' : 'password'"
                                required
                                autocomplete="new-password"
                                placeholder="Ketik ulang password baru Anda"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-10 text-xs text-gray-900 transition placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                            <button
                                type="button"
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute right-3 top-3 text-gray-400 transition hover:text-gray-600"
                            >
                                <EyeOff v-if="showConfirmPassword" class="h-4 w-4" />
                                <Eye v-else class="h-4 w-4" />
                            </button>
                        </div>
                        <div v-if="form.errors.password_confirmation" class="mt-1 text-xs font-medium text-rose-600">
                            {{ form.errors.password_confirmation }}
                        </div>
                    </div>

                    <!-- SUBMIT ACTION -->
                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="shadow-xs inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800 disabled:opacity-50"
                        >
                            <Save class="h-4 w-4 text-emerald-400" />
                            <span>{{ form.processing ? 'Menyimpan...' : 'Perbarui Password' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
