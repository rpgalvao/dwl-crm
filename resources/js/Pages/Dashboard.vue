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
            <h2 class="font-serif font-bold text-2xl text-dwl-darkblue leading-tight">
                Visão Geral (Dashboard)
            </h2>
        </template>

        <!-- Reduzimos de py-12 para py-6 para diminuir o espaço do topo -->
        <div class="py-6">
            <!-- Reduzimos o space-y-6 para space-y-4 para aproximar as linhas -->
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

                <!-- Primeira Linha: Cards Menores (Mudamos de p-6 para p-4) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-dwl-lightgreen/50 border border-dwl-teal/30 rounded-lg p-4 shadow-sm">
                        <p class="text-xs font-bold text-dwl-teal uppercase tracking-wider mb-1">
                            Total em Vendas Ganhas
                        </p>
                        <p class="text-2xl font-bold text-dwl-darkblue">
                            {{ formatarMoeda(metrics.valorGanho) }}
                        </p>
                        <p class="text-xs text-gray-600 mt-1">
                            {{ metrics.ganhas }} negociações fechadas
                        </p>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <p class="text-xs font-bold text-dwl-slate uppercase tracking-wider mb-1">
                            Valor no Funil
                        </p>
                        <p class="text-2xl font-bold text-gray-800">
                            {{ formatarMoeda(metrics.valorFunil) }}
                        </p>
                        <p class="text-xs text-gray-600 mt-1">
                            {{ metrics.ativas }} propostas ativas
                        </p>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <p class="text-xs font-bold text-dwl-slate uppercase tracking-wider mb-1">
                            Carteira de Clientes
                        </p>
                        <p class="text-2xl font-bold text-gray-800">
                            {{ metrics.totalClientes }}
                        </p>
                        <p class="text-xs text-gray-600 mt-1">
                            Laboratórios cadastrados
                        </p>
                    </div>
                </div>

                <!-- Segunda Linha: Gráfico e Tabela (Diminuímos gap e paddings) -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                    <!-- Gráfico Menor -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 lg:col-span-1 flex flex-col">
                        <div class="p-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="font-serif font-bold text-base text-dwl-darkblue">
                                Propostas por Status
                            </h3>
                        </div>
                        <div class="p-4 flex-1 flex justify-center items-center min-h-[220px]">
                            <Doughnut :data="chartConfig" :options="chartOptions" />
                        </div>
                    </div>

                    <!-- Tabela de Negociações -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 lg:col-span-2 flex flex-col">
                        <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                            <h3 class="font-serif font-bold text-base text-dwl-darkblue">
                                Negociações Recentes
                            </h3>
                            <Link :href="route('deals.index')" class="text-xs font-bold text-dwl-teal hover:text-dwl-darkblue">
                                Ver Funil &rarr;
                            </Link>
                        </div>

                        <div v-if="recentDeals.length > 0" class="flex-1 overflow-x-auto">
                            <!-- Diminuímos a altura das células (py-4 para py-2.5) -->
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-white">
                                <tr>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-dwl-slate uppercase tracking-wider">Título</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-dwl-slate uppercase tracking-wider">Cliente</th>
                                    <th class="px-4 py-2.5 text-center text-xs font-semibold text-dwl-slate uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-2.5 text-right text-xs font-semibold text-dwl-slate uppercase tracking-wider">Previsão</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="deal in recentDeals" :key="deal.id" class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-2.5 font-bold text-dwl-darkblue">
                                        <Link :href="route('deals.edit', deal.id)" class="hover:text-dwl-teal hover:underline transition-colors">
                                            {{ deal.title }}
                                        </Link>
                                    </td>
                                    <td class="px-4 py-2.5 text-gray-600">{{ deal.contact?.name || "-" }}</td>
                                    <td class="px-4 py-2.5 text-center">
                                            <span class="px-2 py-1 rounded-md text-[10px] font-bold border uppercase tracking-wider"
                                                  :class="{
                                                    'bg-dwl-lightgreen text-dwl-teal border-dwl-teal/30': deal.status === 'ganho',
                                                    'bg-gray-100 text-gray-600 border-gray-200': deal.status !== 'ganho',
                                                }">
                                                {{ deal.status }}
                                            </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-right text-gray-500 font-medium">
                                        {{ formatarData(deal.expected_closed_at) }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center py-6 flex-1 flex items-center justify-center">
                            <p class="text-gray-500 text-sm italic">Ainda não há negociações cadastradas.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
