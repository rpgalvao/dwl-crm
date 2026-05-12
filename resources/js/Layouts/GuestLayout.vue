<script setup>
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-dwl-lightgreen/20"
    >
        <div>
            <Link href="/">
                <img
                    src="/images/logo_dwl.png"
                    alt="Logo DWL"
                    class="w-auto h-24 drop-shadow-sm"
                />
            </Link>
        </div>

        <div
            class="w-full sm:max-w-md mt-8 px-8 py-10 bg-white shadow-lg overflow-hidden sm:rounded-xl border-t-4 border-dwl-teal"
        >
            <slot />
        </div>
    </div>
</template>
