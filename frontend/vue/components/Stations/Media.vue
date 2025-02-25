<template>
    <section
        class="card"
        role="region"
        aria-labelledby="hdr_music_files"
    >
        <div class="card-header text-bg-primary">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h2
                        id="hdr_music_files"
                        class="card-title"
                    >
                        {{ $gettext('Music Files') }}
                    </h2>
                </div>
                <div class="col-md-5 text-end">
                    <stations-common-quota
                        ref="$quota"
                        :quota-url="quotaUrl"
                    />
                </div>
            </div>
        </div>

        <info-card v-if="showSftp">
            <p class="mb-0">
                {{ $gettext('You can also upload files in bulk via SFTP.') }}
            </p>

            <template #action>
                <a
                    class="btn btn-sm btn-info"
                    target="_blank"
                    :href="sftpUrl"
                >
                    {{ $gettext('Manage SFTP Accounts') }}
                </a>
            </template>
        </info-card>

        <div class="card-body">
            <breadcrumb
                :current-directory="currentDirectory"
                @change-directory="changeDirectory"
            />

            <file-upload
                :upload-url="uploadUrl"
                :search-phrase="searchPhrase"
                :valid-mime-types="validMimeTypes"
                :current-directory="currentDirectory"
                @relist="onTriggerRelist"
            />

            <media-toolbar
                :batch-url="batchUrl"
                :selected-items="selectedItems"
                :current-directory="currentDirectory"
                :supports-immediate-queue="supportsImmediateQueue"
                :playlists="playlists"
                @add-playlist="onAddPlaylist"
                @relist="onTriggerRelist"
                @create-directory="createDirectory"
                @move-files="moveFiles"
            />
        </div>

        <data-table
            id="station_media"
            ref="$datatable"
            selectable
            paginated
            select-fields
            :fields="fields"
            :api-url="listUrl"
            :request-config="requestConfig"
            @row-selected="onRowSelected"
            @filtered="onFiltered"
        >
            <template #cell(path)="row">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 pe-2">
                        <template v-if="row.item.media.is_playable">
                            <play-button
                                :url="row.item.media.links.play"
                                class="btn-lg"
                            />
                        </template>
                        <template v-else>
                            <span
                                v-if="row.item.is_dir"
                                class="file-icon"
                            >
                                <icon icon="folder" />
                            </span>
                            <span
                                v-else-if="row.item.is_cover_art"
                                class="file-icon"
                            >
                                <icon icon="photo" />
                            </span>
                            <span
                                v-else
                                class="file-icon"
                            >
                                <icon icon="note" />
                            </span>
                        </template>
                    </div>

                    <div class="flex-fill">
                        <template v-if="row.item.is_dir">
                            <a
                                class="name"
                                href="#"
                                :title="row.item.name"
                                @click.prevent="changeDirectory(row.item.path)"
                            >
                                {{ row.item.path_short }}
                            </a>
                        </template>
                        <template v-else-if="row.item.media.is_playable">
                            <a
                                class="name"
                                :href="row.item.media.links.play"
                                target="_blank"
                                :title="row.item.name"
                            >
                                {{ row.item.text }}
                            </a>
                        </template>
                        <template v-else>
                            <a
                                class="name"
                                :href="row.item.links.download"
                                target="_blank"
                                :title="row.item.text"
                            >
                                {{ row.item.path_short }}
                            </a>
                        </template>
                        <br>
                        <small v-if="row.item.media.is_playable">{{ row.item.path_short }}</small>
                        <small v-else>{{ row.item.text }}</small>
                    </div>

                    <album-art
                        v-if="row.item.media.art"
                        :src="row.item.media.art"
                        class="flex-shrink-1 ps-2"
                        @click.prevent="showImage(row.item.media.art)"
                    />
                    <album-art
                        v-else-if="row.item.is_cover_art"
                        :src="row.item.links.download"
                        class="flex-shrink-1 ps-2"
                        @click.prevent="showImage(row.item.links.download)"
                    />
                </div>
            </template>
            <!-- eslint-disable-next-line -->
            <template #cell(media.length)="row">
                {{ row.item.media.length_text }}
            </template>
            <template #cell(size)="row">
                <template v-if="!row.item.size">
                    &nbsp;
                </template>
                <template v-else>
                    {{ formatFileSize(row.item.size) }}
                </template>
            </template>
            <template #cell(playlists)="row">
                <template v-if="row.item.playlists.length > 0">
                    <template
                        v-for="(playlist, index) in row.item.playlists"
                        :key="playlist.id"
                    >
                        <a
                            class="btn-search"
                            href="#"
                            :title="$gettext('View tracks in playlist')"
                            @click.prevent="filter('playlist:'+playlist.short_name)"
                        >{{ playlist.name }}</a>
                        <span v-if="index+1 < row.item.playlists.length">, </span>
                    </template>
                </template>
                <template v-else>
                    &nbsp;
                </template>
            </template>
            <template #cell(commands)="row">
                <template v-if="row.item.media.links.edit">
                    <button
                        type="button"
                        class="btn btn-sm btn-primary"
                        @click="edit(row.item.media.links.edit, row.item.media.links.art, row.item.media.links.play, row.item.media.links.waveform)"
                    >
                        {{ $gettext('Edit') }}
                    </button>
                </template>
                <template v-else>
                    <button
                        type="button"
                        class="btn btn-sm btn-primary"
                        @click="rename(row.item.path)"
                    >
                        {{ $gettext('Rename') }}
                    </button>
                </template>
            </template>
        </data-table>
    </section>

    <new-directory-modal
        ref="$newDirectoryModal"
        :current-directory="currentDirectory"
        :mkdir-url="mkdirUrl"
        @relist="onTriggerRelist"
    />

    <move-files-modal
        ref="$moveFilesModal"
        :selected-items="selectedItems"
        :current-directory="currentDirectory"
        :batch-url="batchUrl"
        :list-directories-url="listDirectoriesUrl"
        @relist="onTriggerRelist"
    />

    <rename-modal
        ref="$renameModal"
        :rename-url="renameUrl"
        @relist="onTriggerRelist"
    />

    <edit-modal
        ref="$editModal"
        :custom-fields="customFields"
        :playlists="playlists"
        @relist="onTriggerRelist"
    />

    <lightbox ref="$lightbox" />
