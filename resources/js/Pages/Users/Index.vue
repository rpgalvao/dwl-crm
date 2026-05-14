<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    users: Array,
});

const form = useForm({});

const toggleAdmin = (user) => {
    if (confirm(`Tem certeza que deseja mudar a permissão de ${user.name}?`)) {
        form.patch(route("users.toggle-admin", user.id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Gerenciar Equipe" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-serif font-bold text-2xl text-dwl-darkblue leading-tight"
            >
                Equipe e Permissões
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200"
                >
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left font-semibold text-dwl-slate uppercase tracking-wider"
                                >
                                    Nome
                                </th>
                                <th
                                    class="px-6 py-3 text-left font-semibold text-dwl-slate uppercase tracking-wider"
                                >
                                    E-mail
                                </th>
                                <th
                                    class="px-6 py-3 text-center font-semibold text-dwl-slate uppercase tracking-wider"
                                >
                                    Nível de Acesso
                                </th>
                                <th
                                    class="px-6 py-3 text-right font-semibold text-dwl-slate uppercase tracking-wider"
                                >
                                    Ação
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="user in users"
                                :key="user.id"
                                class="hover:bg-gray-50 transition"
                            >
                                <td
                                    class="px-6 py-4 font-bold text-dwl-darkblue"
                                >
                                    {{ user.name }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ user.email }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-3 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wider"
                                        :class="{
                                            'bg-dwl-lightgreen text-dwl-teal border-dwl-teal/30':
                                                user.is_admin,
                                            'bg-gray-100 text-gray-600 border-gray-200':
                                                !user.is_admin,
                                        }"
                                    >
                                        {{
                                            user.is_admin
                                                ? "Administrador"
                                                : "Vendedor"
                                        }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button
                                        v-if="
                                            $page.props.auth.user.id !== user.id
                                        "
                                        @click="toggleAdmin(user)"
                                        class="text-sm font-bold underline transition-colors"
                                        :class="
                                            user.is_admin
                                                ? 'text-red-500 hover:text-red-700'
                                                : 'text-dwl-teal hover:text-dwl-darkblue'
                                        "
                                    >
                                        {{
                                            user.is_admin
                                                ? "Rebaixar para Vendedor"
                                                : "Promover a Admin"
                                        }}
                                    </button>
                                    <span
                                        v-else
                                        class="text-gray-400 text-sm italic"
                                        >Você</span
                                    >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
