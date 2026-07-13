<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref } from "vue";

defineProps({
    users: Array,
});

// Variáveis para controlar o aviso flutuante (Toast)
const showToast = ref(false);
const toastMessage = ref("");

const updateRole = (user, event) => {
    const newRole = event.target.value;
    if (
        confirm(`Tem certeza que deseja alterar a permissão de ${user.name}?`)
    ) {
        router.patch(
            route("users.update-role", user.id),
            { role: newRole },
            {
                preserveScroll: true,
                onSuccess: () => {
                    // Se der certo, configura a mensagem e exibe o Toast por 3 segundos
                    toastMessage.value = `Cargo de ${user.name} atualizado!`;
                    showToast.value = true;
                    setTimeout(() => {
                        showToast.value = false;
                    }, 3000);
                },
                onError: () => {
                    event.target.value = user.role;
                },
            },
        );
    } else {
        event.target.value = user.role;
    }
};

const formatRoleName = (role) => {
    const roles = {
        admin: "Administrador",
        supervisor: "Supervisor",
        seller: "Vendedor",
    };
    return roles[role] || role;
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
                                                user.role === 'admin',
                                            'bg-dwl-cyan/20 text-dwl-darkblue border-dwl-cyan':
                                                user.role === 'supervisor',
                                            'bg-gray-100 text-gray-600 border-gray-200':
                                                user.role === 'seller',
                                        }"
                                    >
                                        {{ formatRoleName(user.role) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <!-- Select visível apenas para o Admin e se não for ele mesmo -->
                                    <select
                                        v-if="
                                            $page.props.auth.user.role ===
                                                'admin' &&
                                            $page.props.auth.user.id !== user.id
                                        "
                                        @change="updateRole(user, $event)"
                                        :value="user.role"
                                        class="text-sm rounded border-gray-300 text-gray-700 shadow-sm focus:border-dwl-teal focus:ring-dwl-teal py-1 pl-3 pr-8"
                                    >
                                        <option value="admin">
                                            Administrador
                                        </option>
                                        <option value="supervisor">
                                            Supervisor
                                        </option>
                                        <option value="seller">Vendedor</option>
                                    </select>

                                    <!-- Mensagem de bloqueio para Supervisores ou para o próprio Admin -->
                                    <span
                                        v-else
                                        class="text-gray-400 text-sm italic"
                                    >
                                        {{
                                            $page.props.auth.user.id === user.id
                                                ? "Você"
                                                : "Apenas Leitura"
                                        }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Alerta Flutuante (Toast) -->
        <transition
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showToast"
                class="fixed bottom-6 right-6 flex items-center bg-dwl-lightgreen border border-dwl-teal text-dwl-darkblue px-4 py-3 rounded-lg shadow-lg z-50"
            >
                <!-- Ícone de Sucesso -->
                <svg
                    class="w-5 h-5 text-dwl-teal mr-3"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7"
                    ></path>
                </svg>
                <span class="font-bold text-sm">{{ toastMessage }}</span>
            </div>
        </transition>
    </AuthenticatedLayout>
</template>