</template>

<script setup>
import DataTable from '~/components/Common/DataTable';
import MediaToolbar from './Media/MediaToolbar';
import Breadcrumb from './Media/Breadcrumb';
import FileUpload from './Media/FileUpload';
import NewDirectoryModal from './Media/NewDirectoryModal';
import MoveFilesModal from './Media/MoveFilesModal';
import RenameModal from './Media/RenameModal';
import EditModal from './Media/EditModal';
import StationsCommonQuota from "~/components/Stations/Common/Quota";
import Icon from '~/components/Common/Icon';
import AlbumArt from '~/components/Common/AlbumArt';
import PlayButton from "~/components/Common/PlayButton";
import {useTranslate} from "~/vendor/gettext";
import {computed, nextTick, onMounted, ref} from "vue";
import {forEach, map, partition} from "lodash";
import {useAzuraCast, useAzuraCastStation} from "~/vendor/azuracast";
import {DateTime} from "luxon";
import {useEventListener} from "@vueuse/core";
import formatFileSize from "../../functions/formatFileSize";
import InfoCard from "~/components/Common/InfoCard.vue";
import Lightbox from "~/components/Common/Lightbox.vue";

const props = defineProps({
    listUrl: {
        type: String,
        required: true
    },
    batchUrl: {
        type: String,
        required: true
    },
    uploadUrl: {
        type: String,
        required: true
    },
    listDirectoriesUrl: {
        type: String,
        required: true
    },
    mkdirUrl: {
        type: String,
        required: true
    },
    renameUrl: {
        type: String,
        required: true
    },
    quotaUrl: {
        type: String,
        required: true
    },
    initialPlaylists: {
        type: Array,
        required: false,
        default: () => []
    },
    customFields: {
        type: Array,
        required: false,
        default: () => []
    },
    validMimeTypes: {
        type: Array,
        required: false,
        default: () => []
    },
    showSftp: {
        type: Boolean,
        default: true
    },
    sftpUrl: {
        type: String,
        required: true
    },
    supportsImmediateQueue: {
        type: Boolean,
        required: true
    }
});

const {$gettext} = useTranslate();
const {timeConfig} = useAzuraCast();
const {timezone} = useAzuraCastStation();

