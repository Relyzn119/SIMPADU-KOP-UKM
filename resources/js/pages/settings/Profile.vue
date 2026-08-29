<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import {
    User,
    Mail,
    CreditCard,
    Briefcase,
    ShieldCheck,
    Building2,
    Lock,
    Save,
    CheckCircle2
} from 'lucide-vue-next';

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

        <div class="space-y-6 max-w-4xl mx-auto">
            
            <!-- HEADER TITLE & TAB NAVIGATION -->
            <div class="bg-white rounded-3xl p-6 border border-gray-200/70 shadow-2xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 tracking-tight">Pengaturan Akun & Profil</h1>
                        <p class="text-xs text-gray-500 mt-0.5">Kelola identitas diri, NIP, email, dan keamanan kata sandi Anda</p>
                    </div>

                    <!-- TAB SELECTOR -->
                    <div class="inline-flex p-1 rounded-2xl bg-gray-100/80 border border-gray-200/60 text-xs font-bold">
                        <Link
                            :href="route('profile.edit')"
                            class="px-4 py-2 rounded-xl bg-white text-gray-900 shadow-2xs transition"
                        >
                            Profil Pengguna
                        </Link>
                        <Link
                            :href="route('password.edit')"
                            class="px-4 py-2 rounded-xl text-gray-500 hover:text-gray-900 transition"
                        >
                            Ubah Password
                        </Link>
                    </div>
                </div>
            </div>

            <!-- MAIN PROFILE FORM CARD -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/70 shadow-2xs space-y-6">
                
                <!-- USER BADGE SUMMARY HEADER -->
                <div class="flex items-center gap-4 p-4 rounded-2xl bg-gray-50 border border-gray-100">
                    <div class="w-12 h-12 rounded-full bg-slate-900 text-white font-bold text-base flex items-center justify-center shadow-xs shrink-0">
                        {{ form.name.charAt(0) || 'U' }}
                    </div>
                    <div>
                        <div class="font-bold text-gray-900 text-sm sm:text-base flex items-center gap-2">
                            {{ form.name }}
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-extrabold border border-emerald-200">
                                <ShieldCheck class="w-3 h-3 text-emerald-600" />
                                {{ roleLabel }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-500 font-medium mt-0.5 flex flex-wrap items-center gap-3">
                            <span v-if="form.nip">NIP: <strong class="font-mono text-gray-700">{{ form.nip }}</strong></span>
                            <span>Email: {{ form.email }}</span>
                        </div>
                    </div>
                </div>

                <!-- EDIT FORM -->
                <form @submit.prevent="submit" class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        
                        <!-- Nama Lengkap -->
                        <div class="space-y-1.5">
                            <label for="name" class="block text-xs font-bold text-gray-700">
                                Nama Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <User class="w-4 h-4 text-gray-400 absolute left-3.5 top-3 pointer-events-none" />
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    placeholder="Masukkan nama lengkap Anda"
                                    class="w-full pl-10 pr-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm placeholder:text-gray-400 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition"
                                />
                            </div>
                            <div v-if="form.errors.name" class="text-xs text-rose-600 font-medium mt-1">
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <!-- NIP / Nomor Identitas -->
                        <div class="space-y-1.5">
                            <label for="nip" class="block text-xs font-bold text-gray-700">
                                NIP / Nomor Identitas Pegawai
                            </label>
                            <div class="relative">
                                <CreditCard class="w-4 h-4 text-gray-400 absolute left-3.5 top-3 pointer-events-none" />
                                <input
                                    id="nip"
                                    v-model="form.nip"
                                    type="text"
                                    placeholder="Contoh: 19850612 201001 1 008"
                                    class="w-full pl-10 pr-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm font-mono placeholder:text-gray-400 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition"
                                />
                            </div>
                            <div v-if="form.errors.nip" class="text-xs text-rose-600 font-medium mt-1">
                                {{ form.errors.nip }}
                            </div>
                        </div>

                        <!-- Alamat Email -->
                        <div class="space-y-1.5">
                            <label for="email" class="block text-xs font-bold text-gray-700">
                                Alamat Email <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <Mail class="w-4 h-4 text-gray-400 absolute left-3.5 top-3 pointer-events-none" />
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    placeholder="nama@sumutprov.go.id"
                                    class="w-full pl-10 pr-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm placeholder:text-gray-400 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition"
                                />
                            </div>
                            <div v-if="form.errors.email" class="text-xs text-rose-600 font-medium mt-1">
                                {{ form.errors.email }}
                            </div>
                        </div>

                        <!-- Jabatan -->
                        <div class="space-y-1.5">
                            <label for="jabatan" class="block text-xs font-bold text-gray-700">
                                Jabatan / Posisi Diri
                            </label>
                            <div class="relative">
                                <Briefcase class="w-4 h-4 text-gray-400 absolute left-3.5 top-3 pointer-events-none" />
                                <input
                                    id="jabatan"
                                    v-model="form.jabatan"
                                    type="text"
                                    placeholder="Contoh: Pengawas Koperasi Ahli Muda"
                                    class="w-full pl-10 pr-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm placeholder:text-gray-400 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition"
                                />
                            </div>
                            <div v-if="form.errors.jabatan" class="text-xs text-rose-600 font-medium mt-1">
                                {{ form.errors.jabatan }}
                            </div>
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
                            <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan Profil' }}</span>
                        </button>
                    </div>
                </form>

            </div>

        </div>
    </AuthenticatedLayout>
</template>
