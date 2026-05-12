<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed } from "vue"; // Importamos o computed para a máscara funcionar

defineProps({
    contacts: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    title: "",
    contact_id: "",
    estimated_value: "", // O Laravel precisa receber assim: 70000.00
    expected_closed_at: "",
});

// Essa é a mágica do Vue para criar a máscara de dinheiro sem instalar bibliotecas
const valorFormatado = computed({
    get() {
        if (!form.estimated_value) return "";
        // Pega o valor real e transforma em texto R$ 0.000,00
        let val = form.estimated_value.toString().replace(/\D/g, "");
        val = (val / 100).toFixed(2) + "";
        val = val.replace(".", ",");
        val = val.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
        return "R$ " + val;
    },
    set(novoValor) {
        // Quando o usuário digita, limpamos o texto para salvar só os números e centavos
        let valLimpo = novoValor.replace(/\D/g, "");
        form.estimated_value = (valLimpo / 100).toFixed(2);
    },
});

const submit = () => {
    form.post(route("deals.store"));
};
</script>

<template>
    <Head title="Nova Negociação" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-serif font-bold text-2xl text-dwl-darkblue leading-tight"
            >
                Cadastrar Nova Negociação
            </h2>
        </template>

        <!-- Aumentei o padding vertical (py-12) e deixei o formulário mais largo (max-w-3xl) -->
        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <!-- Aumentei o padding interno do box (p-10) -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-10 border border-gray-200"
                >
                    <!-- Aumentei o espaço entre os campos (space-y-8) -->
                    <form @submit.prevent="submit" class="space-y-8">
                        <div>
                            <label
                                class="block text-sm font-bold text-gray-700 mb-1"
                                >Título da Negociação</label
                            >
                            <!-- Adicionei p-3 para deixar o campo mais "gordinho" e confortável -->
                            <input
                                v-model="form.title"
                                type="text"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 text-gray-800"
                                placeholder="Ex: Venda de 50 kits de Bioquímica"
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
                                class="block text-sm font-bold text-gray-700 mb-1"
                                >Cliente (Contato)</label
                            >
                            <select
                                v-model="form.contact_id"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 text-gray-800"
                            >
                                <option value="" disabled>
                                    Selecione um contato...
                                </option>
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label
                                    class="block text-sm font-bold text-gray-700 mb-1"
                                    >Valor Estimado</label
                                >
                                <!-- Trocamos para v-model="valorFormatado" e type="text" -->
                                <input
                                    v-model="valorFormatado"
                                    type="text"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 font-semibold text-gray-800"
                                    placeholder="R$ 0,00"
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
                                    class="block text-sm font-bold text-gray-700 mb-1"
                                    >Previsão de Fechamento</label
                                >
                                <input
                                    v-model="form.expected_closed_at"
                                    type="date"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 text-gray-800"
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
                            class="flex items-center justify-end space-x-4 border-t border-gray-100 pt-6 mt-6"
                        >
                            <Link
                                :href="route('deals.index')"
                                class="text-gray-500 hover:text-gray-800 font-medium px-4 py-2"
                            >
                                Cancelar
                            </Link>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex justify-center items-center px-6 py-2.5 bg-dwl-teal border border-transparent rounded-md font-semibold text-sm text-white hover:bg-dwl-darkblue focus:outline-none focus:ring-2 focus:ring-dwl-teal focus:ring-offset-2 transition-all disabled:opacity-50 shadow-sm"
                            >
                                Salvar Negociação
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
