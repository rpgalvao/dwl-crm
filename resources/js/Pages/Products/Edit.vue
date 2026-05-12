<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    product: Object,
});

const form = useForm({
    name: props.product.name,
    sku: props.product.sku,
    price: props.product.price,
});

const valorFormatado = computed({
    get() {
        if (!form.price) return "";
        let val = form.price.toString().replace(/\D/g, "");
        val = (val / 100).toFixed(2) + "";
        return (
            "R$ " +
            val.replace(".", ",").replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")
        );
    },
    set(novoValor) {
        let valLimpo = novoValor.replace(/\D/g, "");
        form.price = (valLimpo / 100).toFixed(2);
    },
});

const submit = () => {
    form.put(route("products.update", props.product.id));
};
</script>

<template>
    <Head title="Editar Reagente" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-serif font-bold text-2xl text-dwl-darkblue leading-tight"
            >
                Editar Reagente
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 border border-gray-200"
                >
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-1"
                                >Nome do Reagente</label
                            >
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-dwl-teal focus:ring-dwl-teal px-4 py-2.5"
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-1"
                                    >Código (SKU)</label
                                >
                                <input
                                    v-model="form.sku"
                                    type="text"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-dwl-teal focus:ring-dwl-teal px-4 py-2.5"
                                />
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-1"
                                    >Preço Base</label
                                >
                                <input
                                    v-model="valorFormatado"
                                    type="text"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-dwl-teal focus:ring-dwl-teal px-4 py-2.5 font-medium"
                                />
                            </div>
                        </div>

                        <div
                            class="flex justify-end space-x-3 pt-6 mt-6 border-t"
                        >
                            <Link
                                :href="route('products.index')"
                                class="px-4 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900"
                                >Cancelar</Link
                            >
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-dwl-teal text-white px-6 py-2.5 rounded-md hover:bg-dwl-darkblue font-semibold transition-colors"
                            >
                                Atualizar Reagente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
