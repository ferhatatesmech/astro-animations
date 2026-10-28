/**
 * Astro Animations - Frontend Public Scripts
 *
 * @package AstroAnimations
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var lazyElements = document.querySelectorAll('.astro-anim-root[data-lazy="1"]');
    if (!lazyElements.length) return;

    if ('IntersectionObserver' in window) {
      var obs = new IntersectionObserver(function (entries, observer) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-in-view');
            entry.target.dispatchEvent(new CustomEvent('astro:anim-inview', { bubbles: true }));
            observer.unobserve(entry.target);
          }
        });
      }, { rootMargin: '120px 0px', threshold: 0.05 });

      lazyElements.forEach(function (el) {
        obs.observe(el);
      });
    } else {
      lazyElements.forEach(function (el) {
        el.classList.add('is-in-view');
      });
    }
  });
})();
