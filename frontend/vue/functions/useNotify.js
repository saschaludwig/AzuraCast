import {useTranslate} from "~/vendor/gettext";
import {h, render} from "vue";
import {default as BSToast} from 'bootstrap/js/src/toast';

import Toast from '~/components/Common/Toast.vue';
import {currentVueInstance} from "~/vendor/vueInstance";

export function createToast(props) {
    let slot;
    if (Array.isArray(props.message)) {
        slot = props.message
        delete props.message
    }

    const defaultSlot = () => {
        return slot
    };

    const vNode = h(Toast, props, defaultSlot);
    vNode.appContext = currentVueInstance._context;

    const newDiv = document.createElement('div');
    newDiv.style.display = "contents";
    document.querySelector('.toast-container').appendChild(newDiv);

    render(vNode, newDiv);

    return new BSToast(vNode.el);
}

/* Composition API BootstrapVue utilities */
export function useNotify() {
    const {$gettext} = useTranslate();

    const notify = (message = null, options = {}) => {
        if (document.hidden) {
            return;
        }

        const toast = createToast({
            ...options,
            message
        });
        toast.show();
    };

    const notifyError = (message = null, options = {}) => {
        if (message === null) {
            message = $gettext('An error occurred and your request could not be completed.');
        }

        const defaults = {
            variant: 'danger'
        };

        notify(message, {...defaults, ...options});

        return message;
    };

    const notifySuccess = (message = null, options = {}) => {
        if (message === null) {
            message = $gettext('Changes saved.');
        }

        const defaults = {
            variant: 'success'
        };

        notify(message, {...defaults, ...options});

        return message;
    };

    // let $loadingComponent;

    const showLoading = () => {
        // TODO Replace Oruga
    };

    const hideLoading = () => {
        // TODO Replace Oruga
    };

    let $isAxiosLoading = false;
    let $axiosLoadCount = 0;

    const setLoading = (isLoading) => {
        let prevIsLoading = $isAxiosLoading;
        if (isLoading) {
            $axiosLoadCount++;
            $isAxiosLoading = true;
        } else if ($axiosLoadCount > 0) {
            $axiosLoadCount--;
            $isAxiosLoading = ($axiosLoadCount > 0);
        }

        // Handle state changes
        if (!prevIsLoading && $isAxiosLoading) {
            showLoading();
        } else if (prevIsLoading && !$isAxiosLoading) {
            hideLoading();
        }
    };

    const wrapWithLoading = (promise) => {
        setLoading(true);

        promise.finally(() => {
            setLoading(false);
        });

        return promise;
    };

    return {
        notify,
        notifyError,
        notifySuccess,
        showLoading,
        hideLoading,
        setLoading,
        wrapWithLoading
    };
}
