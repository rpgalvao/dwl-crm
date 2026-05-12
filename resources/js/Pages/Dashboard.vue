<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    metrics: Object,
    recentDeals: Array,
});

const formatarMoeda = (valor) => {
    return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
    }).format(valor || 0);
};

const formatarData = (data) => {
    if (!data) return "-";
    const [ano, mes, dia] = data.split("T")[0].split("-");
    return `${dia}/${mes}/${ano}`;
};
</script>

<template>
    <Head title="Visão Geral" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-serif font-bold text-2xl text-dwl-darkblue leading-tight"
            >
                Visão Geral (Dashboard)
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"
                >
                    <div
                        class="bg-dwl-lightgreen/50 border border-dwl-teal/30 rounded-lg p-6 shadow-sm"
                    >
                        <p
                            class="text-sm font-bold text-dwl-teal uppercase tracking-wider mb-1"
                        >
                            Total em Vendas Ganhas
                        </p>
                        <p class="text-3xl font-bold text-dwl-darkblue">
                            {{ formatarMoeda(metrics.valorGanho) }}
                        </p>
                        <p class="text-xs text-gray-600 mt-2">
                            {{ metrics.ganhas }} negociações fechadas
                        </p>
                    </div>

                    <div
                        class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm"
                    >
                        <p
                            class="text-sm font-bold text-dwl-slate uppercase tracking-wider mb-1"
                        >
                            Valor em Negociação
                        </p>
                        <p class="text-3xl font-bold text-gray-800">
                            {{ formatarMoeda(metrics.valorFunil) }}
                        </p>
                        <p class="text-xs text-gray-600 mt-2">
                            {{ metrics.ativas }} propostas no funil
                        </p>
                    </div>

                    <div
                        class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm"
                    >
                        <p
                            class="text-sm font-bold text-dwl-slate uppercase tracking-wider mb-1"
                        >
                            Carteira de Clientes
                        </p>
                        <p class="text-3xl font-bold text-gray-800">
                            {{ metrics.totalClientes }}
                        </p>
                        <p class="text-xs text-gray-600 mt-2">
                            Laboratórios cadastrados
                        </p>
                    </div>
                </div>

                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200"
                >
                    <div
                        class="p-6 border-b border-gray-200 bg-gray-50 flex justify-between items-center"
                    >
                        <h3
                            class="font-serif font-bold text-lg text-dwl-darkblue"
                        >
                            Negociações Mais Recentes
                        </h3>
                        <Link
                            :href="route('deals.index')"
                            class="text-sm font-bold text-dwl-teal hover:text-dwl-darkblue"
                            >Ver Funil Completo &rarr;</Link
                        >
                    </div>

                    <div v-if="recentDeals.length > 0">
                        <table
                            class="min-w-full divide-y divide-gray-200 text-sm"
                        >
                            <thead class="bg-white">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left font-semibold text-dwl-slate uppercase tracking-wider"
                                    >
                                        Título
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left font-semibold text-dwl-slate uppercase tracking-wider"
                                    >
                                        Cliente
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center font-semibold text-dwl-slate uppercase tracking-wider"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right font-semibold text-dwl-slate uppercase tracking-wider"
                                    >
                                        Previsão
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right font-semibold text-dwl-slate uppercase tracking-wider"
                                    >
                                        Valor Estimado
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="deal in recentDeals"
                                    :key="deal.id"
                                    class="hover:bg-gray-50 transition"
                                >
                                    <td
                                        class="px-6 py-4 font-bold text-dwl-darkblue"
                                    >
                                        {{ deal.title }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ deal.contact?.name || "-" }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-bold border"
                                            :class="{
                                                'bg-dwl-lightgreen text-green-800 border-green-200':
                                                    deal.status === 'ganho',
                                                'bg-gray-100 text-gray-800 border-gray-200':
                                                    deal.status !== 'ganho',
                                            }"
                                        >
                                            {{ deal.status.toUpperCase() }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 text-right text-gray-500"
                                    >
                                        {{
                                            formatarData(
                                                deal.expected_closed_at,
                                            )
                                        }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-right font-bold text-dwl-teal"
                                    >
                                        {{
                                            formatarMoeda(deal.estimated_value)
                                        }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-else class="text-center py-10">
                        <p class="text-gray-500 italic">
                            Ainda não há negociações cadastradas.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
