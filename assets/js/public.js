( function () {
    'use strict';

    if ( typeof window === 'undefined' || typeof document === 'undefined' ) {
        return;
    }

    /**
     * Toggle `hidden` and `aria-hidden` together.
     *
     * @param {HTMLElement|null} element Element to toggle.
     * @param {boolean} shouldHide       Whether the element should be hidden.
     *
     * @return {void}
     */
    function toggleHidden( element, shouldHide ) {
        if ( ! element ) {
            return;
        }

        if ( shouldHide ) {
            element.hidden = true;
            if ( ! element.hasAttribute( 'hidden' ) ) {
                element.setAttribute( 'hidden', '' );
            }
            element.setAttribute( 'aria-hidden', 'true' );
            return;
        }

        element.hidden = false;
        element.removeAttribute( 'hidden' );
        element.setAttribute( 'aria-hidden', 'false' );
    }

    /**
     * Determine whether an element can receive focus.
     *
     * @param {Element|null} el Element to inspect.
     *
     * @return {boolean}
     */
    function isFocusable( el ) {
        if ( ! el || typeof el.hasAttribute !== 'function' ) {
            return false;
        }

        if ( el.hasAttribute( 'disabled' ) ) {
            return false;
        }

        if ( el.hasAttribute( 'hidden' ) || el.getAttribute( 'aria-hidden' ) === 'true' ) {
            return false;
        }

        return true;
    }

    /**
     * Focus an element safely without breaking older browsers.
     *
     * @param {Element|null} el Element to focus.
     *
     * @return {void}
     */
    function focusSafely( el ) {
        if ( ! el || typeof el.focus !== 'function' ) {
            return;
        }

        try {
            el.focus( { preventScroll: true } );
        } catch ( _error ) {
            el.focus();
        }
    }

    /**
     * Collect focusable nodes inside a context.
     *
     * @param {HTMLElement|null} context Context to search in.
     *
     * @return {Array<Element>}
     */
    function getFocusableWithin( context ) {
        if ( ! context ) {
            return [];
        }

        var selector = [
            'a[href]',
            'area[href]',
            'button:not([disabled])',
            'input:not([disabled])',
            'select:not([disabled])',
            'textarea:not([disabled])',
            '[tabindex]:not([tabindex="-1"])',
        ].join( ',' );

        var nodes = context.querySelectorAll( selector );

        return Array.prototype.filter.call( nodes, function ( node ) {
            if ( ! isFocusable( node ) ) {
                return false;
            }

            var current = node;

            while ( current && current !== context ) {
                if ( current.hasAttribute( 'hidden' ) || current.getAttribute( 'aria-hidden' ) === 'true' ) {
                    return false;
                }

                current = current.parentElement;
            }

            return true;
        } );
    }

    /**
     * Swap missing channel icons with the default asset.
     *
     * @return {void}
     */
           function initChannelIconFallback() {
        var dockRoot = document.querySelector( '.blitz-dock' );

        if ( ! dockRoot ) {
            return;
        }

        if ( dockRoot.dataset && dockRoot.dataset.bdIconFallback === '1' ) {
            return;
        }

        if ( ! dockRoot.dataset && dockRoot.getAttribute( 'data-bd-icon-fallback' ) === '1' ) {
            return;
        }

        if ( dockRoot.dataset ) {
            dockRoot.dataset.bdIconFallback = '1';
        } else {
            dockRoot.setAttribute( 'data-bd-icon-fallback', '1' );
        }

        dockRoot.addEventListener( 'error', function onChannelIconError( event ) {
            var img = event && event.target ? event.target : null;

            if ( ! img || img.tagName !== 'IMG' ) {
                return;
            }

            if ( ! img.classList || ! img.classList.contains( 'blitz-dock-channels__icon' ) ) {
                return;
            }

            if ( img.dataset && img.dataset.fallbackApplied === '1' ) {
                return;
            }

            if ( ( ! img.dataset || typeof img.dataset.fallbackApplied === 'undefined' ) && img.getAttribute( 'data-fallback-applied' ) === '1' ) {
                return;
            }

            try {
                var u = new URL( img.src, window.location.href );

                if ( /\/default\.png$/i.test( u.pathname ) ) {
                    return;
                }

                u.pathname = u.pathname.replace( /\/[^/]+$/, '/default.png' );

                if ( img.dataset ) {
                    img.dataset.fallbackApplied = '1';
                }

                img.setAttribute( 'data-fallback-applied', '1' );

                img.src = u.toString();
            } catch ( _error ) {
                // Ignore malformed URLs and continue.
            }
        }, true );
    }

     document.addEventListener( 'DOMContentLoaded', function onReady() {
        var root = document.querySelector( '[data-bd-root]' );

        if ( ! root ) {
            return;
        }

        var bubble = root.querySelector( '[data-bd-bubble]' );
        var panel = root.querySelector( '[data-bd-panel]' );
        var overlay = root.querySelector( '[data-bd-overlay]' );
        var close = root.querySelector( '[data-bd-close]' );
        var views = Array.prototype.slice.call( root.querySelectorAll( '[data-bd-view]' ) );

        if ( ! bubble || ! panel || ! overlay || ! close || ! views.length ) {
            return;
        }

        var state = {
            switchingView: false,
            closeLock: false,
            lastActive: null,
        };

        /**
         * Retrieve a view container by identifier.
         *
         * @param {string} name View identifier.
         *
         * @return {HTMLElement|null}
         */
        function getViewByName( name ) {
            if ( ! name ) {
                return null;
            }

            for ( var i = 0; i < views.length; i += 1 ) {
                if ( views[ i ].dataset && views[ i ].dataset.bdView === name ) {
                    return views[ i ];
                }
            }

            return null;
        }

        /**
         * Retrieve the currently visible view element.
         *
         * @return {HTMLElement|null}
         */
        function getActiveView() {
            for ( var i = 0; i < views.length; i += 1 ) {
                if ( views[ i ].getAttribute( 'aria-hidden' ) === 'false' ) {
                    return views[ i ];
                }
            }

            return null;
        }

        /**
         * Retrieve the active view identifier.
         *
         * @return {string|null}
         */
        function getActiveViewName() {
            var active = getActiveView();
            return active && active.dataset ? active.dataset.bdView : null;
        }

        /**
         * Sync trigger aria-expanded attributes.
         *
         * @param {string} activeView Active view name.
         *
         * @return {void}
         */
        function syncTriggers( activeView ) {
            var triggers = root.querySelectorAll( '[data-bd-target]' );

            Array.prototype.forEach.call( triggers, function ( trigger ) {
                if ( ! trigger.dataset ) {
                    return;
                }

                if ( ! trigger.hasAttribute( 'aria-expanded' ) ) {
                    return;
                }

                var isExpanded = trigger.dataset.bdTarget === activeView;
                trigger.setAttribute( 'aria-expanded', isExpanded ? 'true' : 'false' );
            } );
        }

        /**
         * Update the contextual close button label.
         *
         * @param {string} view Active view.
         *
         * @return {void}
         */
        function updateCloseLabel( view ) {
            var dataset = close.dataset || {};
            var defaultLabel = dataset.closeLabelHome || close.getAttribute( 'aria-label' ) || 'Close panel';
            var trimmedDefault = defaultLabel.trim();

            if ( ! trimmedDefault ) {
                trimmedDefault = 'Close panel';
            }

            var backLabel = dataset.closeLabelBack || trimmedDefault;
            backLabel = backLabel.trim();

            if ( ! backLabel ) {
                backLabel = trimmedDefault;
            }

            var label = 'home' === view ? trimmedDefault : backLabel;
            close.setAttribute( 'aria-label', label );
        }

        /**
         * Focus the primary control for the home view.
         *
         * @return {void}
         */
        function focusHomePrimary() {
            var home = getViewByName( 'home' );

            if ( ! home ) {
                return;
            }

            var primary = home.querySelector( '.blitz-dock__nav-button' );

            if ( primary && isFocusable( primary ) ) {
                focusSafely( primary );
                return;
            }

            var fallbacks = getFocusableWithin( home );

            if ( fallbacks.length ) {
                focusSafely( fallbacks[ 0 ] );
            }
        }

        /**
         * Focus the first meaningful element for a view.
         *
         * @param {string} view    View identifier.
         * @param {HTMLElement} el View element.
         * @param {Element|null} trigger Trigger element.
         *
         * @return {void}
         */
        function focusForView( view, el, trigger ) {
            if ( 'home' === view ) {
                focusHomePrimary();
                return;
            }

            if ( 'channels' === view ) {
                var title = el ? el.querySelector( '#blitz-dock-channels-title' ) : null;

                if ( title ) {
                    if ( ! title.hasAttribute( 'tabindex' ) ) {
                        title.setAttribute( 'tabindex', '-1' );
                    }

                    focusSafely( title );
                    return;
                }
            }

            var focusables = getFocusableWithin( el );

            if ( focusables.length ) {
                focusSafely( focusables[ 0 ] );
                return;
            }

            if ( trigger ) {
                focusSafely( trigger );
            }
        }

        /**
         * Focus the first interactive control when the panel opens.
         *
         * @return {void}
         */
        function focusInitialControl() {
            var active = getActiveView();
            var focusables = getFocusableWithin( active );

            if ( focusables.length ) {
                focusSafely( focusables[ 0 ] );
                return;
            }

            var fallback = getFocusableWithin( panel );

            if ( fallback.length ) {
                focusSafely( fallback[ 0 ] );
                return;
            }

            focusSafely( close );
        }

        /**
         * Switch the active view.
         *
         * @param {string} name View identifier.
         * @param {{ focus?: boolean, trigger?: Element|null }} [options] Options.
         *
         * @return {void}
         */
        function setActiveView( name, options ) {
            if ( ! name || state.switchingView ) {
                return;
            }

            var target = getViewByName( name );

            if ( ! target ) {
                return;
            }

            state.switchingView = true;

            var opts = options || {};

            try {
                views.forEach( function ( view ) {
                    toggleHidden( view, view !== target );
                } );

                updateCloseLabel( name );
                syncTriggers( name );

                if ( opts.focus ) {
                    focusForView( name, target, opts.trigger || null );
                }
            } finally {
                state.switchingView = false;
            }
        }

        /**
         * Open the panel.
         *
         * @return {void}
         */
        function openPanel() {
            if ( panel.hidden ) {
                state.lastActive = document.activeElement;

                bubble.setAttribute( 'aria-expanded', 'true' );
                toggleHidden( panel, false );
                toggleHidden( overlay, false );
                document.body.classList.add( 'blitz-dock-scroll-lock' );
                root.classList.add( 'blitz-dock--open' );

                setActiveView( 'home', { focus: false } );
                document.addEventListener( 'keydown', onKeydown, true );
                focusInitialControl();
            }
        }

        /**
         * Close the panel.
         *
         * @return {void}
         */
        function closePanel() {
            if ( panel.hidden ) {
                return;
            }

            bubble.setAttribute( 'aria-expanded', 'false' );
            document.removeEventListener( 'keydown', onKeydown, true );

            setActiveView( 'home', { focus: false } );

            toggleHidden( panel, true );
            toggleHidden( overlay, true );
            document.body.classList.remove( 'blitz-dock-scroll-lock' );
            root.classList.remove( 'blitz-dock--open' );

            var focusTarget = state.lastActive && isFocusable( state.lastActive ) ? state.lastActive : bubble;

            focusSafely( focusTarget );
            state.lastActive = null;
        }

        /**
         * Trap focus within the panel and support ESC close.
         *
         * @param {KeyboardEvent} event Keyboard event.
         *
         * @return {void}
         */
        function onKeydown( event ) {
            if ( 'Escape' === event.key ) {
                event.preventDefault();
                closePanel();
                return;
            }

            if ( 'Tab' !== event.key ) {
                return;
            }

            var focusables = getFocusableWithin( panel );

            if ( ! focusables.length ) {
                return;
            }

            var first = focusables[ 0 ];
            var last = focusables[ focusables.length - 1 ];

            if ( event.shiftKey ) {
                if ( document.activeElement === first || ! panel.contains( document.activeElement ) ) {
                    event.preventDefault();
                    focusSafely( last );
                }

                return;
            }

            if ( document.activeElement === last ) {
                event.preventDefault();
                focusSafely( first );
            }
        }

               root.addEventListener( 'click', function handleClick( event ) {
            if ( 'function' !== typeof event.target.closest ) {
                return;
            }

            var bubbleTrigger = event.target.closest( '[data-bd-bubble]' );

            if ( bubbleTrigger ) {
                event.preventDefault();

                if ( panel.hidden ) {
                    openPanel();
                } else {
                    closePanel();
                }

                return;
            }

            var overlayTarget = event.target.closest( '[data-bd-overlay]' );

            if ( overlayTarget ) {
                event.preventDefault();
                event.stopPropagation();
                closePanel();
                return;
            }

            var closeTrigger = event.target.closest( '[data-bd-close]' );

            if ( closeTrigger ) {
                event.preventDefault();

                if ( state.closeLock ) {
                    return;
                }

                state.closeLock = true;

                try {
                    var activeView = getActiveViewName();

                    if ( activeView && 'home' !== activeView ) {
                        setActiveView( 'home', { focus: true, trigger: closeTrigger } );
                    } else {
                        closePanel();
                    }
                } finally {
                    state.closeLock = false;
                }

                return;
            }

            var targetTrigger = event.target.closest( '[data-bd-target]' );

            if ( ! targetTrigger || ! root.contains( targetTrigger ) ) {
                return;
            }

            var targetView = targetTrigger.dataset ? targetTrigger.dataset.bdTarget : null;

            if ( ! targetView ) {
                return;
            }

            event.preventDefault();
            setActiveView( targetView, { trigger: targetTrigger, focus: true } );
               } );

        setActiveView( 'home', { focus: false } );
        root.classList.remove( 'blitz-dock--hidden' );
        initChannelIconFallback();
    } );
}() );