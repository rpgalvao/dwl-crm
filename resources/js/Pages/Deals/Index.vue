<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import draggable from "vuedraggable";
import { ref, watch } from "vue";

const props = defineProps({
    deals: {
        type: [Array, Object], // Agora aceita tanto lista simples quanto caixas agrupadas
        required: true,
    },
    sellers: Array, // Lista de vendedores para o filtro
    filters: Object, // Guarda o vendedor que está selecionado atualmente
});

const selectedSeller = ref(props.filters?.seller_id || "");

// Função que recarrega a página aplicando o filtro
const aplicarFiltro = () => {
    router.get(
        route("deals.index"),
        { seller_id: selectedSeller.value },
        { preserveState: true, replace: true },
    );
};

const board = ref([
    { id: "novo", title: "Novos Leads", bg: "bg-gray-100", deals: [] },
    { id: "cotacao", title: "Em Cotação", bg: "bg-dwl-cyan/20", deals: [] },
    {
        id: "aprovacao",
        title: "Aguardando Aprovação",
        bg: "bg-dwl-slate/10",
        deals: [],
    },
    {
        id: "ganho",
        title: "Ganhos (Fechado)",
        bg: "bg-dwl-lightgreen",
        deals: [],
    },
]);

watch(
    () => props.deals,
    (novasNegociacoes) => {
        board.value.forEach((coluna) => {
            // Se o Laravel mandou uma lista simples, filtramos.
            // Se mandou já agrupado, pegamos direto da "caixa" correspondente à coluna.
            if (Array.isArray(novasNegociacoes)) {
                coluna.deals = novasNegociacoes.filter(
                    (deal) => deal.status === coluna.id,
                );
            } else {
                coluna.deals = novasNegociacoes[coluna.id] || [];
            }
        });
    },
    { immediate: true },
);

const aoMoverCard = (evento, idDaNovaColuna) => {
    if (evento.added) {
        const negociacao = evento.added.element;
        router.patch(
            route("deals.update-status", negociacao.id),
            {
                status: idDaNovaColuna,
            },
            {
                preserveScroll: true,
            },
        );
    }
};

const formatarMoeda = (valor) => {
    if (!valor) return "R$ 0,00";
    return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
    }).format(valor);
};

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
                <h2
                    class="font-serif font-bold text-2xl text-dwl-darkblue leading-tight"
                >
                    Funil de Vendas (CRM)
                </h2>

                <div class="flex items-center space-x-4">
                    <!-- Filtro de Vendedores (Apenas Admin) -->
                    <select
                        v-if="
                            ['admin', 'supervisor'].includes(
                                $page.props.auth.user.role,
                            ) && sellers.length > 0
                        "
                        v-model="selectedSeller"
                        @change="aplicarFiltro"
                        class="rounded-md border-gray-300 shadow-sm focus:border-dwl-teal focus:ring-dwl-teal text-sm py-2"
                    >
                        <option value="">Todos os Vendedores</option>
                        <option
                            v-for="seller in sellers"
                            :key="seller.id"
                            :value="seller.id"
                        >
                            {{ seller.name }}
                        </option>
                    </select>

                    <Link
                        :href="route('deals.create')"
                        class="bg-dwl-teal text-white px-5 py-2.5 rounded-md hover:bg-dwl-darkblue transition font-semibold text-sm shadow-sm"
                    >
                        + Nova Negociação
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex overflow-x-auto gap-6 pb-4 items-start">
                    <div
                        v-for="coluna in board"
                        :key="coluna.id"
                        :class="[
                            'flex-shrink-0 w-80 rounded-lg p-4 min-h-[500px] flex flex-col border border-gray-100',
                            coluna.bg,
                        ]"
                    >
                        <div class="flex justify-between items-center mb-4">
                            <h3
                                class="font-bold text-dwl-darkblue uppercase text-sm"
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
                                    class="bg-white border border-gray-200 rounded-md p-4 shadow-sm hover:shadow-md hover:ring-2 hover:ring-dwl-cyan transition cursor-grab active:cursor-grabbing border-l-4 border-l-dwl-darkblue"
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
                                    <!-- Etiqueta do Dono da Negociação (Visível para o Admin) -->
                                    <div
                                        v-if="
                                            ['admin', 'supervisor'].includes(
                                                $page.props.auth.user.role,
                                            )
                                        "
                                        class="mt-2 flex items-center text-[11px] text-gray-500 bg-gray-50 px-2 py-1 rounded w-fit border border-gray-100"
                                    >
                                        <svg
                                            class="w-3 h-3 mr-1 text-dwl-slate"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                            ></path>
                                        </svg>
                                        {{ deal.user?.name || "Desconhecido" }}
                                    </div>
                                    <div
                                        class="flex justify-between items-end mt-3"
                                    >
                                        <span
                                            class="text-sm font-bold text-dwl-teal"
                                        >
                                            {{
                                                formatarMoeda(
                                                    deal.estimated_value,
                                                )
                                            }}
                                        </span>
                                        <span
                                            class="text-[10px] text-gray-500 bg-gray-50 border border-gray-200 px-2 py-1 rounded"
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
