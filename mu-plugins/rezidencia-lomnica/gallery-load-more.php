<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', function () {
    if (is_admin()) {
        return;
    }

    $style = <<<'CSS'
.rs-gallery-loadmore .rs-gallery-hidden {
    display: none !important;
}

.rs-gallery-loadmore {
    width: 100%;
}

.rs-gallery-loadmore-btn {
    margin-top: 3.2rem;
    margin-left: auto;
    margin-right: auto;
    align-items: center;
    justify-content: center;
}
CSS;

    $script = <<<'JS'
(function () {
            if (window.rsGalleryLoadMoreInit) {
                return;
            }

            window.rsGalleryLoadMoreInit = true;

            function toArray(list) {
                return Array.prototype.slice.call(list || []);
            }

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
                var candidates = toArray(wrap.querySelectorAll('.happyfiles-gallery, .brxe-happyfiles-gallery, .brxe-image-gallery, .brxe-gallery'));

                for (var i = 0; i < candidates.length; i++) {
                    var candidate = candidates[i];
                    if (candidate && candidate.matches && candidate.matches('.happyfiles-gallery') && candidate.querySelector(':scope > ul > li.item')) {
                        return candidate;
                    }
                }

                for (var j = 0; j < candidates.length; j++) {
                    var fallback = candidates[j];
                    if (fallback && fallback.querySelector && fallback.querySelector('img')) {
                        return fallback;
                    }
                }

                return wrap;
            }

            function getCards(wrap) {
                var root = getGalleryRoot(wrap);
                var selectors = [
                    ':scope > ul > li.item',
                    ':scope > li.item',
                    ':scope > li',
                    'ul > li.item',
                    'ul > li',
                    '.bricks-layout-item',
                    '.bricks-gallery-item',
                    '.gallery-item',
                    '.happyfiles-gallery-item'
                ];
                var cards = [];

                selectors.forEach(function (selector) {
                    try {
                        toArray(root.querySelectorAll(selector)).forEach(function (item) {
                            if (item && item.querySelector && item.querySelector('img')) {
                                cards.push(item);
                            }
                        });
                    } catch (error) {}
                });

                if (!cards.length) {
                    toArray(root.querySelectorAll('img')).forEach(function (img) {
                        var card = img.closest('li, a, .bricks-layout-item, .bricks-gallery-item, .gallery-item, .happyfiles-gallery-item, .brxe-image');
                        if (card) {
                            cards.push(card);
                        }
                    });
                }

                cards = unique(cards).filter(function (item) {
                    return item
                        && root.contains(item)
                        && item !== root
                        && !(item.matches && item.matches('.happyfiles-gallery, .brxe-happyfiles-gallery, .brxe-image-gallery, .brxe-gallery'))
                        && !item.classList.contains('rs-gallery-loadmore-btn')
                        && !item.closest('.rs-gallery-loadmore-btn');
                });

                return cards;
            }

            function refresh(wrap) {
                var btn = wrap.querySelector('.rs-gallery-loadmore-btn');
                if (!btn) return;

                var cards = getCards(wrap);
                if (!cards.length) return;

                var visible = parseInt(wrap.getAttribute('data-rs-visible') || wrap.getAttribute('data-initial-visible') || '20', 10);
                var step = parseInt(wrap.getAttribute('data-load-step') || '20', 10);

                cards.forEach(function (card, index) {
                    card.classList.toggle('rs-gallery-hidden', index >= visible);
                    if (index >= visible) {
                        card.setAttribute('aria-hidden', 'true');
                    } else {
                        card.removeAttribute('aria-hidden');
                    }
                });

                btn.style.display = visible >= cards.length ? 'none' : 'inline-flex';

                if (wrap.getAttribute('data-rs-gallery-bound') !== '1') {
                    wrap.setAttribute('data-rs-gallery-bound', '1');
                    btn.addEventListener('click', function (event) {
                        event.preventDefault();
                        var current = parseInt(wrap.getAttribute('data-rs-visible') || wrap.getAttribute('data-initial-visible') || '20', 10);
                        wrap.setAttribute('data-rs-visible', String(current + step));
                        refresh(wrap);
                    });
                }
            }

            function init() {
                toArray(document.querySelectorAll('.rs-gallery-loadmore')).forEach(function (wrap) {
                    refresh(wrap);
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }

            window.addEventListener('load', init);
            setTimeout(init, 250);
            setTimeout(init, 1000);
            setTimeout(init, 2500);
})();
JS;

    wp_register_style('rs-gallery-load-more', false, [], null);
    wp_enqueue_style('rs-gallery-load-more');
    wp_add_inline_style('rs-gallery-load-more', $style);

    wp_register_script('rs-gallery-load-more', '', [], null, true);
    wp_enqueue_script('rs-gallery-load-more');
    wp_add_inline_script('rs-gallery-load-more', $script);
}, 99);

