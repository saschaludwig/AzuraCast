<template>
    <form-fieldset>
        <template #label>
            {{ $gettext('No AutoDJ Enabled') }}
        </template>

        <p class="card-text">
            {{ $gettext('This feature requires the AutoDJ feature to be enabled.') }}
        </p>
    </form-fieldset>
</template>

<script setup>
import FormFieldset from "~/components/Form/FormFieldset";</script>
