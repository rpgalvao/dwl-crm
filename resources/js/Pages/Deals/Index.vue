<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// Aqui recebemos os dados enviados pelo DealController
defineProps({
    deals: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="Funil de Vendas" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Funil de Vendas (CRM)</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                    <h3 class="text-lg font-bold mb-4">Negociações em Andamento</h3>

                    <!-- Grid para exibir os cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <!-- Loop do Vue iterando sobre a variável 'deals' -->
                        <div
                            v-for="deal in deals"
                            :key="deal.id"
                            class="border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition"
                        >
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold text-gray-800">{{ deal.title }}</h4>
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded">
                                    {{ deal.status }}
                                </span>
                            </div>

                            <p class="text-sm text-gray-600 mb-1">
                                <span class="font-semibold">Cliente:</span> {{ deal.contact.name }}
                            </p>

                            <p class="text-sm text-gray-600 mb-3">
                                <span class="font-semibold">Valor Estimado:</span>
                                R$ {{ deal.estimated_value }}
                            </p>

                            <div class="text-xs text-gray-500">
                                Previsão de Fechamento: {{ new Date(deal.expected_closed_at).toLocaleDateString('pt-BR') }}
                            </div>
                        </div>

                    </div>

                    <!-- Mensagem caso não tenha nada no banco -->
                    <div v-if="deals.length === 0" class="text-gray-500 mt-4">
                        Nenhuma negociação encontrada.
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
