<?php

if (!defined('ABSPATH')) {
    exit;
}

/* =========================================
   REZIDENCIA Ĺ TĂšROVĂ â€“ IMAGE MAP NASTAVENIA
   Tu menĂ­Ĺˇ farby tooltipu, pillov, ikon, tlaÄŤidla
   a default farbu shapes podÄľa statusu bytu.

   Hover farby, dashed stroke a mouseover sprĂˇvanie
   nastavuj iba v Image Map Pro UI.

   CSS premennĂ© pĂ­Ĺˇ bez fallbacku napr.:
   'pill_bg' => 'var(--status-sold)'
========================================= */
if (!function_exists('rs_imp_design_tokens')) {
    function rs_imp_design_tokens() {
        return array(
            'text' => 'var(--text-dark)',
            'image_bg' => 'var(--bg-muted)',
            'button_bg' => 'var(--primary)',
            'button_text' => 'var(--white)',
            'button_hover_bg' => 'var(--tertiary)',
            'button_hover_text' => 'var(--neutral-dark)',
            'status' => array(
                'available' => array(
                    'label' => 'Na predaj',
                    'pill_bg' => 'var(--status-available)',
                    'pill_text' => 'var(--white)',
                    'icon' => 'var(--icon-accent)',
                    'shape_fill' => 'var(--status-available)',
                    'shape_stroke' => 'var(--status-available)',
                    'shape_fill_opacity' => 0.26,
                    'shape_stroke_opacity' => 0.9,
                ),
                'reserved' => array(
                    'label' => 'RezervovanĂ˝',
                    'pill_bg' => 'var(--status-reserved)',
                    'pill_text' => 'var(--white)',
                    'icon' => 'var(--icon-accent)',
                    'shape_fill' => 'var(--status-reserved)',
                    'shape_stroke' => 'var(--status-reserved)',
                    'shape_fill_opacity' => 0.28,
                    'shape_stroke_opacity' => 0.95,
                ),
                'sold' => array(
                    'label' => 'PredanĂ˝',
                    'pill_bg' => 'var(--status-sold)',
                    'pill_text' => 'var(--white)',
                    'icon' => 'var(--icon-accent)',
                    'shape_fill' => 'var(--status-sold)',
                    'shape_stroke' => 'var(--status-sold)',
                    'shape_fill_opacity' => 0.24,
                    'shape_stroke_opacity' => 0.85,
                ),
                'unknown' => array(
                    'label' => 'Status neuvedenĂ˝',
                    'pill_bg' => 'var(--primary)',
                    'pill_text' => 'var(--white)',
                    'icon' => 'var(--icon-accent)',
                    'shape_fill' => 'var(--primary)',
                    'shape_stroke' => 'var(--primary)',
                    'shape_fill_opacity' => 0.22,
                    'shape_stroke_opacity' => 0.75,
                ),
            ),
        );
    }
}

if (!function_exists('rs_imp_status_token')) {
    function rs_imp_status_token($status) {
        $tokens = rs_imp_design_tokens();
        $status = rs_imp_normalize_status($status);
        return isset($tokens['status'][$status]) ? $tokens['status'][$status] : $tokens['status']['unknown'];
    }
}

if (!function_exists('rs_imp_normalize_status')) {
    function rs_imp_normalize_status($status) {
        $status = (string) $status;
        if (function_exists('remove_accents')) {
            $status = remove_accents($status);
        }
        $status = sanitize_key(str_replace(' ', '-', $status));
        $aliases = array(
            'available' => array('available', 'na-predaj', 'na_predaj', 'dostupne', 'dostupny', 'volne', 'volny', 'free'),
            'reserved' => array('reserved', 'rezervovane', 'rezervovany', 'rezervovana'),
            'sold' => array('sold', 'predane', 'predany', 'predana'),
        );

        foreach ($aliases as $normalized => $values) {
            if (in_array($status, $values, true)) {
                return $normalized;
            }
        }

        return $status;
    }
}

if (!function_exists('rs_imp_field')) {
    function rs_imp_field($field, $post_id) {
        if (is_array($field)) {
            foreach ($field as $single_field) {
                $value = rs_imp_field($single_field, $post_id);
                if ($value !== '' && $value !== null && $value !== false) {
                    return $value;
                }
            }
            return '';
        }

        if (function_exists('get_field')) {
            return get_field($field, $post_id);
        }
        return get_post_meta($post_id, $field, true);
    }
}

if (!function_exists('rs_imp_unit_definitions')) {
    function rs_imp_unit_definitions() {
        return array(
            'apartment' => array(
                'post_types' => array('byty'),
                'code_field' => 'apartment_code',
                'price_field' => 'price',
                'status_field' => 'status',
                'detail_url' => true,
                'type_label' => 'Byt',
            ),
            'parking' => array(
                'post_types' => array('parking', 'parkovacie-miesta'),
                'code_field' => 'parking_number',
                'price_field' => 'parking_price',
                'status_field' => 'parking_status',
                'detail_url' => false,
                'type_label' => 'Parkovacie miesto',
            ),
        );
    }
}

if (!function_exists('rs_imp_existing_post_types')) {
    function rs_imp_existing_post_types($post_types) {
        $existing = array();
        foreach ((array) $post_types as $post_type) {
            if (post_type_exists($post_type)) {
                $existing[] = $post_type;
            }
        }
        return $existing;
    }
}

