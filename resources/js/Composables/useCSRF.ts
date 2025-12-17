import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

export const useCSRF = () => {
    const page = usePage();
    const csrf_token = computed(() => page.props.auth.csrf_token);

    return {
        csrf_token,
    };
}