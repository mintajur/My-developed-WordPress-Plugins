<?php
/**
 * Plugin Name: My Image Slider
 * Description: This plugin creates a customizable image slider for WordPress.
 * Version: 1.0.0
 * Author: Md. Mintajur Rahman Emon
 * Author URI: Your Website
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: my-image-slider
 */

 
 // Enqueue CSS and JavaScript files
function my_image_slider_enqueue_scripts() {
    // Enqueue CSS file
    wp_enqueue_style('my-image-slider-style', plugins_url('css/style.css', __FILE__));

    // Enqueue JavaScript file
    wp_enqueue_script('my-image-slider-script', plugins_url('js/script.js', __FILE__), array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'my_image_slider_enqueue_scripts');

// Add shortcode to display image slider
function my_image_slider_shortcode() {
    ob_start();
    ?>
    <div class="my-image-slider">
        <div class="slides">
            <div class="slide">
                <img src="<?php echo plugins_url('images/image1.png', __FILE__); ?>" alt="Slide 1">
            </div>
            <div class="slide">
                <img src="<?php echo plugins_url('images/image2.png', __FILE__); ?>" alt="Slide 2">
            </div>
            <div class="slide">
                <img src="<?php echo plugins_url('images/image3.png', __FILE__); ?>" alt="Slide 3">
            </div>
        </div>
        <div class="navigation">
        <button class="prev"><i class="fas fa-chevron-left"></i></button>
        <button class="next"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('my_image_slider', 'my_image_slider_shortcode');