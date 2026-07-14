<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    deal: Object,
    contacts: Array,
    sellers: Array, // Recebe os vendedores (só virá preenchido para o Admin)
});

// O form já começa preenchido com os dados atuais
const form = useForm({
    title: props.deal.title,
    contact_id: props.deal.contact_id,
    estimated_value: props.deal.estimated_value,
    status: props.deal.status,
    user_id: props.deal.user_id, // Recebe o dono atual da negociação
    expected_closed_at: props.deal.expected_closed_at
        ? props.deal.expected_closed_at.split("T")[0]
        : "",
});

const valorFormatado = computed({
    get() {
        if (!form.estimated_value) return "";
        let val = form.estimated_value.toString().replace(/\D/g, "");
        val = (val / 100).toFixed(2) + "";
        val = val.replace(".", ",");
        val = val.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
        return "R$ " + val;
    },
    set(novoValor) {
        let valLimpo = novoValor.replace(/\D/g, "");
        form.estimated_value = (valLimpo / 100).toFixed(2);
    },
});

const submit = () => {
    // Usamos .put() porque é uma atualização (update)
    form.put(route("deals.update", props.deal.id));
};
</script>

<template>
    <Head title="Editar Negociação" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Negociação
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 sm:p-12 border border-gray-200"
                >
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-1"
                                >Título da Negociação</label
                            >
                            <input
                                v-model="form.title"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 text-gray-700 transition-colors"
                            />
                            <div
                                v-if="form.errors.title"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.title }}
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-1"
                                >Cliente (Contato)</label
                            >
                            <select
                                v-model="form.contact_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 text-gray-700 transition-colors"
                            >
                                <option
                                    v-for="contact in contacts"
                                    :key="contact.id"
                                    :value="contact.id"
                                >
                                    {{ contact.name }}
                                </option>
                            </select>
                            <div
                                v-if="form.errors.contact_id"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.contact_id }}
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-1"
                            >
                                Responsável (Dono da Negociação)
                            </label>

                            <!-- Se for Admin, mostra o Select para transferir a negociação -->
                            <select
                                v-if="$page.props.auth.user.role === 'admin'"
                                v-model="form.user_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 text-gray-700 transition-colors"
                            >
                                <option
                                    v-for="seller in sellers"
                                    :key="seller.id"
                                    :value="seller.id"
                                >
                                    {{ seller.name }}
                                </option>
                            </select>

                            <!-- Se for vendedor comum, mostra apenas leitura com visual desabilitado (cinza) -->
                            <div
                                v-else
                                class="mt-1 block w-full rounded-md border border-gray-200 bg-gray-50 px-4 py-2.5 text-gray-600 font-medium cursor-not-allowed"
                            >
                                {{ deal.user?.name || "Desconhecido" }}
                            </div>

                            <div
                                v-if="form.errors.user_id"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.user_id }}
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-1"
                                >Fase do Funil (Status)</label
                            >
                            <select
                                v-model="form.status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 text-gray-700 transition-colors"
                            >
                                <option value="novo">Novo Lead</option>
                                <option value="cotacao">Em Cotação</option>
                                <option value="aprovacao">
                                    Aguardando Aprovação
                                </option>
                                <option value="ganho">Ganho (Fechado)</option>
                            </select>
                            <div
                                v-if="form.errors.status"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.status }}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-1"
                                    >Valor Estimado</label
                                >
                                <input
                                    v-model="valorFormatado"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 font-medium text-gray-800 transition-colors"
                                />
                                <div
                                    v-if="form.errors.estimated_value"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.estimated_value }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-1"
                                    >Previsão de Fechamento</label
                                >
                                <input
                                    v-model="form.expected_closed_at"
                                    type="date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 text-gray-700 transition-colors"
                                />
                                <div
                                    v-if="form.errors.expected_closed_at"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.expected_closed_at }}
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-end space-x-3 border-t border-gray-100 pt-6 mt-6"
                        >
                            <Link
                                :href="route('deals.show', deal.id)"
                                class="inline-flex justify-center items-center px-4 py-2.5 text-sm font-bold text-dwl-teal hover:text-dwl-darkblue transition-colors mr-auto"
                            >
                                📂 Painel da Negociação
                            </Link>
                            <Link
                                :href="route('deals.index')"
                                class="inline-flex justify-center items-center px-4 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors"
                            >
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex justify-center items-center px-6 py-2.5 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-700 transition-all disabled:opacity-50 shadow-sm"
                            >
                                Atualizar Negociação
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
