<template>
    <card-page header-id="hdr_public_pages">
        <template #header="{id}">
            <h3
                :id="id"
                class="card-title"
            >
                {{ $gettext('Public Pages') }}
                <enabled-badge :enabled="enablePublicPage" />
            </h3>
        </template>

        <template v-if="enablePublicPage">
            <table class="table table-striped table-responsive-md mb-0">
                <colgroup>
                    <col style="width: 30%;">
                    <col style="width: 70%;">
                </colgroup>
                <tbody>
                    <tr>
                        <td>{{ $gettext('Public Page') }}</td>
                        <td>
                            <a
                                :href="publicPageUri"
                                target="_blank"
                            >{{ publicPageUri }}</a>
                        </td>
                    </tr>
                    <tr v-if="stationSupportsStreamers && enableStreamers">
                        <td>{{ $gettext('Web DJ') }}</td>
                        <td>
                            <a
                                :href="publicWebDjUri"
                                target="_blank"
                            >{{ publicWebDjUri }}</a>
                        </td>
                    </tr>
                    <tr v-if="enableOnDemand">
                        <td>{{ $gettext('On-Demand Media') }}</td>
                        <td>
                            <a
                                :href="publicOnDemandUri"
                                target="_blank"
                            >{{ publicOnDemandUri }}</a>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ $gettext('Podcasts') }}</td>
                        <td>
                            <a
                                :href="publicPodcastsUri"
                                target="_blank"
                            >{{ publicPodcastsUri }}</a>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ $gettext('Schedule') }}</td>
                        <td>
                            <a
                                :href="publicScheduleUri"
                                target="_blank"
                            >{{ publicScheduleUri }}</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </template>

        <template #footer_actions>
            <template v-if="enablePublicPage">
                <a
                    class="btn btn-link text-secondary"
                    @click.prevent="doOpenEmbed"
                >
                    <icon icon="code" />
                    <span>
                        {{ $gettext('Embed Widgets') }}
                    </span>
                </a>
                <a
                    v-if="userCanManageProfile"
                    class="btn btn-link text-secondary"
                    :href="brandingUri"
                >
                    <icon icon="design_services" />
                    <span>
                        {{ $gettext('Edit Branding') }}
                    </span>
                </a>
                <a
                    v-if="userCanManageProfile"
                    v-confirm-link="$gettext('Disable public pages?')"
                    class="btn btn-link text-danger"
                    :href="togglePublicPageUri"
                >
                    <icon icon="close" />
                    <span>
                        {{ $gettext('Disable') }}
                    </span>
                </a>
            </template>
            <template v-else>
                <a
                    v-if="userCanManageProfile"
                    v-confirm-link="$gettext('Enable public pages?')"
                    class="btn btn-link text-success"
                    :href="togglePublicPageUri"
                >
                    <icon icon="check" />
                    <span>
                        {{ $gettext('Enable') }}
                    </span>
                </a>
            </template>
        </template>
    </card-page>

    <embed-modal
        v-bind="pickProps($props, embedModalProps)"
        ref="$embedModal"
    />
</template>

<script setup>
import Icon from '~/components/Common/Icon';
import EnabledBadge from "~/components/Common/Badges/EnabledBadge.vue";
import {ref} from "vue";
import EmbedModal from "~/components/Stations/Profile/EmbedModal.vue";
import publicPagesPanelProps from "~/components/Stations/Profile/publicPagesPanelProps";
import embedModalProps from "~/components/Stations/Profile/embedModalProps";
import {pickProps} from "~/functions/pickProps";
import CardPage from "~/components/Common/CardPage.vue";

const props = defineProps({
    ...publicPagesPanelProps,
    ...embedModalProps
});

const $embedModal = ref(); // Template Ref

const doOpenEmbed = () => {
    $embedModal.value.open();
};
</script>