const fields = computed(() => {
    let fields = [
        {key: 'path', isRowHeader: true, label: $gettext('Name'), sortable: true},
        {key: 'media.title', label: $gettext('Title'), sortable: true, selectable: true, visible: false},
        {
            key: 'media.artist',
            label: $gettext('Artist'),
            sortable: true,
            selectable: true,
            visible: false
        },
        {key: 'media.album', label: $gettext('Album'), sortable: true, selectable: true, visible: false},
        {key: 'media.genre', label: $gettext('Genre'), sortable: true, selectable: true, visible: false},
        {key: 'media.isrc', label: $gettext('ISRC'), sortable: true, selectable: true, visible: false},
        {key: 'media.length', label: $gettext('Length'), sortable: true, selectable: true, visible: true}
    ];

    forEach({...props.customFields}, (field) => {
        fields.push({
            key: 'media.custom_fields[' + field.id + ']',
            label: field.name,
            sortable: true,
            selectable: true,
            visible: false
        });
    });

    fields.push(
        {key: 'size', label: $gettext('Size'), sortable: true, selectable: true, visible: true},
        {
            key: 'timestamp',
            label: $gettext('Modified'),
            sortable: true,
            formatter: (value) => {
                if (!value) {
                    return '';
                }

                return DateTime.fromSeconds(value).setZone(timezone).toLocaleString(
                    {...DateTime.DATETIME_MED, ...timeConfig}
                );
            },
            selectable: true,
            visible: true
        },
        {
            key: 'playlists',
            label: $gettext('Playlists'),
            sortable: false,
            selectable: true,
            visible: true
        },
        {key: 'commands', label: $gettext('Actions'), sortable: false, class: 'shrink'}
    );

    return fields;
});

const playlists = ref(props.initialPlaylists);
const selectedItems = ref({
    all: [],
    files: [],
    directories: []
});
const currentDirectory = ref('');
const searchPhrase = ref('');

const onRowSelected = (items) => {
    let splitItems = partition(items, 'is_dir');

    selectedItems.value = {
        all: items,
        files: map(splitItems[1], 'path'),
        directories: map(splitItems[0], 'path')
    };
};

const $datatable = ref(); // Template Ref

const onTriggerNavigate = () => {
    $datatable.value?.navigate();
};

const filter = (newFilter) => {
    $datatable.value.setFilter(newFilter);
};

const $quota = ref(); // Template Ref

const onTriggerRelist = () => {
    $quota.value?.update();
    $datatable.value?.relist();
};

const onAddPlaylist = (row) => {
    playlists.value.push(row);
};

const isFilterString = (str) =>
    (str.substring(0, 9) === 'playlist:' || str.substring(0, 8) === 'special:');

const onHashChange = () => {
    // Handle links from the sidebar for special functions.
    let urlHash = decodeURIComponent(window.location.hash.substring(1).replace(/\+/g, '%20'));

    if ('' !== urlHash && isFilterString(urlHash)) {
        window.location.hash = '';
        filter(urlHash);
    }
};

const changeDirectory = (newDir) => {
    window.location.hash = newDir;
    currentDirectory.value = newDir;
    onTriggerNavigate();
};

const onFiltered = (newFilter) => {
    searchPhrase.value = newFilter;
};

const $renameModal = ref(); // Template Ref

const rename = (path) => {
    $renameModal.value?.open(path);
};

const $editModal = ref(); // Template Ref

const edit = (recordUrl, albumArtUrl, audioUrl, waveformUrl) => {
    $editModal.value?.open(recordUrl, albumArtUrl, audioUrl, waveformUrl);
};

const $newDirectoryModal = ref(); // Template Ref

const createDirectory = () => {
    $newDirectoryModal.value.open();
}

const $moveFilesModal = ref(); // Template Ref

const moveFiles = () => {
    $moveFilesModal.value.open();
}

const $lightbox = ref(); // Template Ref

const showImage = (url) => {
    $lightbox.value.show(url);
};

const requestConfig = (config) => {
    config.params.currentDirectory = currentDirectory.value;
    return config;
};

onMounted(() => {
    // Load directory from URL hash, if applicable.
    let urlHash = decodeURIComponent(window.location.hash.substring(1).replace(/\+/g, '%20'));

    if ('' !== urlHash) {
        if (isFilterString(urlHash)) {
            nextTick(() => {
                onHashChange();
            });
        } else {
            currentDirectory.value = urlHash;
            onTriggerNavigate();
        }
    }

    useEventListener(window, 'hashchange', onHashChange);
});
</script>