add_action('wp_head', function () {
    if (is_admin()) {
        return;
    }
    ?>
    <style id="rs-gallery-load-more-css-fallback">
        .rs-gallery-loadmore .rs-gallery-hidden {
            display: none !important;
        }

        .rs-gallery-loadmore {
            width: 100%;
        }

        .rs-gallery-loadmore-btn {
            margin-top: 3.2rem;
            margin-left: auto;
            margin-right: auto;
            align-items: center;
            justify-content: center;
        }
    </style>
    <?php
}, 100);

add_action('wp_footer', function () {
    if (is_admin()) {
        return;
    }
    ?>
    <script id="rs-gallery-load-more-js-fallback">
        (function () {
            if (window.rsGalleryLoadMoreInit) {
                return;
            }

            window.rsGalleryLoadMoreInit = true;

            function toArray(list) {
                return Array.prototype.slice.call(list || []);
            }

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
                var candidates = toArray(wrap.querySelectorAll('.happyfiles-gallery, .brxe-happyfiles-gallery, .brxe-image-gallery, .brxe-gallery'));

                for (var i = 0; i < candidates.length; i++) {
                    var candidate = candidates[i];
                    if (candidate && candidate.matches && candidate.matches('.happyfiles-gallery') && candidate.querySelector(':scope > ul > li.item')) {
                        return candidate;
                    }
                }

                for (var j = 0; j < candidates.length; j++) {
                    var fallback = candidates[j];
                    if (fallback && fallback.querySelector && fallback.querySelector('img')) {
                        return fallback;
                    }
                }

                return wrap;
            }

            function getCards(wrap) {
                var root = getGalleryRoot(wrap);
                var selectors = [
                    ':scope > ul > li.item',
                    ':scope > li.item',
                    ':scope > li',
                    'ul > li.item',
                    'ul > li',
                    '.bricks-layout-item',
                    '.bricks-gallery-item',
                    '.gallery-item',
                    '.happyfiles-gallery-item'
                ];
                var cards = [];

                selectors.forEach(function (selector) {
                    try {
                        toArray(root.querySelectorAll(selector)).forEach(function (item) {
                            if (item && item.querySelector && item.querySelector('img')) {
                                cards.push(item);
                            }
                        });
                    } catch (error) {}
                });

                if (!cards.length) {
                    toArray(root.querySelectorAll('img')).forEach(function (img) {
                        var card = img.closest('li, a, .bricks-layout-item, .bricks-gallery-item, .gallery-item, .happyfiles-gallery-item, .brxe-image');
                        if (card) {
                            cards.push(card);
                        }
                    });
                }

                cards = unique(cards).filter(function (item) {
                    return item
                        && root.contains(item)
                        && item !== root
                        && !(item.matches && item.matches('.happyfiles-gallery, .brxe-happyfiles-gallery, .brxe-image-gallery, .brxe-gallery'))
                        && !item.classList.contains('rs-gallery-loadmore-btn')
                        && !item.closest('.rs-gallery-loadmore-btn');
                });

                return cards;
            }

            function refresh(wrap) {
                var btn = wrap.querySelector('.rs-gallery-loadmore-btn');
                if (!btn) return;

                var cards = getCards(wrap);
                if (!cards.length) return;

                var visible = parseInt(wrap.getAttribute('data-rs-visible') || wrap.getAttribute('data-initial-visible') || '20', 10);
                var step = parseInt(wrap.getAttribute('data-load-step') || '20', 10);

                cards.forEach(function (card, index) {
                    card.classList.toggle('rs-gallery-hidden', index >= visible);
                    if (index >= visible) {
                        card.setAttribute('aria-hidden', 'true');
                    } else {
                        card.removeAttribute('aria-hidden');
                    }
                });

                btn.style.display = visible >= cards.length ? 'none' : 'inline-flex';

                if (wrap.getAttribute('data-rs-gallery-bound') !== '1') {
                    wrap.setAttribute('data-rs-gallery-bound', '1');
                    btn.addEventListener('click', function (event) {
                        event.preventDefault();
                        var current = parseInt(wrap.getAttribute('data-rs-visible') || wrap.getAttribute('data-initial-visible') || '20', 10);
                        wrap.setAttribute('data-rs-visible', String(current + step));
                        refresh(wrap);
                    });
                }
            }

            function init() {
                toArray(document.querySelectorAll('.rs-gallery-loadmore')).forEach(function (wrap) {
                    refresh(wrap);
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }

            window.addEventListener('load', init);
            setTimeout(init, 250);
            setTimeout(init, 1000);
            setTimeout(init, 2500);
        })();
    </script>
    <?php
}, 100);

