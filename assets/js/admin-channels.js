(function () {
    'use strict';

    const submitToggleForm = function (event) {
        const input = event.target;

        if (!(input instanceof HTMLInputElement)) {
            return;
        }

        const form = input.closest('form');

        if (form) {
            form.submit();
        }
    };

    const init = function () {
        const toggles = document.querySelectorAll('.blitz-dock-toggle__input');

        toggles.forEach(function (toggle) {
            toggle.addEventListener('change', submitToggleForm);
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();