<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * Theme Customizer Settings
 *
 * @package WordlandBaseline
 */

// Sanitize checkboxes
function baseline_sanitize_checkbox($checked) {
	return (isset($checked) && $checked === true) ? true : false;
}

/**
 * Sets up customize options
 */
function baseline_customize_register($wp_customize) {

	// Section: Theme Options
	$wp_customize->add_section('baseline_options', [
		'title'    => __('Baseline Theme Options', 'baseline'),
		'priority' => 30,
	]);

	// ADD SETTINGS

	// Setting: Show Comments
	$wp_customize->add_setting('baseline_show_comments', [
		'default'           => false,
		'sanitize_callback' => 'baseline_sanitize_checkbox',
	]);

	// Setting: Disable Pagination
	$wp_customize->add_setting('baseline_disable_pagination', [
		'default'           => true,
		'sanitize_callback' => 'baseline_sanitize_checkbox',
	]);

	// Settings: Jetpack Features
	$wp_customize->add_setting('baseline_disable_sharing', [
		'default'           => true,
		'sanitize_callback' => 'baseline_sanitize_checkbox',
	]);

	$wp_customize->add_setting('baseline_disable_likes', [
		'default'           => true,
		'sanitize_callback' => 'baseline_sanitize_checkbox',
	]);

	$wp_customize->add_setting('baseline_disable_related', [
		'default'           => true,
		'sanitize_callback' => 'baseline_sanitize_checkbox',
	]);

	$wp_customize->add_setting('baseline_show_date_headlines', [
		'default'           => false,
		'sanitize_callback' => 'baseline_sanitize_checkbox',
	]);

	// ADD CONTROLS

	$wp_customize->add_control('baseline_show_comments', [
		'label'    => __('Show Comments on Posts', 'baseline'),
		'section'  => 'baseline_options',
		'type'     => 'checkbox',
	]);

	// Controls: Jetpack Features
	$wp_customize->add_control('baseline_disable_sharing', [
		'label'    => __('Disable Jetpack Sharing Buttons', 'baseline'),
		'section'  => 'baseline_options',
		'type'     => 'checkbox',
	]);

	$wp_customize->add_control('baseline_disable_likes', [
		'label'    => __('Disable Jetpack Likes', 'baseline'),
		'section'  => 'baseline_options',
		'type'     => 'checkbox',
	]);

	$wp_customize->add_control('baseline_disable_related', [
		'label'    => __('Disable Jetpack Related Posts', 'baseline'),
		'section'  => 'baseline_options',
		'type'     => 'checkbox',
	]);

	// Control: Disable Pagination
	$wp_customize->add_control('baseline_disable_pagination', [
		'label'    => __('Disable Pagination on Home Page', 'baseline'),
		'section'  => 'baseline_options',
		'type'     => 'checkbox',
	]);

	$wp_customize->add_control('baseline_show_date_headlines', [
		'label'    => __('Show Date Headlines on Home Page', 'baseline'),
		'section'  => 'baseline_options',
		'type'     => 'checkbox',
		'description' => __('Show date headlines and group posts by publication date on the home page.', 'baseline'),
	]);
}
add_action('customize_register', 'baseline_customize_register');

// Add customizer options for home page template
add_action('customize_register', function ($wp_customize) {
	// Add a section for our template options
	$wp_customize->add_section('linkblog_template_options', array(
		'title'    => __('Linkblog Template Options', 'linkblog-importer'),
		'priority' => 120,
	));

	// Add setting for home page template choice
	$wp_customize->add_setting('linkblog_home_template', array(
		'default'           => 'default',
		'sanitize_callback' => 'sanitize_key',
		'transport'         => 'refresh',
	));

	// Add control for the setting
	$wp_customize->add_control('linkblog_home_template', array(
		'label'    => __('Home Page Template', 'linkblog-importer'),
		'section'  => 'linkblog_template_options',
		'type'     => 'radio',
		'choices'  => array(
			'default'  => __('Default (index.php)', 'linkblog-importer'),
			'linkblog' => __('Linkblog Template (category-linkblog.php)', 'linkblog-importer'),
		),
	));
});

// Filter the home template based on customizer setting
add_filter('home_template', function ($template) {
	$home_template = get_theme_mod('linkblog_home_template', 'default');

	if ($home_template === 'linkblog') {
		$linkblog_template = locate_template('category-linkblog.php');
		if ($linkblog_template) {
			return $linkblog_template;
		}
	}

	return $template;
});
