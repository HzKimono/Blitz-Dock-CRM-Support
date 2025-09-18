(function () {
  document.addEventListener('DOMContentLoaded', function () {
    var root = document.getElementById('blitz-dock-root');
    if (!root) {
      return;
    }
    var bubble = root.querySelector('.blitz-dock__bubble');
    var panel = root.querySelector('.blitz-dock__panel');
    var overlay = root.querySelector('[data-blitz-dock-overlay]');
    var close = root.querySelector('.blitz-dock__close');
    if (!bubble || !panel || !overlay) {
      return;
    }
    var lastActive = null;
    var focusableSelector = 'a,button,input,select,textarea,[tabindex]:not([tabindex="-1"])';

    function getFocusable() {
      return Array.prototype.slice.call(panel.querySelectorAll(focusableSelector))
        .filter(function (el) {
          return !el.hasAttribute('disabled') && !el.getAttribute('aria-hidden');
        });
    }

    function onKeydown(e) {
      if (e.key === 'Escape') {
        e.preventDefault();
        closePanel();
        return;
      }
      if (e.key === 'Tab') {
        var nodes = getFocusable();
        if (!nodes.length) {
          return;
        }
        var first = nodes[0];
        var last = nodes[nodes.length - 1];
        if (e.shiftKey && document.activeElement === first) {
          e.preventDefault();
          last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
          e.preventDefault();
          first.focus();
        }
      }
    }

    function openPanel() {
      if (!panel.hasAttribute('hidden')) {
        return;
      }
      lastActive = document.activeElement;
      bubble.setAttribute('aria-expanded', 'true');
      panel.removeAttribute('hidden');
      overlay.removeAttribute('hidden');
      document.body.classList.add('blitz-dock-scroll-lock');
      root.classList.add('blitz-dock--open');
      document.addEventListener('keydown', onKeydown);
      var f = getFocusable()[0] || panel;
      f.focus();
    }

    function closePanel() {
      if (panel.hasAttribute('hidden')) {
        return;
      }
      bubble.setAttribute('aria-expanded', 'false');
      panel.setAttribute('hidden', '');
      overlay.setAttribute('hidden', '');
      document.body.classList.remove('blitz-dock-scroll-lock');
      root.classList.remove('blitz-dock--open');
      document.removeEventListener('keydown', onKeydown);
      if (lastActive && lastActive.focus) {
        lastActive.focus();
      }
    }

    bubble.addEventListener('click', function () {
      if (panel.hasAttribute('hidden')) {
        openPanel();
      } else {
        closePanel();
      }
    });

    overlay.addEventListener('click', closePanel);
    if (close) {
      close.addEventListener('click', closePanel);
    }

    root.classList.remove('blitz-dock--hidden');
  });
})();