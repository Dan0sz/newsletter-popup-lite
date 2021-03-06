<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * *
 * @package  : dan0sz/genesis-widget-area-halfway-post
 * @author   : Daan van den Bergh
 * @copyright: (c) 2020 Daan van den Bergh
 * @url      : https://daan.dev | https://woosh.dev
 * * * * * * * * * * * * * * * * * * * * * * * * * * */

class WidgetAreaHalfwayPost
{
    /** @var string $handle */
    private $handle = 'widget-area-halfway-post';

    /** @var string $plugin_text_domain */
    private $plugin_text_domain = 'widget-area-halfway-post';

    /**
     * NewsletterPopup constructor.
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Hook into required actions.
     */
    private function init()
    {
        add_action('widgets_init', [$this, 'register_sidebar'], 0, 100);
        add_action('wp', [$this, 'maybe_insert_sidebar'], 0, 200);
    }

    /**
     * Should we insert the widget area on this page?
     */
    public function maybe_insert_sidebar()
    {
        if (!is_admin()
            && is_single()
            && get_post_type() == 'post'
            && function_exists('genesis_get_theme_handle')) {
            wp_enqueue_style($this->handle, plugin_dir_url(WOOSH_WIDGET_AREA_HALFWAY_POST_PLUGIN_FILE) . 'assets/css/widget-area-halfway-post.min.css', [ genesis_get_theme_handle() ], WOOSH_WIDGET_AREA_HALFWAY_POST_STATIC_VERSION);
            add_filter('the_content', [$this, 'insert_sidebar']);
        }
    }

    /**
     * Register the widget area in WP Admin.
     */
    public function register_sidebar()
    {
        if (function_exists('genesis_register_widget_area')) {
            genesis_register_widget_area(
                [
                    'id'          => 'halfway-post',
                    'name'        => __('Halfway Post Content', $this->plugin_text_domain),
                    'description' => __('Insert widgets halfway the post\'s content.', $this->plugin_text_domain)
                ]
            );
        }
    }

    /**
     * @param $html
     *
     * @return string
     */
    public function insert_sidebar($html)
    {
        if (!function_exists('genesis_widget_area')) {
            return '';
        }

        $headers = preg_split('@(?=\<h2\>)@', $html);
        $middle  = (int) ceil(count($headers) / 2);

        ob_start();
        genesis_widget_area(
            'halfway-post',
            [
                'before' => '<div class="halfway-post widget-area">',
                'after'  => '</div>',
            ]
        );
        $sidebar = ob_get_contents();
        ob_end_clean();

        array_splice($headers, $middle, 0, $sidebar);

        return implode('', $headers);
    }
}
