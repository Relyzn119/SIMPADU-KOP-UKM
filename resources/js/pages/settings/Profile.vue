<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Briefcase, CreditCard, Mail, Save, ShieldCheck, User } from 'lucide-vue-next';
import { computed } from 'vue';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();

const page = usePage();
const authUser = computed(() => page.props.auth?.user as any);
const roleLabel = computed(() => page.props.auth?.role_label || 'Administrasi Koperasi');

const form = useForm({
    name: authUser.value?.name || '',
    email: authUser.value?.email || '',
    nip: authUser.value?.nip || '',
    jabatan: authUser.value?.jabatan || '',
});

const submit = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Pengaturan Profil Pengguna" />

        <div class="mx-auto max-w-4xl space-y-6">
            <!-- HEADER TITLE & TAB NAVIGATION -->
            <div class="shadow-2xs space-y-4 rounded-3xl border border-gray-200/70 bg-white p-6">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-gray-900">Pengaturan Akun & Profil</h1>
                        <p class="mt-0.5 text-xs text-gray-500">Kelola identitas diri, NIP, email, dan keamanan kata sandi Anda</p>
                    </div>

                    <!-- TAB SELECTOR -->
                    <div class="inline-flex rounded-2xl border border-gray-200/60 bg-gray-100/80 p-1 text-xs font-bold">
                        <Link :href="route('profile.edit')" class="shadow-2xs rounded-xl bg-white px-4 py-2 text-gray-900 transition">
                            Profil Pengguna
                        </Link>
                        <Link :href="route('password.edit')" class="rounded-xl px-4 py-2 text-gray-500 transition hover:text-gray-900">
                            Ubah Password
                        </Link>
                    </div>
                </div>
            </div>

            <!-- MAIN PROFILE FORM CARD -->
            <div class="shadow-2xs space-y-6 rounded-3xl border border-gray-200/70 bg-white p-6 sm:p-8">
                <!-- USER BADGE SUMMARY HEADER -->
                <div class="flex items-center gap-4 rounded-2xl border border-gray-100 bg-gray-50 p-4">
                    <div
                        class="shadow-xs flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-slate-900 text-base font-bold text-white"
                    >
                        {{ form.name.charAt(0) || 'U' }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2 text-sm font-bold text-gray-900 sm:text-base">
                            {{ form.name }}
                            <span
                                class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-[10px] font-extrabold text-emerald-700"
                            >
                                <ShieldCheck class="h-3 w-3 text-emerald-600" />
                                {{ roleLabel }}
                            </span>
                        </div>
                        <div class="mt-0.5 flex flex-wrap items-center gap-3 text-xs font-medium text-gray-500">
                            <span v-if="form.nip"
                                >NIP: <strong class="font-mono text-gray-700">{{ form.nip }}</strong></span
                            >
                            <span>Email: {{ form.email }}</span>
                        </div>
                    </div>
                </div>

                <!-- EDIT FORM -->
                <form @submit.prevent="submit" class="space-y-5">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <!-- Nama Lengkap -->
                        <div class="space-y-1.5">
                            <label for="name" class="block text-xs font-bold text-gray-700">
                                Nama Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <User class="pointer-events-none absolute left-3.5 top-3 h-4 w-4 text-gray-400" />
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    placeholder="Masukkan nama lengkap Anda"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-3.5 text-xs text-gray-900 transition placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                                />
                            </div>
                            <div v-if="form.errors.name" class="mt-1 text-xs font-medium text-rose-600">
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <!-- NIP / Nomor Identitas -->
                        <div class="space-y-1.5">
                            <label for="nip" class="block text-xs font-bold text-gray-700"> NIP / Nomor Identitas Pegawai </label>
                            <div class="relative">
                                <CreditCard class="pointer-events-none absolute left-3.5 top-3 h-4 w-4 text-gray-400" />
                                <input
                                    id="nip"
                                    v-model="form.nip"
                                    type="text"
                                    placeholder="Contoh: 19850612 201001 1 008"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-3.5 font-mono text-xs text-gray-900 transition placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                                />
                            </div>
                            <div v-if="form.errors.nip" class="mt-1 text-xs font-medium text-rose-600">
                                {{ form.errors.nip }}
                            </div>
                        </div>

                        <!-- Alamat Email -->
                        <div class="space-y-1.5">
                            <label for="email" class="block text-xs font-bold text-gray-700">
                                Alamat Email <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <Mail class="pointer-events-none absolute left-3.5 top-3 h-4 w-4 text-gray-400" />
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    placeholder="nama@sumutprov.go.id"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-3.5 text-xs text-gray-900 transition placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                                />
                            </div>
                            <div v-if="form.errors.email" class="mt-1 text-xs font-medium text-rose-600">
                                {{ form.errors.email }}
                            </div>
                        </div>

                        <!-- Jabatan -->
                        <div class="space-y-1.5">
                            <label for="jabatan" class="block text-xs font-bold text-gray-700"> Jabatan / Posisi Diri </label>
                            <div class="relative">
                                <Briefcase class="pointer-events-none absolute left-3.5 top-3 h-4 w-4 text-gray-400" />
                                <input
                                    id="jabatan"
                                    v-model="form.jabatan"
                                    type="text"
                                    placeholder="Contoh: Pengawas Koperasi Ahli Muda"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-3.5 text-xs text-gray-900 transition placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                                />
                            </div>
                            <div v-if="form.errors.jabatan" class="mt-1 text-xs font-medium text-rose-600">
                                {{ form.errors.jabatan }}
                            </div>
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
                            <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan Profil' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
