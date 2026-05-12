<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Doughnut } from "vue-chartjs";
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from "chart.js";

// Registrando os componentes do Chart.js
ChartJS.register(ArcElement, Tooltip, Legend);

const props = defineProps({
    metrics: Object,
    chartData: Array,
    recentDeals: Array,
});

// Configuração do Gráfico com as cores da DWL
const chartConfig = {
    labels: ["Novos Leads", "Em Cotação", "Aprovação", "Ganhos"],
    datasets: [
        {
            backgroundColor: [
                "#70808F", // Cinza Slate (Novos)
                "#ACF0F2", // Ciano (Cotação)
                "#225378", // Azul Escuro (Aprovação)
                "#179680", // Teal/Verde (Ganhos)
            ],
            data: props.chartData,
            borderWidth: 2,
            hoverOffset: 4,
        },
    ],
};

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: "bottom",
            labels: {
                usePointStyle: true,
                padding: 20,
                font: { family: "'Open Sans', sans-serif", size: 12 },
            },
        },
    },
};

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

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 lg:col-span-1 flex flex-col"
                    >
                        <div class="p-6 border-b border-gray-200 bg-gray-50">
                            <h3
                                class="font-serif font-bold text-lg text-dwl-darkblue"
                            >
                                Propostas por Status
                            </h3>
                        </div>
                        <div
                            class="p-6 flex-1 flex justify-center items-center min-h-[300px]"
                        >
                            <Doughnut
                                :data="chartConfig"
                                :options="chartOptions"
                            />
                        </div>
                    </div>

                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 lg:col-span-2 flex flex-col"
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
                                >Ver Funil &rarr;</Link
                            >
                        </div>

                        <div
                            v-if="recentDeals.length > 0"
                            class="flex-1 overflow-x-auto"
                        >
                            <table
                                class="min-w-full divide-y divide-gray-200 text-sm h-full"
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
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-gray-200"
                                >
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
                                                class="px-3 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wider"
                                                :class="{
                                                    'bg-dwl-lightgreen text-dwl-teal border-dwl-teal/30':
                                                        deal.status === 'ganho',
                                                    'bg-gray-100 text-gray-600 border-gray-200':
                                                        deal.status !== 'ganho',
                                                }"
                                            >
                                                {{ deal.status }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-4 text-right text-gray-500 font-medium"
                                        >
                                            {{
                                                formatarData(
                                                    deal.expected_closed_at,
                                                )
                                            }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div
                            v-else
                            class="text-center py-10 flex-1 flex items-center justify-center"
                        >
                            <p class="text-gray-500 italic">
                                Ainda não há negociações cadastradas.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
