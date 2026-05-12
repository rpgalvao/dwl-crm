<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineProps({ products: Array });

const formatarMoeda = (valor) => {
    return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
    }).format(valor);
};
</script>

<template>
    <Head title="Catálogo de Reagentes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2
                    class="font-serif font-bold text-2xl text-dwl-darkblue leading-tight"
                >
                    Catálogo de Reagentes
                </h2>
                <Link
                    :href="route('products.create')"
                    class="bg-dwl-teal text-white px-5 py-2.5 rounded-md hover:bg-dwl-darkblue transition font-semibold text-sm shadow-sm"
                >
                    + Novo Reagente
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200"
                >
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-dwl-lightgreen/30">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left font-semibold text-dwl-slate uppercase tracking-wider"
                                >
                                    Código (SKU)
                                </th>
                                <th
                                    class="px-6 py-4 text-left font-semibold text-dwl-slate uppercase tracking-wider"
                                >
                                    Nome do Produto
                                </th>
                                <th
                                    class="px-6 py-4 text-right font-semibold text-dwl-slate uppercase tracking-wider"
                                >
                                    Preço Base
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="product in products"
                                :key="product.id"
                                class="hover:bg-gray-50 transition"
                            >
                                <td class="px-6 py-4 text-gray-500 font-medium">
                                    {{ product.sku || "N/A" }}
                                </td>
                                <td
                                    class="px-6 py-4 font-bold text-dwl-darkblue"
                                >
                                    {{ product.name }}
                                </td>
                                <td
                                    class="px-6 py-4 text-right font-bold text-dwl-teal"
                                >
                                    {{ formatarMoeda(product.price) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
