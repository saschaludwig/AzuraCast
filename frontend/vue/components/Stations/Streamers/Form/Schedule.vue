<template>
    <o-tab-item :label="$gettext('Schedule')">
        <form-markup
            v-if="scheduleItems.length === 0"
            id="no_scheduled_entries"
        >
            <template #label>
                {{ $gettext('Not Scheduled') }}
            </template>
            <p>
                {{ $gettext('This streamer is not scheduled to play at any times.') }}
            </p>
        </form-markup>

        <streamers-form-schedule-row
            v-for="(row, index) in scheduleItems"
            :key="index"
            v-model:row="scheduleItems[index]"
            :index="index"
            @remove="remove(index)"
        />

        <div class="buttons mt-3">
            <button
                type="button"
                class="btn btn-sm btn-primary"
                @click="add"
            >
                <icon icon="add" />
                <span>
                    {{ $gettext('Add Schedule Item') }}
                </span>
            </button>
        </div>
    </o-tab-item>
</template>

<script setup>
import Icon from '~/components/Common/Icon';
import StreamersFormScheduleRow from "~/components/Stations/Streamers/Form/ScheduleRow.vue";
import {useVModel} from "@vueuse/core";
import FormMarkup from "~/components/Form/FormMarkup.vue";

const props = defineProps({
    scheduleItems: {
        type: Array,
        default: () => {
            return [];
        }
    }
});

const emit = defineEmits(['update:scheduleItems']);

const scheduleItems = useVModel(props, 'scheduleItems', emit);

const add = () => {
    scheduleItems.value.push({
        start_time: null,
        end_time: null,
        start_date: null,
        end_date: null,
        days: []
    });
};

const remove = (index) => {
    scheduleItems.value.splice(index, 1);
};
</script>
