import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

export const useUser = () => {
    const page = usePage();
    const user = computed(() => page.props.auth.user);

    const mfa_methods = computed(() => {
        const methods = ['mail'];

        if (user.value?.has_webauthn_enabled) {
            methods.push('webauthn');
        }

        return methods;
    });
    
    return {
        user,
        mfa_methods,
    };
}