if (!function_exists('rs_imp_unit_definition_for_post_type')) {
    function rs_imp_unit_definition_for_post_type($post_type) {
        foreach (rs_imp_unit_definitions() as $unit_type => $definition) {
            if (in_array($post_type, (array) $definition['post_types'], true)) {
                $definition['unit_type'] = $unit_type;
                return $definition;
            }
        }
        return array();
    }
}

if (!function_exists('rs_imp_normalize_unit_type')) {
    function rs_imp_normalize_unit_type($unit_type) {
        $unit_type = sanitize_key((string) $unit_type);
        $aliases = array(
            'apartment' => array('apartment', 'apartments', 'byt', 'byty'),
            'parking' => array('parking', 'parkovanie', 'parkovacie-miesta', 'parkovacie_miesta'),
        );

        foreach ($aliases as $normalized => $values) {
            if (in_array($unit_type, $values, true)) {
                return $normalized;
            }
        }

        return $unit_type;
    }
}

if (!function_exists('rs_imp_display_title')) {
    function rs_imp_display_title($post_id, $code = '') {
        $code = sanitize_text_field((string) $code);
        if ($code !== '' && preg_match('/^AP-[A-Z]$/i', $code)) {
            return strtoupper($code);
        }
        return get_the_title($post_id);
    }
}

if (!function_exists('rs_imp_code_candidates')) {
    function rs_imp_code_candidates($code, $unit_type = '') {
        $code = sanitize_text_field((string) $code);
        $unit_type = rs_imp_normalize_unit_type($unit_type);
        if ($code === '') { return array(); }

        $candidates = array($code);

        if ($unit_type === 'parking') {
            if (preg_match('/^[PG]\s*-?\s*(\d+)$/i', $code, $match)) {
                $number = (string) absint($match[1]);
                $candidates[] = str_pad($number, 2, '0', STR_PAD_LEFT);
                $candidates[] = $number;
            } elseif (preg_match('/^\d+$/', $code)) {
                $number = (string) absint($code);
                $padded = str_pad($number, 2, '0', STR_PAD_LEFT);
                $candidates[] = $padded;
                $candidates[] = 'P' . $padded;
                $candidates[] = 'G' . $padded;
            }
        }

        return array_values(array_unique(array_filter($candidates, 'strlen')));
    }
}

if (!function_exists('rs_imp_map_unit_aliases')) {
    function rs_imp_map_unit_aliases($code, $unit_type = '') {
        return rs_imp_code_candidates($code, $unit_type);
    }
}

if (!function_exists('rs_imp_unit_id_by_code')) {
    function rs_imp_unit_id_by_code($code, $unit_type = '') {
        $code = sanitize_text_field((string) $code);
        $unit_type = rs_imp_normalize_unit_type($unit_type);
        if ($code === '') { return array(); }

        $definitions = rs_imp_unit_definitions();
        if ($unit_type !== '' && isset($definitions[$unit_type])) {
            $definitions = array($unit_type => $definitions[$unit_type]);
        }

        foreach ($definitions as $current_type => $definition) {
            $post_types = rs_imp_existing_post_types($definition['post_types']);
            if (empty($post_types)) { continue; }
            $code_candidates = rs_imp_code_candidates($code, $current_type);

            $posts = get_posts(array(
                'post_type' => $post_types,
                'post_status' => 'publish',
                'posts_per_page' => 1,
                'fields' => 'ids',
                'meta_query' => array(array('key' => $definition['code_field'], 'value' => $code_candidates, 'compare' => 'IN')),
            ));

            if (!empty($posts)) {
                $definition['unit_type'] = $current_type;
                return array('id' => absint($posts[0]), 'type' => $current_type, 'definition' => $definition);
            }
        }

        return array();
    }
}

if (!function_exists('rs_imp_resolve_unit')) {
    function rs_imp_resolve_unit($code = '', $unit_type = '') {
        $code = sanitize_text_field((string) $code);
        $unit_type = rs_imp_normalize_unit_type($unit_type);
        if ($code !== '') { return rs_imp_unit_id_by_code($code, $unit_type); }

        $post_id = get_the_ID();
        if ($post_id) {
            $definition = rs_imp_unit_definition_for_post_type(get_post_type($post_id));
            if (!empty($definition)) {
                return array('id' => absint($post_id), 'type' => $definition['unit_type'], 'definition' => $definition);
            }
        }

        global $post;
        if ($post instanceof WP_Post && $post->ID) {
            $definition = rs_imp_unit_definition_for_post_type(get_post_type($post->ID));
            if (!empty($definition)) {
                return array('id' => absint($post->ID), 'type' => $definition['unit_type'], 'definition' => $definition);
            }
        }

        return array();
    }
}

if (!function_exists('rs_imp_unit_status_label')) {
    function rs_imp_unit_status_label($status, $unit_type = 'apartment') {
        $status = sanitize_key((string) $status);
        $unit_type = sanitize_key((string) $unit_type);

        $labels = array(
            'apartment' => array(
                'available' => 'Na predaj',
                'reserved' => 'RezervovanĂ˝',
                'sold' => 'PredanĂ˝',
            ),
            'parking' => array(
                'available' => 'DostupnĂ©',
                'reserved' => 'RezervovanĂ©',
                'sold' => 'PredanĂ©',
            ),
        );

        return isset($labels[$unit_type][$status]) ? $labels[$unit_type][$status] : rs_imp_status_label($status);
    }
}

