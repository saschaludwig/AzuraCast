<template>
    <form-group :id="id">
        <template #label="slotProps">
            <slot
                name="label"
                v-bind="slotProps"
            >
                {{ label }}
            </slot>
        </template>

        <template #default="slotProps">
            <div :id="id">
                <slot
                    v-bind="slotProps"
                    :id="id"
                    name="default"
                />
            </div>
        </template>

        <template #description="slotProps">
            <slot
                v-bind="slotProps"
                name="description"
            >
                {{ description }}
            </slot>
        </template>
    </form-group>
</template>

<script setup>
import FormGroup from "~/components/Form/FormGroup.vue";

const props = defineProps({
    id: {
        type: String,
        required: true
    },
    label: {
        type: String,
        default: null
    },
    description: {
        type: String,
        default: null
    },
});
</script>
