<template>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header text-bg-primary">
                    <div class="d-lg-flex align-items-center">
                        <div class="flex-fill my-0">
                            <h2 class="card-title">
                                {{ $gettext('Listeners') }}
                            </h2>
                        </div>
                        <div class="flex-shrink buttons mt-2 mt-lg-0">
                            <a
                                id="btn-export"
                                class="btn btn-dark"
                                :href="exportUrl"
                                target="_blank"
                            >
                                <icon icon="file_download" />
                                <span>
                                    {{ $gettext('Download CSV') }}
                                </span>
                            </a>
                        </div>
                        <div
                            v-if="!isLive"
                            class="flex-shrink buttons ms-lg-2 mt-2 mt-lg-0"
                        >
                            <date-range-dropdown
                                v-model="dateRange"
                                time-picker
                                :min-date="minDate"
                                :max-date="maxDate"
                                :tz="timezone"
                                @update="updateListeners"
                            />
                        </div>
                    </div>
                </div>

                <div class="card-body pb-0">
                    <nav
                        class="nav nav-tabs"
                        role="tablist"
                    >
                        <div
                            class="nav-item"
                            role="presentation"
                        >
                            <button
                                class="nav-link"
                                :class="(isLive) ? 'active' : ''"
                                type="button"
                                role="tab"
                                @click="setIsLive(true)"
                            >
                                {{ $gettext('Live Listeners') }}
                            </button>
                        </div>
                        <div
                            class="nav-item"
                            role="presentation"
                        >
                            <button
                                class="nav-link"
                                :class="(!isLive) ? 'active' : ''"
                                type="button"
                                role="tab"
                                @click="setIsLive(false)"
                            >
                                {{ $gettext('Listener History') }}
                            </button>
                        </div>
                    </nav>
                </div>

                <div id="map">
                    <StationReportsListenersMap
                        :listeners="listeners"
                    />
                </div>
                <div>
                    <div class="card-body row">
                        <div class="col-md-4">
                            <h5>
                                {{ $gettext('Unique Listeners') }}
                                <br>
                                <small>
                                    {{ $gettext('for selected period') }}
                                </small>
                            </h5>
                            <h3>{{ listeners.length }}</h3>
                        </div>
                        <div class="col-md-4">
                            <h5>
                                {{ $gettext('Total Listener Hours') }}
                                <br>
                                <small>
                                    {{ $gettext('for selected period') }}
                                </small>
                            </h5>
                            <h3>{{ totalListenerHours }}</h3>
                        </div>
                    </div>

                    <data-table
                        id="station_playlists"
                        ref="datatable"
                        paginated
                        handle-client-side
                        :fields="fields"
                        :responsive="false"
                        :items="listeners"
                    >
                        <template #cell(time)="row">
                            {{ formatTime(row.item.connected_time) }}
                        </template>
                        <template #cell(time_sec)="row">
                            {{ row.item.connected_time }}
                        </template>
                        <template #cell(user_agent)="row">
                            <div>
                                <span v-if="row.item.is_mobile">
                                    <icon icon="smartphone" />
                                    <span class="visually-hidden">
                                        {{ $gettext('Mobile Device') }}
                                    </span>
                                </span>
                                <span v-else>
                                    <icon icon="desktop_windows" />
                                    <span class="visually-hidden">
                                        {{ $gettext('Desktop Device') }}
                                    </span>
                                </span>

                                {{ row.item.user_agent }}
                            </div>
                            <div v-if="row.item.device.client">
                                <small>{{ row.item.device.client }}</small>
                            </div>
                        </template>
                        <template #cell(stream)="row">
                            <span v-if="row.item.mount_name === ''">
                                {{ $gettext('Unknown') }}
                            </span>
                            <span v-else>
                                {{ row.item.mount_name }}<br>
                                <small v-if="row.item.mount_is_local">
                                    {{ $gettext('Local') }}
                                </small>
                                <small v-else>
                                    {{ $gettext('Remote') }}
                                </small>
                            </span>
                        </template>
                        <template #cell(location)="row">
                            <span v-if="row.item.location.description">
                                {{ row.item.location.description }}
                            </span>
                            <span v-else>
                                {{ $gettext('Unknown') }}
                            </span>
                        </template>
                    </data-table>
                </div>
                <div
                    class="card-body card-padding-sm text-muted"
                    v-html="attribution"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import StationReportsListenersMap from "./Listeners/Map";