if (!function_exists('rs_imp_apartment_id_by_code')) {
    function rs_imp_apartment_id_by_code($code) {
        $unit = rs_imp_unit_id_by_code($code, 'apartment');
        return empty($unit['id']) ? 0 : absint($unit['id']);
    }
}

if (!function_exists('rs_imp_resolve_apartment_id')) {
    function rs_imp_resolve_apartment_id($code = '') {
        $code = sanitize_text_field((string) $code);
        if ($code !== '') { return rs_imp_apartment_id_by_code($code); }

        $post_id = get_the_ID();
        if ($post_id && get_post_type($post_id) === 'byty') { return absint($post_id); }

        global $post;
        if ($post instanceof WP_Post && $post->ID && get_post_type($post->ID) === 'byty') {
            return absint($post->ID);
        }

        return 0;
    }
}

if (!function_exists('rs_imp_price_text')) {
    function rs_imp_price_text($price) {
        if ($price === '' || $price === null || is_array($price) || is_object($price)) { return ''; }

        $value = preg_replace('/[^0-9,.]/', '', (string) $price);
        $value = str_replace(',', '.', $value);
        if ($value === '' || !is_numeric($value)) { return ''; }

        return number_format((float) $value, 0, ',', ' ') . ' â‚¬';
    }
}

if (!function_exists('rs_imp_number_text')) {
    function rs_imp_number_text($value, $suffix = '') {
        if ($value === '' || $value === null || is_array($value) || is_object($value)) { return ''; }
        $value = str_replace('.', ',', (string) $value);
        return trim($value . ' ' . $suffix);
    }
}

if (!function_exists('rs_imp_room_text')) {
    function rs_imp_room_text($rooms) {
        if ($rooms === '' || $rooms === null || is_array($rooms) || is_object($rooms)) { return ''; }

        $rooms = (string) $rooms;
        if ($rooms === '0') { return 'Ĺ tĂşdio'; }
        if ($rooms === '1') { return '1-izbovĂ˝'; }
        if (in_array($rooms, array('2', '3', '4'), true)) { return $rooms . '-izbovĂ˝'; }

        return $rooms . '-izbovĂ˝';
    }
}

if (!function_exists('rs_imp_status_label')) {
    function rs_imp_status_label($status) {
        $token = rs_imp_status_token($status);
        return $token['label'];
    }
}

if (!function_exists('rs_imp_status_colors')) {
    function rs_imp_status_colors($status) {
        $token = rs_imp_status_token($status);
        return array(
            'fill' => $token['shape_fill'],
            'stroke' => $token['shape_stroke'],
            'fillOpacity' => $token['shape_fill_opacity'],
            'strokeOpacity' => $token['shape_stroke_opacity'],
        );
    }
}

add_shortcode('rs_apartment_price', function($atts) {
    $atts = shortcode_atts(array('code' => ''), $atts, 'rs_apartment_price');
    $post_id = rs_imp_resolve_apartment_id($atts['code']);
    if (!$post_id) { return ''; }

    return esc_html(rs_imp_price_text(rs_imp_field('price', $post_id)));
});

add_shortcode('rs_apartment_context', function($atts, $content = null) {
    $atts = shortcode_atts(array('code' => ''), $atts, 'rs_apartment_context');
    $post_id = rs_imp_resolve_apartment_id($atts['code']);
    if (!$post_id || $content === null) { return ''; }

    global $post;
    $old_post = $post;
    $post = get_post($post_id);
    if (!$post) { $post = $old_post; return ''; }

    setup_postdata($post);
    $output = do_shortcode($content);
    wp_reset_postdata();
    $post = $old_post;

    return $output;
});

