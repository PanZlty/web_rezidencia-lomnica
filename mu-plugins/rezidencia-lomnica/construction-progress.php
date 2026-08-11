<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('rs_termin_vystavby_css')) {
    function rs_termin_vystavby_css() {
        return <<<'CSS'
.rs-construction-progress-wrap,
.brxe-shortcode > strong > .rs-construction-progress-wrap,
.brxe-shortcode:has(> .construction-progress),
.brxe-shortcode:has(> strong > .construction-progress),
.brxe-shortcode > strong:has(> .construction-progress) {
  display: block;
  width: 100%;
  font-weight: inherit;
}

.construction-progress,
.construction-progress * {
  box-sizing: border-box;
}

.construction-progress {
  width: min(100%, 86rem);
  margin: 0 auto;
  padding: clamp(2.2rem, 3vw, 3rem);
  border-radius: var(--radius-l, 2rem);
  background: var(--white, #ffffff);
  border: 1px solid rgba(255, 255, 255, 0.28);
  color: var(--heading, #202a2f);
  box-shadow: 0 2.4rem 6rem rgba(0, 0, 0, 0.16);
  font-family: inherit;
  font-weight: 400;
  text-align: left;
}

.construction-progress__top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: clamp(1.6rem, 3vw, 3rem);
  margin-bottom: 2rem;
}

.construction-progress__content {
  min-width: 0;
}

.construction-progress__title {
  color: var(--heading, #202a2f);
  font-family: "Cormorant Garamond", serif;
  font-size: clamp(2.6rem, 3vw, 4rem);
  line-height: 1.05;
  font-weight: 700;
}

.construction-progress__label {
  margin-top: 0.7rem;
  color: var(--text, #596469);
  font-size: var(--text-s, 1.5rem);
  line-height: 1.45;
  font-weight: 500;
}

.construction-progress__days {
  flex: 0 0 auto;
  min-width: 10.8rem;
  padding: 0;
  border-radius: 0;
  background: transparent;
  color: var(--primary, #4e707a);
  text-align: right;
  white-space: nowrap;
}

.construction-progress__days strong {
  display: block;
  color: inherit;
  font-size: clamp(3rem, 3.6vw, 4.6rem);
  line-height: 0.9;
  font-weight: 800;
}

.construction-progress__days span {
  display: block;
  margin-top: 0.45rem;
  color: var(--muted, #7d8788);
  font-size: 1.25rem;
  line-height: 1.2;
  font-weight: 600;
}

.construction-progress__bar {
  display: block;
  width: 100%;
  height: 1.2rem;
  border-radius: 999px;
  overflow: hidden;
  background: rgba(21, 38, 33, 0.1);
}

.construction-progress__fill {
  display: block;
  height: 100%;
  min-width: 0.8rem;
  border-radius: inherit;
  background: linear-gradient(90deg, var(--tertiary-d-2, #7f4637), var(--tertiary, #b86b50));
  transition: width 0.6s ease;
}

.construction-progress__bottom {
  display: flex;
  justify-content: space-between;
  gap: 1.5rem;
  margin-top: 1.2rem;
  color: var(--muted, #7d8788);
  font-size: 1.3rem;
  line-height: 1.35;
  font-weight: 600;
}

.construction-progress__bottom-label,
.construction-progress__bottom-value {
  display: inline;
}

@media (max-width: 767px) {
  .construction-progress {
    padding: 2rem;
    border-radius: 1.8rem;
    text-align: center;
  }

  .construction-progress__top {
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
  }

  .construction-progress__days {
    width: 100%;
    text-align: center;
  }

  .construction-progress__bottom {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.45rem 1rem;
    text-align: left;
  }

  .construction-progress__bottom > span:nth-child(2) {
    grid-column: 1 / -1;
    grid-row: 1;
    text-align: center;
  }

  .construction-progress__bottom > span:nth-child(1),
  .construction-progress__bottom > span:nth-child(3) {
    grid-row: 2;
    text-align: center;
  }

  .construction-progress__bottom-label,
  .construction-progress__bottom-value {
    display: block;
  }
}
CSS;
    }
}

if (!function_exists('rs_output_termin_vystavby_styles')) {
    function rs_output_termin_vystavby_styles() {
        if (is_admin()) {
            return;
        }

        echo '<style id="rs-termin-vystavby-styles">' . rs_termin_vystavby_css() . '</style>';
    }
}

add_action('wp_head', 'rs_output_termin_vystavby_styles', 30);

if (!function_exists('rs_render_termin_vystavby_shortcode')) {
    function rs_render_termin_vystavby_shortcode($atts) {
        $atts = shortcode_atts([
            'start' => '2025-01-01',
            'end'   => '2026-12-31',
            'title' => 'TermĂ­n vĂ˝stavby',
            'label' => 'PredpokladanĂ© ukonÄŤenie vĂ˝stavby a odovzdanie bytov',
        ], $atts, 'termin_vystavby');

        $atts['label'] = 'PredpokladanĂ© ukonÄŤenie vĂ˝stavby a odovzdanie bytov';

        $start_raw = sanitize_text_field((string) $atts['start']);
        $end_raw   = sanitize_text_field((string) $atts['end']);

        $start = strtotime($start_raw);
        $end   = strtotime($end_raw);
        $today = strtotime(date('Y-m-d', current_time('timestamp')));

        if (!$start || !$end || $end <= $start) {
            return '';
        }

        $total_days  = max(1, (int) round(($end - $start) / DAY_IN_SECONDS));
        $passed_days = max(0, min($total_days, (int) round(($today - $start) / DAY_IN_SECONDS)));
        $days_left   = max(0, (int) round(($end - $today) / DAY_IN_SECONDS));
        $progress    = max(0, min(100, (int) round(($passed_days / $total_days) * 100)));

        $start_formatted = mb_strtolower(date_i18n('F Y', $start), 'UTF-8');
        $end_formatted   = mb_strtolower(date_i18n('F Y', $end), 'UTF-8');

        ob_start();
        ?>
        <div class="rs-construction-progress-wrap">
        <div class="construction-progress" data-progress="<?php echo esc_attr($progress); ?>">
            <div class="construction-progress__top">
                <div class="construction-progress__content">
                    <div class="construction-progress__title">
                        <?php echo esc_html($atts['title']); ?>
                    </div>

                    <div class="construction-progress__label">
                        <?php echo esc_html($atts['label']); ?>: <?php echo esc_html($end_formatted); ?>
                    </div>
                </div>

                <div class="construction-progress__days">
                    <?php if ($days_left > 0) : ?>
                        <strong><?php echo esc_html($days_left); ?></strong>
                        <span>dnĂ­ zostĂˇva</span>
                    <?php else : ?>
                        <strong>0</strong>
                        <span>termĂ­n dosiahnutĂ˝</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="construction-progress__bar" aria-label="Priebeh vĂ˝stavby <?php echo esc_attr($progress); ?> %">
                <div class="construction-progress__fill" style="width: <?php echo esc_attr($progress); ?>%;"></div>
            </div>

            <div class="construction-progress__bottom">
                <span><span class="construction-progress__bottom-label">ZaÄŤiatok:</span> <span class="construction-progress__bottom-value"><?php echo esc_html($start_formatted); ?></span></span>
                <span><?php echo esc_html($progress); ?> %</span>
                <span><span class="construction-progress__bottom-label">UkonÄŤenie:</span> <span class="construction-progress__bottom-value"><?php echo esc_html($end_formatted); ?></span></span>
            </div>
        </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

add_shortcode('termin_vystavby', 'rs_render_termin_vystavby_shortcode');