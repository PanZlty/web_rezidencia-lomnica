<?php
/**
 * Gallery load-more script loaded as a MU-plugin module.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_footer', static function () {
    if (is_admin()) {
        return;
    }
    ?>
    <script id="gallery-load-more-script">
(function () {
  function unique(items) {
    var result = [];

    items.forEach(function (item) {
      if (item && result.indexOf(item) === -1) {
        result.push(item);
      }
    });

    return result;
  }

  function getGalleryRoot(wrap) {
    var roots = wrap.querySelectorAll('.happyfiles-gallery, .brxe-happyfiles-gallery, .brxe-image-gallery, .brxe-gallery, [class*="gallery"]');

    for (var i = 0; i < roots.length; i++) {
      if (
        !roots[i].classList.contains('rs-gallery-loadmore-btn') &&
        roots[i].matches &&
        roots[i].matches('.happyfiles-gallery') &&
        roots[i].querySelector(':scope > ul > li.item')
      ) {
        return roots[i];
      }
    }

    for (var j = 0; j < roots.length; j++) {
      if (!roots[j].classList.contains('rs-gallery-loadmore-btn') && roots[j].querySelector('img')) {
        return roots[j];
      }
    }

    return wrap;
  }

  function getItems(wrap) {
    var root = getGalleryRoot(wrap);
    var images = Array.prototype.slice.call(root.querySelectorAll('img'));
    var items = images.map(function (img) {
      return img.closest('li, .bricks-layout-item, .bricks-gallery-item, .gallery-item, .happyfiles-gallery-item, .brxe-image, a') || img.parentElement;
    }).filter(function (item) {
      return item &&
        root.contains(item) &&
        item !== root &&
        !(item.matches && item.matches('.happyfiles-gallery, .brxe-happyfiles-gallery, .brxe-image-gallery, .brxe-gallery')) &&
        !item.classList.contains('rs-gallery-loadmore-btn') &&
        !item.closest('.rs-gallery-loadmore-btn');
    });

    items = unique(items);

    if (items.length) {
      return items;
    }

    return Array.prototype.slice.call(root.children).filter(function (item) {
      return item && item.querySelector && item.querySelector('img') && !item.classList.contains('rs-gallery-loadmore-btn');
    });
  }

  function apply(wrap) {
    var btn = wrap.querySelector('.rs-gallery-loadmore-btn');
    if (!btn) return false;

    var items = getItems(wrap);
    if (!items.length) return false;

    var visible = parseInt(wrap.getAttribute('data-rs-visible') || wrap.getAttribute('data-initial-visible') || '20', 10);
    var step = parseInt(wrap.getAttribute('data-load-step') || '20', 10);

    items.forEach(function (item, index) {
      if (index < visible) {
        item.classList.remove('rs-gallery-hidden');
        item.removeAttribute('aria-hidden');
      } else {
        item.classList.add('rs-gallery-hidden');
        item.setAttribute('aria-hidden', 'true');
      }
    });

    btn.style.display = visible >= items.length ? 'none' : 'inline-flex';

    if (wrap.getAttribute('data-rs-click-ready') !== '1') {
      wrap.setAttribute('data-rs-click-ready', '1');

      btn.addEventListener('click', function (event) {
        event.preventDefault();
        var current = parseInt(wrap.getAttribute('data-rs-visible') || wrap.getAttribute('data-initial-visible') || '20', 10);
        wrap.setAttribute('data-rs-visible', String(current + step));
        apply(wrap);
      });
    }

    return true;
  }

  function init() {
    document.querySelectorAll('.rs-gallery-loadmore').forEach(function (wrap) {
      apply(wrap);

      if (wrap.getAttribute('data-rs-observer-ready') === '1') return;
      wrap.setAttribute('data-rs-observer-ready', '1');

      var observer = new MutationObserver(function () {
        apply(wrap);
      });

      observer.observe(wrap, {
        childList: true,
        subtree: true
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  window.addEventListener('load', init);
  setTimeout(init, 500);
  setTimeout(init, 1500);
  setTimeout(init, 3000);
})();
    </script>
    <?php
}, 98);