import Icon from "~/components/Common/Icon";
import formatTime from "~/functions/formatTime";
import DataTable from "~/components/Common/DataTable";
import DateRangeDropdown from "~/components/Common/DateRangeDropdown";
import {DateTime} from 'luxon';
import {computed, onMounted, ref, shallowRef} from "vue";
import {useTranslate} from "~/vendor/gettext";
import {useNotify} from "~/functions/useNotify";
import {useAxios} from "~/vendor/axios";
import {useAzuraCastStation} from "~/vendor/azuracast";

const props = defineProps({
  apiUrl: {
    type: String,
    required: true
  },
    attribution: {
        type: String,
        required: true
    }
});

const isLive = ref(true);
const listeners = shallowRef([]);

const {timezone} = useAzuraCastStation();

const nowTz = DateTime.now().setZone(timezone);

const minDate = nowTz.minus({years: 5}).toJSDate();
const maxDate = nowTz.plus({days: 5}).toJSDate();

const dateRange = ref({
    startDate: nowTz.minus({days: 1}).toJSDate(),
    endDate: nowTz.toJSDate()
});

const {$gettext} = useTranslate();

const fields = [
  {key: 'ip', label: $gettext('IP'), sortable: false},
  {key: 'time', label: $gettext('Time'), sortable: false},
  {key: 'time_sec', label: $gettext('Time (sec)'), sortable: false},
  {key: 'user_agent', isRowHeader: true, label: $gettext('User Agent'), sortable: false},
  {key: 'stream', label: $gettext('Stream'), sortable: false},
  {key: 'location', label: $gettext('Location'), sortable: false}
];

const exportUrl = computed(() => {
  let exportUrl = new URL(props.apiUrl, document.location);
  let exportUrlParams = exportUrl.searchParams;
  exportUrlParams.set('format', 'csv');

  if (!isLive.value) {
    exportUrlParams.set('start', DateTime.fromJSDate(dateRange.value.startDate).toISO());
    exportUrlParams.set('end', DateTime.fromJSDate(dateRange.value.endDate).toISO());
  }

  return exportUrl.toString();
});

const totalListenerHours = computed(() => {
  let tlh_seconds = 0;
  listeners.value.forEach(function (listener) {
    tlh_seconds += listener.connected_time;
  });

  let tlh_hours = tlh_seconds / 3600;
  return Math.round((tlh_hours + 0.00001) * 100) / 100;
});

const {wrapWithLoading} = useNotify();
const {axios} = useAxios();

const updateListeners = () => {
  let params = {};
  if (!isLive.value) {
    params.start = DateTime.fromJSDate(dateRange.value.startDate).toISO();
    params.end = DateTime.fromJSDate(dateRange.value.endDate).toISO();
  }

  wrapWithLoading(
      axios.get(props.apiUrl, {params: params})
  ).then((resp) => {
    listeners.value = resp.data;

    if (isLive.value) {
      setTimeout(updateListeners, (!document.hidden) ? 15000 : 30000);
    }
  }).catch((error) => {
    if (isLive.value && (!error.response || error.response.data.code !== 403)) {
      setTimeout(updateListeners, (!document.hidden) ? 30000 : 120000);
    }
  });
};

onMounted(updateListeners);

const setIsLive = (newValue) => {
  isLive.value = newValue;
  updateListeners();
};
</script>
