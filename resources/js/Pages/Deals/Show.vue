<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { watch } from "vue";

const props = defineProps({
    deal: Object,
    products: Array,
});

// Formulário para adicionar um novo item
const form = useForm({
    product_id: "",
    quantity: 1,
    unit_price: "",
});

// A mágica: Fica "vigiando" a escolha do produto. Se escolher um, preenche o preço automático!
watch(
    () => form.product_id,
    (novoProdutoId) => {
        const produtoEscolhido = props.products.find(
            (p) => p.id === novoProdutoId,
        );
        if (produtoEscolhido) {
            form.unit_price = produtoEscolhido.price;
        }
    },
);

const submitItem = () => {
    form.post(route("deals.items.store", props.deal.id), {
        preserveScroll: true, // Não deixa a tela pular para o topo
        onSuccess: () => form.reset(), // Limpa os campos após adicionar com sucesso
    });
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
    <Head :title="`Detalhes - ${deal.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2
                    class="font-serif font-bold text-2xl text-dwl-darkblue leading-tight"
                >
                    {{ deal.title }}
                </h2>
                <div class="space-x-3">
                    <Link
                        :href="route('deals.edit', deal.id)"
                        class="text-sm font-semibold text-dwl-slate hover:text-dwl-darkblue transition"
                    >
                        Editar Dados
                    </Link>
                    <Link
                        :href="route('deals.index')"
                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition font-semibold text-sm"
                    >
                        Voltar ao Funil
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200 flex flex-wrap gap-6 justify-between items-center"
                >
                    <div>
                        <p
                            class="text-xs text-dwl-slate font-bold uppercase tracking-wider mb-1"
                        >
                            Cliente
                        </p>
                        <p class="text-lg font-bold text-dwl-darkblue">
                            {{ deal.contact?.name }}
                        </p>
                    </div>
                    <div>
                        <p
                            class="text-xs text-dwl-slate font-bold uppercase tracking-wider mb-1"
                        >
                            Status no Funil
                        </p>
                        <span
                            class="bg-dwl-cyan/20 text-dwl-darkblue px-3 py-1 rounded-full text-sm font-bold border border-dwl-cyan"
                        >
                            {{ deal.status.toUpperCase() }}
                        </span>
                    </div>
                    <div>
                        <p
                            class="text-xs text-dwl-slate font-bold uppercase tracking-wider mb-1"
                        >
                            Previsão
                        </p>
                        <p class="text-lg font-medium text-gray-800">
                            {{ formatarData(deal.expected_closed_at) }}
                        </p>
                    </div>
                    <div class="text-right border-l pl-6">
                        <p
                            class="text-xs text-dwl-slate font-bold uppercase tracking-wider mb-1"
                        >
                            Valor Total Estimado
                        </p>
                        <p class="text-3xl font-bold text-dwl-teal">
                            {{ formatarMoeda(deal.estimated_value) }}
                        </p>
                    </div>
                </div>

                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200"
                >
                    <div
                        class="p-6 bg-dwl-lightgreen/30 border-b border-gray-200"
                    >
                        <h3
                            class="font-serif font-bold text-lg text-dwl-darkblue mb-4"
                        >
                            Adicionar Reagentes ao Pacote
                        </h3>

                        <form
                            @submit.prevent="submitItem"
                            class="flex flex-wrap items-end gap-4"
                        >
                            <div class="flex-1 min-w-[250px]">
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-1"
                                    >Catálogo de Reagentes</label
                                >
                                <select
                                    v-model="form.product_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-dwl-teal focus:ring-dwl-teal px-4 py-2.5 text-gray-700"
                                >
                                    <option value="" disabled>
                                        Selecione um produto...
                                    </option>
                                    <option
                                        v-for="product in products"
                                        :key="product.id"
                                        :value="product.id"
                                    >
                                        {{ product.name }} ({{ product.sku }})
                                    </option>
                                </select>
                            </div>

                            <div class="w-24">
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-1"
                                    >Qtd</label
                                >
                                <input
                                    v-model="form.quantity"
                                    type="number"
                                    min="1"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-dwl-teal focus:ring-dwl-teal px-4 py-2.5 text-gray-700"
                                />
                            </div>

                            <div class="w-40">
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-1"
                                    >Preço Unit. (R$)</label
                                >
                                <input
                                    v-model="form.unit_price"
                                    type="number"
                                    step="0.01"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-dwl-teal focus:ring-dwl-teal px-4 py-2.5 text-gray-700"
                                />
                            </div>

                            <div>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex justify-center items-center px-6 py-2.5 bg-dwl-teal border border-transparent rounded-md font-semibold text-sm text-white hover:bg-dwl-darkblue transition-all disabled:opacity-50 shadow-sm h-[46px]"
                                >
                                    + Inserir
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="p-6">
                        <h3
                            class="font-serif font-bold text-lg text-dwl-darkblue mb-4"
                        >
                            Itens Inclusos nesta Negociação
                        </h3>

                        <div
                            v-if="deal.items && deal.items.length > 0"
                            class="overflow-x-auto"
                        >
                            <table
                                class="min-w-full divide-y divide-gray-200 text-sm"
                            >
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left font-semibold text-dwl-slate uppercase tracking-wider"
                                        >
                                            Descrição do Reagente
                                        </th>
                                        <th
                                            class="px-6 py-3 text-center font-semibold text-dwl-slate uppercase tracking-wider"
                                        >
                                            Quantidade
                                        </th>
                                        <th
                                            class="px-6 py-3 text-right font-semibold text-dwl-slate uppercase tracking-wider"
                                        >
                                            Valor Unitário
                                        </th>
                                        <th
                                            class="px-6 py-3 text-right font-semibold text-dwl-slate uppercase tracking-wider"
                                        >
                                            Subtotal
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-gray-200"
                                >
                                    <tr
                                        v-for="item in deal.items"
                                        :key="item.id"
                                        class="hover:bg-gray-50 transition"
                                    >
                                        <td
                                            class="px-6 py-4 font-medium text-dwl-darkblue"
                                        >
                                            {{
                                                item.product?.name ||
                                                "Produto Removido"
                                            }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-center text-gray-700 font-medium"
                                        >
                                            {{ item.quantity }} un
                                        </td>
                                        <td
                                            class="px-6 py-4 text-right text-gray-700"
                                        >
                                            {{ formatarMoeda(item.unit_price) }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-right font-bold text-dwl-teal"
                                        >
                                            {{
                                                formatarMoeda(
                                                    item.quantity *
                                                        item.unit_price,
                                                )
                                            }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div
                            v-else
                            class="text-center py-10 bg-gray-50 rounded-md border border-dashed border-gray-300"
                        >
                            <p class="text-gray-500 italic">
                                Nenhum reagente foi adicionado ao pacote desta
                                negociação ainda.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
