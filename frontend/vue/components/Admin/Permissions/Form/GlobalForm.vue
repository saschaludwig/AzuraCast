<template>
    <o-tab-item
        :label="$gettext('Global Permissions')"
        active
    >
        <div class="row g-3">
            <form-group-field
                id="edit_form_name"
                class="col-md-12"
                :field="form.name"
                :label="$gettext('Role Name')"
            />

            <form-group-multi-check
                id="edit_form_global_permissions"
                class="col-md-12"
                :field="form.permissions.global"
                :options="globalPermissionOptions"
                stacked
                :label="$gettext('Global Permissions')"
                :description="$gettext('Users with this role will have these permissions across the entire installation.')"
            />
        </div>
    </o-tab-item>
</template>

<script setup>
import FormGroupField from "~/components/Form/FormGroupField.vue";
import {map} from 'lodash';
import {computed} from "vue";
import FormGroupMultiCheck from "~/components/Form/FormGroupMultiCheck.vue";

const props = defineProps({
    form: {
        type: Object,
        required: true
    },
    globalPermissions: {
        type: Object,
        required: true
    }
});

const globalPermissionOptions = computed(() => {
    return map(props.globalPermissions, (permissionName, permissionKey) => {
        return {
            text: permissionName,
            value: permissionKey
        };
    });
});
</script>
