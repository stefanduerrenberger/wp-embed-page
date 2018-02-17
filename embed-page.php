<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/*
Plugin Name: Embed pages and posts in autoresizing iFrames
Plugin URI:  
Description: Generates code to embed any of your pages/post into another website
Version:     1.1
Author:      Stefan Dürrenberger
Author URI:  http://idm-studios.ch
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: embedpage
Domain Path: /languages
*/


if (is_admin()) {
} else {
    add_action( 'wp_enqueue_scripts', 'embedpage_enqueue_scripts' );
}


/**
 * Load translations
 */
function embedpage_load_plugin_textdomain() {
    load_plugin_textdomain( 'embedpage', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'embedpage_load_plugin_textdomain' );


/**
 * Add Javascript and Stylesheet
 */
function embedpage_enqueue_scripts() {
        wp_enqueue_script( 'embedpage-iframe',
            plugins_url( '/lib/iframeResizer/iframeResizer.contentWindow.min.js', __FILE__ )
        );
}


/**
 * Adds a meta box to page and post edit screens
 */
function embedpage_add_meta_box() {
    $screens = ['post', 'page'];
    foreach ($screens as $screen) {
        add_meta_box(
            'embedpage_code',           // Unique ID
            __('Code to embed this content', 'embedpage'),  // Box title
            'embedpage_meta_box_content',  // Content callback, must be of type callable
            $screen                   // Post type
        );
    }
}

add_action('add_meta_boxes', 'embedpage_add_meta_box');


/**
 * Generates the output for the page/post meta box
 */
function embedpage_meta_box_content($post) {
    if ($post->post_status == 'publish') {  
        echo '<p>' . __('Copy the code below to embed this page as an iFrame somewhere else. The iFrame will automatically resize to fit the content.', 'embedpage') . '</p>';
        ?> 
            <textarea id="embedpage-code" readonly style="width:100%;color:#333;font-family:monospace;font-size:13px;" rows="5">
<iframe style="min-height: 150px; border: 0;" src="<?php echo get_permalink($post->ID) ?>" width="100%" height="150" scrolling="yes"></iframe>
<script src="<?php echo plugins_url( 'lib/iframeResizer/iframeResizer.min.js', __FILE__ ) ?>" type="text/javascript"></script>
<script type="text/javascript">var iframes = iFrameResize({log:false, checkOrigin: false, heightCalculationMethod: 'bodyScroll'});</script>
</textarea>
            
            <script type="text/javascript">
            var textBox = document.getElementById("embedpage-code");
            // select all text in the textarea on focus
            textBox.onfocus = function() {
                textBox.select();

                textBox.onmouseup = function() {
                    textBox.onmouseup = null;
                    return false;
                };
            };
            </script>

        <?php
    }
    else {
        echo '<p><i>' . __('The code to embed this content is only available when the page/post is published.', 'embedpage') . '<i></p>';
    }
}

