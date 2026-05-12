<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import draggable from "vuedraggable";
import { ref, watch } from "vue";

const props = defineProps({
    deals: {
        type: Array,
        required: true,
    },
});

// Transformamos nossas colunas em um estado reativo para o drag & drop funcionar
const board = ref([
    { id: "novo", title: "Novos Leads", bg: "bg-gray-100", deals: [] },
    { id: "cotacao", title: "Em Cotação", bg: "bg-blue-50", deals: [] },
    {
        id: "aprovacao",
        title: "Aguardando Aprovação",
        bg: "bg-yellow-50",
        deals: [],
    },
    { id: "ganho", title: "Ganhos (Fechado)", bg: "bg-green-50", deals: [] },
]);

// O watch "observa" os dados vindos do banco. Se houver novidade, ele distribui os cards nas colunas certas
watch(
    () => props.deals,
    (novasNegociacoes) => {
        board.value.forEach((coluna) => {
            coluna.deals = novasNegociacoes.filter(
                (deal) => deal.status === coluna.id,
            );
        });
    },
    { immediate: true },
);

// A mágica acontece aqui: avisamos o Laravel que o status mudou ao arrastar
const aoMoverCard = (evento, idDaNovaColuna) => {
    if (evento.added) {
        const negociacao = evento.added.element;

        router.patch(
            route("deals.update-status", negociacao.id),
            {
                status: idDaNovaColuna,
            },
            {
                preserveScroll: true, // Mantém a tela parada no mesmo lugar
            },
        );
    }
};

// Formata moeda com proteção contra valores nulos
const formatarMoeda = (valor) => {
    if (!valor) return "R$ 0,00";
    return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
    }).format(valor);
};

// Formata data protegendo contra fuso horário e datas nulas
const formatarData = (data) => {
    if (!data) return "Sem previsão";
    try {
        const apenasData = data.split("T")[0];
        const [ano, mes, dia] = apenasData.split("-");
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
                        v-for="coluna in board"
                        :key="coluna.id"
                        :class="[
                            'flex-shrink-0 w-80 rounded-lg p-4 min-h-[500px] flex flex-col',
                            coluna.bg,
                        ]"
                    >
                        <div class="flex justify-between items-center mb-4">
                            <h3
                                class="font-bold text-gray-700 uppercase text-sm"
                            >
                                {{ coluna.title }}
                            </h3>
                            <span
                                class="bg-white text-gray-600 text-xs font-bold px-2 py-1 rounded-full shadow-sm"
                            >
                                {{ coluna.deals.length }}
                            </span>
                        </div>

                        <draggable
                            v-model="coluna.deals"
                            group="negociacoes"
                            item-key="id"
                            class="flex-1 space-y-3"
                            @change="aoMoverCard($event, coluna.id)"
                        >
                            <template #item="{ element: deal }">
                                <div
                                    @click="
                                        router.get(route('deals.edit', deal.id))
                                    "
                                    class="bg-white border border-gray-200 rounded-md p-4 shadow-sm hover:shadow-md hover:ring-2 hover:ring-blue-300 transition cursor-grab active:cursor-grabbing border-l-4 border-l-blue-500"
                                >
                                    <h4
                                        class="font-bold text-gray-800 text-sm mb-1"
                                    >
                                        {{ deal.title }}
                                    </h4>
                                    <p class="text-xs text-gray-600 mb-2">
                                        <span class="font-semibold"
                                            >Cliente:</span
                                        >
                                        {{
                                            deal.contact?.name ||
                                            "Cliente Removido"
                                        }}
                                    </p>
                                    <div
                                        class="flex justify-between items-end mt-3"
                                    >
                                        <span
                                            class="text-sm font-bold text-green-600"
                                        >
                                            {{
                                                formatarMoeda(
                                                    deal.estimated_value,
                                                )
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
                            </template>
                        </draggable>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
