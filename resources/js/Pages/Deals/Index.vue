<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
    deals: {
        type: Array,
        required: true,
    },
});

const columns = [
    { id: "novo", title: "Novos Leads", bg: "bg-gray-100" },
    { id: "cotacao", title: "Em Cotação", bg: "bg-blue-50" },
    { id: "aprovacao", title: "Aguardando Aprovação", bg: "bg-yellow-50" },
    { id: "ganho", title: "Ganhos (Fechado)", bg: "bg-green-50" },
];

const getDealsByStatus = (status) => {
    return props.deals.filter((deal) => deal.status === status);
};

// Formata moeda com proteção contra valores nulos
const formatarMoeda = (valor) => {
    if (!valor) return "R$ 0,00";
    return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
    }).format(valor);
};

// Formata data de forma blindada cortando o texto exato
const formatarData = (data) => {
    if (!data) return "Sem previsão";

    try {
        // Separa o texto pelo "T" (se existir) e pega a primeira parte (YYYY-MM-DD)
        const apenasData = data.split("T")[0];

        // Fatiamos nos tracinhos
        const [ano, mes, dia] = apenasData.split("-");

        // Retornamos no formato brasileiro
        return `${dia}/${mes}/${ano}`;
    } catch (e) {
        return "Erro na data";
    }
};
</script>

<template>
    <Head title="Funil de Vendas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Funil de Vendas (CRM)
                </h2>
                <Link
                    :href="route('deals.create')"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition font-semibold text-sm shadow-sm"
                >
                    + Nova Negociação
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex overflow-x-auto gap-6 pb-4 items-start">
                    <div
                        v-for="column in columns"
                        :key="column.id"
                        :class="[
                            'flex-shrink-0 w-80 rounded-lg p-4 min-h-[500px]',
                            column.bg,
                        ]"
                    >
                        <div class="flex justify-between items-center mb-4">
                            <h3
                                class="font-bold text-gray-700 uppercase text-sm"
                            >
                                {{ column.title }}
                            </h3>
                            <span
                                class="bg-white text-gray-600 text-xs font-bold px-2 py-1 rounded-full shadow-sm"
                            >
                                {{ getDealsByStatus(column.id).length }}
                            </span>
                        </div>

                        <div class="space-y-3">
                            <div
                                v-for="deal in getDealsByStatus(column.id)"
                                :key="deal.id"
                                class="bg-white border border-gray-200 rounded-md p-4 shadow-sm hover:shadow-md transition cursor-pointer border-l-4 border-l-blue-500"
                            >
                                <h4
                                    class="font-bold text-gray-800 text-sm mb-1"
                                >
                                    {{ deal.title }}
                                </h4>
                                <p class="text-xs text-gray-600 mb-2">
                                    <span class="font-semibold">Cliente:</span>
                                    {{
                                        deal.contact?.name || "Cliente Removido"
                                    }}
                                </p>
                                <div
                                    class="flex justify-between items-end mt-3"
                                >
                                    <span
                                        class="text-sm font-bold text-green-600"
                                    >
                                        {{
                                            formatarMoeda(deal.estimated_value)
                                        }}
                                    </span>
                                    <span
                                        class="text-[10px] text-gray-500 bg-gray-100 border border-gray-200 px-2 py-1 rounded"
                                    >
                                        Prev:
                                        {{
                                            formatarData(
                                                deal.expected_closed_at,
                                            )
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
