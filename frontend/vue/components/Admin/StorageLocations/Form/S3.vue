<template>
    <o-tab-item
        :label="$gettext('Remote: S3 Compatible')"
        :item-header-class="tabClass"
    >
        <div class="row g-3">
            <form-group-field
                id="form_edit_s3CredentialKey"
                class="col-md-6"
                :field="v$.s3CredentialKey"
                :label="$gettext('Access Key ID')"
            />

            <form-group-field
                id="form_edit_s3CredentialSecret"
                class="col-md-6"
                :field="v$.s3CredentialSecret"
                :label="$gettext('Secret Key')"
            />

            <form-group-field
                id="form_edit_s3Endpoint"
                class="col-md-6"
                :field="v$.s3Endpoint"
                :label="$gettext('Endpoint')"
            />

            <form-group-field
                id="form_edit_s3Bucket"
                class="col-md-6"
                :field="v$.s3Bucket"
                :label="$gettext('Bucket Name')"
            />

            <form-group-field
                id="form_edit_s3Region"
                class="col-md-6"
                :field="v$.s3Region"
                :label="$gettext('Region')"
            />

            <form-group-field
                id="form_edit_s3Version"
                class="col-md-6"
                :field="v$.s3Version"
                :label="$gettext('API Version')"
            />
        </div>
    </o-tab-item>
</template>

<script setup>
import FormGroupField from "~/components/Form/FormGroupField.vue";
import {useVModel} from "@vueuse/core";
import {useVuelidateOnFormTab} from "~/functions/useVuelidateOnFormTab";
import {required} from "@vuelidate/validators";

const props = defineProps({
    form: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['update:form']);
const form = useVModel(props, 'form', emit);

const {v$, tabClass} = useVuelidateOnFormTab(
    {
        s3CredentialKey: {required},
        s3CredentialSecret: {required},
        s3Region: {required},
        s3Version: {required},
        s3Bucket: {required},
        s3Endpoint: {required}
    },
    form
);
</script>
