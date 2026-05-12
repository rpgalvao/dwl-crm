<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    contact: Object,
});

const form = useForm({
    name: props.contact.name,
    email: props.contact.email,
    phone: props.contact.phone,
});

const mascaraTelefone = (event) => {
    let valor = event.target.value.replace(/\D/g, "");
    if (valor.length > 11) valor = valor.slice(0, 11);

    if (valor.length > 10) {
        valor = valor.replace(/^(\d{2})(\d{5})(\d{4}).*/, "($1) $2-$3");
    } else if (valor.length > 6) {
        valor = valor.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, "($1) $2-$3");
    } else if (valor.length > 2) {
        valor = valor.replace(/^(\d{2})(\d{0,5})/, "($1) $2");
    } else if (valor.length > 0) {
        valor = valor.replace(/^(\d{0,2})/, "($1");
    }

    form.phone = valor;
};

const submit = () => {
    form.put(route("contacts.update", props.contact.id));
};
</script>

<template>
    <Head title="Editar Cliente" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-serif font-bold text-2xl text-dwl-darkblue leading-tight"
            >
                Editar Cliente
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
                                >Nome do Laboratório / Contato *</label
                            >
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-dwl-teal focus:ring-dwl-teal px-4 py-2.5"
                            />
                            <div
                                v-if="form.errors.name"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-1"
                                    >E-mail Corporativo</label
                                >
                                <input
                                    v-model="form.email"
                                    type="email"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-dwl-teal focus:ring-dwl-teal px-4 py-2.5"
                                />
                                <div
                                    v-if="form.errors.email"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.email }}
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-1"
                                    >Telefone / WhatsApp</label
                                >
                                <input
                                    v-model="form.phone"
                                    @input="mascaraTelefone"
                                    maxlength="15"
                                    type="text"
                                    placeholder="(00) 00000-0000"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-dwl-teal focus:ring-dwl-teal px-4 py-2.5"
                                />
                                <div
                                    v-if="form.errors.phone"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.phone }}
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex justify-end space-x-3 pt-6 mt-6 border-t border-gray-100"
                        >
                            <Link
                                :href="route('contacts.index')"
                                class="px-4 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900"
                                >Cancelar</Link
                            >
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-dwl-teal text-white px-6 py-2.5 rounded-md hover:bg-dwl-darkblue font-semibold transition-colors disabled:opacity-50"
                            >
                                Atualizar Cliente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
