<?php

if (!defined('ABSPATH')) {
    exit;
}

add_shortcode('rs_apartment_status_pill', function () {
    $post_id = get_the_ID();

    if (!$post_id) {
        return '';
    }

    if (function_exists('get_field')) {
        $status = get_field('status', $post_id);
    } else {
        $status = get_post_meta($post_id, 'status', true);
    }

    $status = sanitize_key((string) $status);

    $statuses = [
        'available' => [
            'label' => 'Na predaj',
            'class' => 'rs-status-pill--available',
        ],
        'reserved' => [
            'label' => 'Rezervovaný',
            'class' => 'rs-status-pill--reserved',
        ],
        'sold' => [
            'label' => 'Predaný',
            'class' => 'rs-status-pill--sold',
        ],
    ];

    if (!isset($statuses[$status])) {
        return '';
    }

    return sprintf(
        '<span class="%s">%s</span>',
        esc_attr('rs-status-pill ' . $statuses[$status]['class']),
        esc_html($statuses[$status]['label'])
    );
});

add_action('wp_footer', function () {
    if (is_admin()) {
        return;
    }
    ?>
    <script id="rs-status-pill-normalizer">
      (function(){
        var statusClasses = ['rs-status-pill--available', 'rs-status-pill--reserved', 'rs-status-pill--sold'];
        var aliases = {
          available: ['available', 'na predaj', 'na-predaj', 'dostupny', 'dostupne', 'dostupná', 'dostupna', 'volny', 'volne', 'voľny', 'voľne'],
          reserved: ['reserved', 'rezervovany', 'rezervovane', 'rezervovaný', 'rezervované'],
          sold: ['sold', 'predany', 'predane', 'predaný', 'predané']
        };

        function normalize(value){
          value = String(value || '').trim().toLowerCase();
          if(value.normalize){
            value = value.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
          }
          return value.replace(/[_-]+/g, ' ').replace(/\s+/g, ' ').trim();
        }

        function resolveStatus(value){
          var normalized = normalize(value);
          if(!normalized){ return ''; }
          for(var status in aliases){
            if(!Object.prototype.hasOwnProperty.call(aliases, status)){ continue; }
            for(var i = 0; i < aliases[status].length; i++){
              if(normalize(aliases[status][i]) === normalized){ return status; }
            }
          }
          return '';
        }

        function syncPills(){
          document.querySelectorAll('.rs-status-pill').forEach(function(pill){
            var status = resolveStatus(pill.getAttribute('data-status')) || resolveStatus(pill.textContent);
            if(!status){ return; }
            statusClasses.forEach(function(className){ pill.classList.remove(className); });
            pill.classList.add('rs-status-pill--' + status);
            pill.setAttribute('data-status', status);
          });
        }

        document.addEventListener('DOMContentLoaded', syncPills);
        window.addEventListener('load', syncPills);
        if(typeof MutationObserver !== 'undefined'){
          var timer = null;
          new MutationObserver(function(){
            clearTimeout(timer);
            timer = setTimeout(syncPills, 80);
          }).observe(document.documentElement, { childList: true, subtree: true, characterData: true });
        }
      })();
    </script>
    <?php
}, 99);