add_shortcode('rs_apartment_map_card', function($atts) {
    $atts = shortcode_atts(array('code' => ''), $atts, 'rs_apartment_map_card');
    $post_id = rs_imp_resolve_apartment_id($atts['code']);
    if (!$post_id) { return ''; }

    $tokens = rs_imp_design_tokens();
    $status = rs_imp_normalize_status(rs_imp_field('status', $post_id));
    $status_token = rs_imp_status_token($status);
    $price = rs_imp_price_text(rs_imp_field('price', $post_id));
    $rooms = rs_imp_room_text(rs_imp_field('rooms', $post_id));
    $area_total = rs_imp_number_text(rs_imp_field('area_total', $post_id), 'mÂ˛');
    $balcony_area = rs_imp_number_text(rs_imp_field('balcony_area', $post_id), 'mÂ˛');
    $cellar_code = sanitize_text_field((string) rs_imp_field('cellar_code', $post_id));
    $cellar_area = rs_imp_number_text(rs_imp_field('cellar_area', $post_id), 'mÂ˛');
    $image = get_the_post_thumbnail_url($post_id, 'large');
    $url = get_permalink($post_id);
    $display_title = rs_imp_display_title($post_id, rs_imp_field('apartment_code', $post_id));

    if (!$image) {
        $gallery = rs_imp_field('apartment_gallery', $post_id);
        if (is_array($gallery) && !empty($gallery)) {
            $first = reset($gallery);
            if (is_array($first) && !empty($first['url'])) { $image = $first['url']; }
            elseif (is_numeric($first)) { $image = wp_get_attachment_image_url((int) $first, 'large'); }
        }
    }

    $meta_items = array();
    if ($rooms) { $meta_items[] = array('icon' => rl_asset_url('dispozicia.svg'), 'value' => $rooms); }
    if ($area_total) { $meta_items[] = array('icon' => rl_asset_url('vymera.svg'), 'value' => $area_total); }
    if ($balcony_area) { $meta_items[] = array('icon' => rl_asset_url('balkon.svg'), 'value' => $balcony_area); }
    if ($cellar_code || $cellar_area) {
        $meta_items[] = array(
            'icon' => rl_asset_url('sklad.svg'),
            'value' => $cellar_area ? 'Sklad ' . $cellar_area : 'Sklad v cene',
        );
    }

    $tooltip_vars = array(
        '--rs-map-text' => $tokens['text'],
        '--rs-map-image-bg' => $tokens['image_bg'],
        '--rs-map-icon-color' => $status_token['icon'],
        '--rs-map-pill-bg' => $status_token['pill_bg'],
        '--rs-map-pill-text' => $status_token['pill_text'],
        '--rs-map-button-bg' => $tokens['button_bg'],
        '--rs-map-button-text' => $tokens['button_text'],
        '--rs-map-button-hover-bg' => $tokens['button_hover_bg'],
        '--rs-map-button-hover-text' => $tokens['button_hover_text'],
    );
    $tooltip_style = '';
    foreach ($tooltip_vars as $name => $value) {
        $tooltip_style .= $name . ':' . $value . ';';
    }

    ob_start();
    ?>
    <style>
      .rs-map-tooltip{width:100%;max-width:360px;color:var(--rs-map-text);font-family:inherit}
      .rs-map-tooltip *{box-sizing:border-box}
      .rs-map-tooltip__image{display:block;width:100%;height:190px;object-fit:cover;border-radius:18px;margin:0 0 26px;background:var(--rs-map-image-bg)}
      .rs-map-tooltip__top{display:flex;align-items:center;justify-content:space-between;gap:18px;margin:0 0 22px}
      .rs-map-tooltip__title{margin:0;color:var(--rs-map-text);font-size:28px;font-weight:650;line-height:1.05}
      .rs-map-tooltip__pill{display:inline-flex;align-items:center;justify-content:center;min-height:32px;padding:0 13px;border-radius:999px;background:var(--rs-map-pill-bg) !important;color:var(--rs-map-pill-text) !important;font-size:13px;font-weight:700;line-height:1;white-space:nowrap;flex-shrink:0}
      .rs-map-tooltip__price{display:flex;align-items:flex-start;margin:0 0 24px;color:var(--rs-map-text);font-size:36px;font-weight:750;line-height:1.05;letter-spacing:-.03em}
      .rs-map-tooltip__meta{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;margin:0 0 30px;padding:0;list-style:none}
      .rs-map-tooltip__meta-item{display:flex;align-items:center;justify-content:flex-start;gap:9px;min-width:0;color:var(--rs-map-text);font-size:14px;font-weight:700;line-height:1.1;white-space:nowrap}
      .rs-map-tooltip__meta-icon{width:21px;height:21px;flex:0 0 21px;background-color:var(--rs-map-icon-color);-webkit-mask:var(--rs-map-icon) center/contain no-repeat;mask:var(--rs-map-icon) center/contain no-repeat}
      .rs-map-tooltip__button{display:flex;align-items:center;justify-content:center;min-height:50px;width:100%;border-radius:999px;background:var(--rs-map-button-bg) !important;color:var(--rs-map-button-text) !important;font-size:16px;font-weight:750;text-decoration:none;transition:background .2s,color .2s,transform .2s}
      .rs-map-tooltip__button:hover{background:var(--rs-map-button-hover-bg) !important;color:var(--rs-map-button-hover-text) !important;transform:translateY(-1px)}
    </style>
    <div class="rs-map-tooltip rs-map-tooltip--<?php echo esc_attr($status ?: 'unknown'); ?>" style="<?php echo esc_attr($tooltip_style); ?>">
      <?php if ($image) : ?><img class="rs-map-tooltip__image" src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($display_title); ?>"><?php endif; ?>
      <div class="rs-map-tooltip__top">
        <h3 class="r…2535 tokens truncated…,
                'typeLabel' => $definition['type_label'],
                'code' => $code,
                'title' => get_the_title($post_id),
                'status' => $status,
                'price' => rs_imp_price_text(rs_imp_field($definition['price_field'], $post_id)),
                'colors' => rs_imp_status_colors($status),
            );

            foreach (rs_imp_map_unit_aliases($code, $unit_type) as $alias) {
                if (!isset($map_units[$alias])) {
                    $map_units[$alias] = $map_unit;
                }
            }
        }
    }

    if (empty($map_units)) { return; }

    echo '<script>window.RS_IMP_MAP_UNITS = ' . wp_json_encode($map_units) . '; window.RS_IMP_APARTMENTS = window.RS_IMP_MAP_UNITS;</script>';
    ?>
    <script>
    (function(){
      var shapeSelector = '.imp-object,.imp-shape,.imp-object-poly,.imp-object-rect,.imp-object-ellipse';
      var objectSelector = '.imp-object,.imp-shape,.imp-object-poly,.imp-object-rect,.imp-object-ellipse';
      var listSelector = '.imp-object-list-item';
      var statusClasses = ['rs-imp-status-available','rs-imp-status-reserved','rs-imp-status-sold','rs-imp-status-unknown'];
      var hoveredCodes = new Set();
      var mapRootSelector = '.imp-wrap,.imp-main,.imp-canvas-wrap,.imp-ui,.imp-object-menu,.imp-object-list,.imp-tooltips-container';
      var logoSelector = '#brx-header,#brx-footer,header,footer,.logo-wrapper,.graphic-logo,.brxe-logo,.site-logo,[class*="logo"]';

      function n(v){return String(v||'').trim();}
      function allData(){return window.RS_IMP_MAP_UNITS||window.RS_IMP_APARTMENTS||{};}
      function isSoldHatch(el){return !!(el&&((el.getAttribute&&el.getAttribute('data-rs-sold-hatch')==='1')||(el.closest&&el.closest('[data-rs-sold-hatch="1"]'))));}
      function isShape(el){if(!el||!el.tagName||isSoldHatch(el))return false;var t=el.tagName.toLowerCase();return ['path','polygon','rect','ellipse','circle'].indexOf(t)>-1;}
      function shapes(el){if(!el)return[];if(isShape(el))return[el];return el.querySelectorAll?Array.prototype.slice.call(el.querySelectorAll('path,polygon,rect,ellipse,circle')).filter(function(shape){return !isSoldHatch(shape);}) :[];}
      function readCodeRaw(el){if(!el)return'';var a=['data-rs-map-unit-code','data-rs-apartment-code','data-title','data-name','data-object-title','data-object-name','aria-label','title'];for(var i=0;i<a.length;i++){var v=el.getAttribute&&el.getAttribute(a[i]);if(v)return n(v);}var title=el.querySelector&&el.querySelector('title');if(title&&title.textContent)return n(title.textContent);return n(el.textContent);}
      function normalizeHumanLabel(value){value=n(value);var m=value.match(/^byt\s*-?\s*(\d+)$/i);if(m){return 'B-'+String(parseInt(m[1],10)).padStart(2,'0');}m=value.match(/^apartm[aĂˇ]n\s*-?\s*([a-z])$/i);if(m){return 'AP-'+m[1].toUpperCase();}m=value.match(/^(?:parkovacie\s+miesto|parking|miesto)\s*-?\s*(\d+)$/i);if(m){return 'P'+String(parseInt(m[1],10)).padStart(2,'0');}return value;}
      function codeCandidates(value){value=normalizeHumanLabel(value);if(!value)return[];var out=[value];var m=value.match(/^([PG])\s*-?\s*(\d+)$/i);if(m){var num=String(parseInt(m[2],10));var padded=num.padStart(2,'0');out.push(padded,num,m[1].toUpperCase()+padded);}else if(/^\d+$/.test(value)){var raw=String(parseInt(value,10));var pad=raw.padStart(2,'0');out.push(raw,pad,'P'+pad,'G'+pad);}return out.filter(function(item,index){return item&&out.indexOf(item)===index;});}
      function resolveCode(value){var candidates=codeCandidates(value);if(!candidates.length)return'';var items=allData();for(var i=0;i<candidates.length;i++){if(items[candidates[i]])return candidates[i];}var lower=candidates.map(function(item){return item.toLowerCase();});for(var code in items){if(!Object.prototype.hasOwnProperty.call(items,code))continue;var item=items[code]||{};if(lower.indexOf(n(item.title).toLowerCase())>-1)return code;if(lower.indexOf(n(item.code).toLowerCase())>-1)return code;}return'';}
      function codeFromDescendant(el){if(!el||!el.querySelectorAll)return'';var nodes=el.querySelectorAll('[data-title],[data-name],[data-object-title],[data-object-name],[aria-label],[title],title');for(var i=0;i<nodes.length;i++){var c=resolveCode(readCodeRaw(nodes[i]));if(c)return c;}return'';}
      function codeFor(el){var c=resolveCode(readCodeRaw(el));if(c)return c;c=codeFromDescendant(el);if(c)return c;var p=el&&el.closest?el.closest(objectSelector):null;c=resolveCode(readCodeRaw(p));if(c)return c;return codeFromDescendant(p);}
      function codeForListItem(list){if(!list)return'';var c=resolveCode(list.getAttribute&&list.getAttribute('data-rs-map-unit-code'));if(c)return c;c=resolveCode(list.getAttribute&&list.getAttribute('data-rs-apartment-code'));if(c)return c;var label=list.querySelector&&list.querySelector('.rs-imp-list-label');c=resolveCode(label&&label.textContent);if(c)return c;return resolveCode(list.textContent)||codeFor(list);}
      function objectFor(el){return el&&el.closest?el.closest(objectSelector):null;}
      function svgFor(shape){if(!shape)return null;return shape.ownerSVGElement||(shape.tagName&&shape.tagName.toLowerCase()==='svg'?shape:null);}
      function clearNode(el){while(el&&el.firstChild){el.removeChild(el.firstChild);}}
      function svgDefs(svg){var ns='http://www.w3.org/2000/svg';var defs=svg.querySelector('defs')||document.createElementNS(ns,'defs');if(!defs.parentNode){svg.insertBefore(defs,svg.firstChild);}return defs;}
      function cssPaint(value,context){value=n(value);var m=value.match(/^var\((--[^),\s]+)/);if(m&&window.getComputedStyle){var doc=(context&&context.ownerDocument)||document;var source=(context&&context.nodeType===1)?context:doc.documentElement;var resolved=window.getComputedStyle(source).getPropertyValue(m[1]).trim()||window.getComputedStyle(doc.documentElement).getPropertyValue(m[1]).trim();if(resolved)return resolved;}return value;}
      function isLogoArea(el){return !!(el&&el.closest&&el.closest(logoSelector));}
      function logoMedia(root){var out=[];if(!root)return out;if(root.matches&&root.matches('img,svg'))out.push(root);if(root.querySelectorAll){out=out.concat(Array.prototype.slice.call(root.querySelectorAll('img,svg')));}return out.filter(function(item,index){return item&&out.indexOf(item)===index;});}
      function validLogoPaint(value){value=n(value);return value&&value!=='none'&&value!=='transparent'&&value!=='rgba(0, 0, 0, 0)'&&value!=='currentColor';}
      function preserveLogoPaint(el,prop){
        if(!el||!el.style)return;
        var key='data-rs-logo-'+prop;
        var saved=el.getAttribute(key);
        if(!saved){
          var inline=el.style.getPropertyValue(prop);
          var attr=el.getAttribute(prop);
          var computed=window.getComputedStyle?window.getComputedStyle(el).getPropertyValue(prop):'';
          saved=validLogoPaint(inline)?inline:(validLogoPaint(attr)?attr:(validLogoPaint(computed)?computed:''));
          if(saved){el.setAttribute(key,saved);}
        }
        if(saved){el.style.setProperty(prop,saved,'important');}
      }
      function protectLogoColors(root){
        var roots=[];
        if(root&&root.nodeType===1){roots=[root.closest&&root.closest(logoSelector)||root];}
        else{roots=Array.prototype.slice.call(document.querySelectorAll(logoSelector));}
        roots.forEach(function(item){
          logoMedia(item).forEach(function(media){media.style.setProperty('filter','none','important');media.style.setProperty('opacity','1','important');media.style.setProperty('mix-blend-mode','normal','important');});
          if(item.matches&&item.matches('svg')){preserveLogoPaint(item,'fill');preserveLogoPaint(item,'stroke');}
          var nodes=item.querySelectorAll?Array.prototype.slice.call(item.querySelectorAll('svg,path,polygon,rect,circle,ellipse,line,polyline')):[];
          nodes.forEach(function(node){preserveLogoPaint(node,'fill');preserveLogoPaint(node,'stroke');});
        });
      }
      function soldPattern(shape,item){
        if(!item||!item.colors)return'';
        return cssPaint(item.colors.stroke||item.colors.fill,shape);
      }
      function shapePathD(shape){
        if(!shape||!shape.tagName)return'';
        var t=shape.tagName.toLowerCase();
        if(t==='path')return shape.getAttribute('d')||'';
        if(t==='polygon'||t==='polyline'){
          var points=n(shape.getAttribute('points')).replace(/,/g,' ').trim().split(/\s+/);
          var out=[];
          for(var i=0;i+1<points.length;i+=2){out.push((i?'L':'M')+' '+points[i]+' '+points[i+1]);}
          if(t==='polygon'&&out.length){out.push('Z');}
          return out.join(' ');
        }
        if(t==='rect'){
          var x=parseFloat(shape.getAttribute('x')||0),y=parseFloat(shape.getAttribute('y')||0),w=parseFloat(shape.getAttribute('width')||0),h=parseFloat(shape.getAttribute('height')||0);
          return 'M '+x+' '+y+' H '+(x+w)+' V '+(y+h)+' H '+x+' Z';
        }
        return '';
      }
      function removeSoldOverlay(shape){
        var svg=svgFor(shape);var id=shape&&shape.getAttribute&&shape.getAttribute('data-rs-sold-hatch-id');
        if(!svg||!id)return;
        var overlay=svg.querySelector('[data-rs-sold-hatch-for="'+id+'"]');
        if(overlay&&overlay.parentNode){overlay.parentNode.removeChild(overlay);}
        var clip=svg.querySelector('#'+id+'-clip');
        if(clip&&clip.parentNode){clip.parentNode.removeChild(clip);}
        shape.removeAttribute('data-rs-sold-hatch-id');
      }
      function syncSoldOverlay(shape,item){
        var svg=svgFor(shape);var color=soldPattern(shape,item);if(!svg||!color)return;
        var id=shape.getAttribute('data-rs-sold-hatch-id');
        if(!id){id='rs-sold-hatch-'+Math.random().toString(36).slice(2);shape.setAttribute('data-rs-sold-hatch-id',id);}
        var ns='http://www.w3.org/2000/svg';
        var clipId=id+'-clip';
        var clip=svg.querySelector('#'+clipId);
        if(!clip){clip=document.createElementNS(ns,'clipPath');clip.setAttribute('id',clipId);clip.setAttribute('clipPathUnits','userSpaceOnUse');svgDefs(svg).appendChild(clip);}
        clip.setAttribute('data-rs-sold-hatch','1');
        clearNode(clip);
        var d=shapePathD(shape);
        if(!d)return;
        var clipShape=document.createElementNS(ns,'path');
        clipShape.setAttribute('data-rs-sold-hatch','1');
        clipShape.setAttribute('d',d);
        if(shape.getAttribute('transform')){clipShape.setAttribute('transform',shape.getAttribute('transform'));}
        if(shape.getAttribute('fill-rule')){clipShape.setAttribute('fill-rule',shape.getAttribute('fill-rule'));}
        clip.appendChild(clipShape);
        var overlay=svg.querySelector('[data-rs-sold-hatch-for="'+id+'"]');
        if(overlay&&overlay.tagName&&overlay.tagName.toLowerCase()!=='g'){overlay.parentNode&&overlay.parentNode.removeChild(overlay);overlay=null;}
        if(!overlay){
          overlay=document.createElementNS(ns,'g');
          overlay.setAttribute('data-rs-sold-hatch','1');
          overlay.setAttribute('data-rs-sold-hatch-for',id);
          overlay.setAttribute('pointer-events','none');
        }
        clearNode(overlay);
        overlay.setAttribute('clip-path','url(#'+clipId+')');
        overlay.setAttribute('fill','none');
        overlay.setAttribute('stroke','none');
        overlay.setAttribute('style','pointer-events:none;fill:none;stroke:none;stroke-width:0;stroke-dasharray:none;mix-blend-mode:normal;');
        overlay.setAttribute('visibility','visible');
        var bbox;try{bbox=shape.getBBox();}catch(e){bbox=null;}
        if(!bbox||!bbox.width||!bbox.height)return;
        var gap=12,stripeWidth=4,pad=Math.max(bbox.width,bbox.height)+30,cx=bbox.x+bbox.width/2,cy=bbox.y+bbox.height/2;
        for(var sx=bbox.x-pad;sx<=bbox.x+bbox.width+pad;sx+=gap){
          var stripe=document.createElementNS(ns,'rect');
          stripe.setAttribute('data-rs-sold-hatch','1');
          stripe.setAttribute('x',sx);
          stripe.setAttribute('y',bbox.y-pad);
          stripe.setAttribute('width',stripeWidth);
          stripe.setAttribute('height',bbox.height+pad*2);
          stripe.setAttribute('fill',color);
          stripe.setAttribute('fill-opacity','0.95');
          stripe.setAttribute('stroke','none');
          stripe.setAttribute('stroke-width','0');
          stripe.setAttribute('stroke-dasharray','none');
          stripe.setAttribute('shape-rendering','geometricPrecision');
          stripe.setAttribute('transform','rotate(45 '+cx+' '+cy+')');
          overlay.appendChild(stripe);
        }
        if(shape.parentNode&&overlay.parentNode!==shape.parentNode){shape.parentNode.appendChild(overlay);}
        else if(shape.parentNode&&overlay.nextSibling){shape.parentNode.appendChild(overlay);}
      }
      function setSoldOverlayVisible(shape,visible){
        var svg=svgFor(shape);var id=shape&&shape.getAttribute&&shape.getAttribute('data-rs-sold-hatch-id');
        if(!svg||!id)return;
        var overlay=svg.querySelector('[data-rs-sold-hatch-for="'+id+'"]');
        if(overlay){overlay.setAttribute('visibility',visible?'visible':'hidden');}
      }
      function setSoldOverlaysForCode(code,visible){
        if(!code)return;
        document.querySelectorAll(shapeSelector).forEach(function(el){
          if(codeFor(el)===code){shapes(el).forEach(function(shape){setSoldOverlayVisible(shape,visible);});}
        });
      }
      function hideSoldOverlaysForHover(code){
        setTimeout(function(){if(hoveredCodes.has(code)){setSoldOverlaysForCode(code,false);}},35);
      }
      function repaintAfterHover(code){
        setTimeout(function(){if(!hoveredCodes.has(code)){paintCode(code);}},0);
        setTimeout(function(){if(!hoveredCodes.has(code)){paintCode(code);}},90);
        setTimeout(function(){if(!hoveredCodes.has(code)){paintCode(code);}},240);
      }
      function markMapRoots(){
        document.querySelectorAll(shapeSelector).forEach(function(el){
          var root=el.closest&&el.closest(mapRootSelector);
          if(root){root.classList.add('rs-imp-map-scope');}
          var uiWrap=root&&root.closest&&root.closest('.imp-ui-wrap,.imp-wrap,.imp-main');
          if(uiWrap){uiWrap.classList.add('rs-imp-map-scope');}
        });
      }
      function applyDefaultShape(shape,item){
        if(!shape||!item||!item.status||!item.colors)return;
        statusClasses.forEach(function(className){shape.classList.remove(className);});
        shape.classList.add('rs-imp-status-'+item.status);
        shape.setAttribute('data-rs-apartment-status',item.status);
        shape.setAttribute('data-rs-map-unit-status',item.status);
        shape.setAttribute('data-rs-map-unit-type',item.type||'apartment');
        shape.setAttribute('fill',cssPaint(item.colors.fill,shape));
        shape.setAttribute('stroke',cssPaint(item.colors.stroke,shape));
        shape.setAttribute('fill-opacity',item.colors.fillOpacity);
        shape.setAttribute('stroke-opacity',item.colors.strokeOpacity);
        shape.setAttribute('stroke-width','2');
        if(item.status==='sold'){syncSoldOverlay(shape,item);}else{removeSoldOverlay(shape);}
      }
      function paintCode(code){
        var item=allData()[code];if(!item)return;
        document.querySelectorAll(shapeSelector).forEach(function(el){
          if(codeFor(el)===code){shapes(el).forEach(function(shape){applyDefaultShape(shape,item);});}
        });
      }
      function paintAll(){
        if(!window.RS_IMP_MAP_UNITS&&!window.RS_IMP_APARTMENTS)return;
        document.querySelectorAll(shapeSelector).forEach(function(el){var code=codeFor(el);if(code&&!hoveredCodes.has(code)){paintCode(code);}});
      }
      function addTarget(list,el){
        if(!el||list.indexOf(el)>-1)return;
        list.push(el);
      }
      function hoverTargets(code){
        var out=[];
        document.querySelectorAll(shapeSelector).forEach(function(el){
          if(codeFor(el)!==code)return;
          addTarget(out,el);
          addTarget(out,objectFor(el));
          shapes(el).forEach(function(shape){
            addTarget(out,shape);
            addTarget(out,objectFor(shape));
          });
        });
        return out;
      }
      function fireMouse(el,type){
        if(!el||!el.dispatchEvent)return;
        try{el.dispatchEvent(new MouseEvent(type,{bubbles:true,cancelable:true,view:window}));}catch(e){}
      }
      function firePointer(el,type){
        if(!el||!el.dispatchEvent||typeof PointerEvent==='undefined')return;
        try{el.dispatchEvent(new PointerEvent(type,{bubbles:true,cancelable:true,view:window,pointerType:'mouse'}));}catch(e){}
      }
      function nativeHover(code,active){
        var targets=hoverTargets(code);
        targets.forEach(function(target){
          if(active){
            firePointer(target,'pointerover');
            fireMouse(target,'mouseover');
            firePointer(target,'pointerenter');
            fireMouse(target,'mouseenter');
          }else{
            firePointer(target,'pointerout');
            fireMouse(target,'mouseout');
            firePointer(target,'pointerleave');
            fireMouse(target,'mouseleave');
          }
        });
      }
      function enterListHover(code){
        if(!code||hoveredCodes.has(code))return;
        hoveredCodes.add(code);
        nativeHover(code,true);
        hideSoldOverlaysForHover(code);
      }
      function leaveListHover(code){
        if(!code||!hoveredCodes.has(code))return;
        hoveredCodes.delete(code);
        nativeHover(code,false);
        repaintAfterHover(code);
      }
      function relatedListCode(e){
        var rt=e.relatedTarget;
        var list=rt&&rt.closest?rt.closest(listSelector):null;
        return list?codeForListItem(list):'';
      }
      function run(){markMapRoots();paintAll();setTimeout(function(){markMapRoots();paintAll();},300);setTimeout(function(){markMapRoots();paintAll();},900);setTimeout(function(){markMapRoots();paintAll();},1800);}
      function keepAlive(){setTimeout(paintAll,300);setTimeout(paintAll,1200);}

      document.addEventListener('DOMContentLoaded',run);
      window.addEventListener('load',run);
      protectLogoColors();
      ['pointerover','mouseover','pointerdown','click','focusin'].forEach(function(type){
        document.addEventListener(type,function(e){if(isLogoArea(e.target)){protectLogoColors(e.target);}},true);
      });
      window.setInterval(function(){if(window.RS_IMP_MAP_UNITS||window.RS_IMP_APARTMENTS){paintAll();}},2500);
      if(typeof MutationObserver!=='undefined'){
        var moTimer=null;
        new MutationObserver(function(){clearTimeout(moTimer);moTimer=setTimeout(keepAlive,120);}).observe(document.documentElement,{childList:true,subtree:true});
      }
      document.addEventListener('click',function(){setTimeout(paintAll,120);setTimeout(paintAll,600);});
      document.addEventListener('pointerover',function(e){
        if(isLogoArea(e.target))return;
        var list=e.target.closest&&e.target.closest(listSelector);
        if(list){enterListHover(codeForListItem(list));return;}
        var code=codeFor(objectFor(e.target)||e.target);
        if(code){hoveredCodes.add(code);hideSoldOverlaysForHover(code);}
      },true);
      document.addEventListener('pointerout',function(e){
        if(isLogoArea(e.target))return;
        var list=e.target.closest&&e.target.closest(listSelector);
        if(list){
          var code=codeForListItem(list);
          if(relatedListCode(e)===code)return;
          leaveListHover(code);
          return;
        }
        var code=codeFor(objectFor(e.target)||e.target);
        var rt=e.relatedTarget;
        var nextCode=rt&&rt.closest?codeFor(objectFor(rt)||rt):'';
        if(code&&code!==nextCode){
          hoveredCodes.delete(code);
          repaintAfterHover(code);
        }
      },true);
    })();
    </script>
    <?php
}, 20);