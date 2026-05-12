<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";

defineProps({ contacts: Array });

const excluirContato = (id) => {
    if (
        confirm(
            "Tem certeza que deseja remover este cliente? Esta ação não pode ser desfeita.",
        )
    ) {
        router.delete(route("contacts.destroy", id));
    }
};
</script>

<template>
    <Head title="Carteira de Clientes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2
                    class="font-serif font-bold text-2xl text-dwl-darkblue leading-tight"
                >
                    Carteira de Clientes
                </h2>
                <Link
                    :href="route('contacts.create')"
                    class="bg-dwl-teal text-white px-5 py-2.5 rounded-md hover:bg-dwl-darkblue transition font-semibold text-sm shadow-sm"
                >
                    + Novo Cliente
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    v-if="$page.props.errors.error"
                    class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm"
                >
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg
                                class="h-5 w-5 text-red-500"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700 font-medium">
                                {{ $page.props.errors.error }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200"
                >
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-dwl-lightgreen/30">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left font-semibold text-dwl-slate uppercase tracking-wider"
                                >
                                    Nome do Laboratório / Contato
                                </th>
                                <th
                                    class="px-6 py-4 text-left font-semibold text-dwl-slate uppercase tracking-wider"
                                >
                                    E-mail
                                </th>
                                <th
                                    class="px-6 py-4 text-left font-semibold text-dwl-slate uppercase tracking-wider"
                                >
                                    Telefone
                                </th>
                                <th
                                    class="px-6 py-4 text-center font-semibold text-dwl-slate uppercase tracking-wider"
                                >
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="contact in contacts"
                                :key="contact.id"
                                class="hover:bg-gray-50 transition"
                            >
                                <td
                                    class="px-6 py-4 font-bold text-dwl-darkblue"
                                >
                                    {{ contact.name }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ contact.email || "-" }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ contact.phone || "-" }}
                                </td>
                                <td class="px-6 py-4 text-center space-x-3">
                                    <Link
                                        :href="
                                            route('contacts.edit', contact.id)
                                        "
                                        class="text-dwl-teal hover:text-dwl-darkblue font-semibold"
                                        >Editar</Link
                                    >
                                    <button
                                        @click="excluirContato(contact.id)"
                                        class="text-red-600 hover:text-red-800 font-semibold"
                                    >
                                        Excluir
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
