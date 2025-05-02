<?php
function yourtheme_enqueue_scripts() {
    // Enqueue Google Fonts
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&family=Rancho&display=swap', array(), null);
    
    // Enqueue theme's main stylesheet
    wp_enqueue_style('yourtheme-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'yourtheme_enqueue_scripts');