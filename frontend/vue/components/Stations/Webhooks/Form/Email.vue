<template>
    <o-tab-item
        :label="title"
        :item-header-class="tabClass"
    >
        <div class="row g-3 mb-3">
            <form-group-field
                id="form_config_to"
                class="col-md-12"
                :field="v$.config.to"
                :label="$gettext('Message Recipient(s)')"
                :description="$gettext('E-mail addresses can be separated by commas.')"
            />
        </div>

        <common-formatting-info />

        <div class="row g-3">
            <form-group-field
                id="form_config_subject"
                class="col-md-12"
                :field="v$.config.subject"
                :label="$gettext('Message Subject')"
            />

            <form-group-field
                id="form_config_message"
                class="col-md-12"
                :field="v$.config.message"
                :label="$gettext('Message Body')"
            />
        </div>
    </o-tab-item>
</template>

<script setup>
import FormGroupField from "~/components/Form/FormGroupField";
import CommonFormattingInfo from "./Common/FormattingInfo";
import {useVModel} from "@vueuse/core";
import {useVuelidateOnFormTab} from "~/functions/useVuelidateOnFormTab";
import {required} from "@vuelidate/validators";

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    form: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['update:form']);
const form = useVModel(props, 'form', emit);

const {v$, tabClass} = useVuelidateOnFormTab(
    {
        config: {
            to: {required},
            subject: {required},
            message: {required}
        }
    },
    form,
    {
        config: {
            to: '',
            subject: '',
            message: ''
        }
    }
);
</script>
