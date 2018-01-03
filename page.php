<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * To generate specific templates for your pages you can use:
 * /mytheme/views/page-mypage.twig
 * (which will still route through this PHP file)
 * OR
 * /mytheme/page-mypage.php
 * (in which case you'll want to duplicate this file and save to the above path)
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

/**
 * Category : Issue
 * Tag      : Campaign
 * Post     : Action
 */

use Timber\Timber;

/**
 * Add custom css class for body element hook.
 *
 * @param array $classes  Array of css classes passed by the hook.
 * @return array
 */
function add_body_classes_for_page( $classes ) {
	$classes[] = 'brown-bg';
	return $classes;
}
add_filter( 'body_class', 'add_body_classes_for_page' );

$context = Timber::get_context();
$post = new TimberPost();

$page_meta_data  = get_post_meta( $post->ID );
$temp_categories = get_the_category( $post->ID );
$category        = count( $temp_categories ) > 0 ? $temp_categories[0] : null;

// Handle navigation links.
if ( $category && ( $category->name !== $post->post_title ) ) {     // Do not add links inside the Issue page itself.
	// Get Issue.
	$issue = get_page_by_title( $category->name );                  // Category and Issue need to have the same name.
	if ( $issue ) {
		$context['issue'] = [
			'name' => $issue->post_title,
			'link' => get_permalink( $issue ),
		];
	}

	// Get Campaigns.
	$page_tags = wp_get_post_tags( $post->ID );
	$tags      = [];

	if ( is_array( $page_tags ) && $page_tags ) {
		foreach ( $page_tags as $page_tag ) {
			$tags[] = [
				'name' => $page_tag->name,
				'link' => get_tag_link( $page_tag ),
			];
		}
		$context['campaigns'] = $tags;
	}
}

$context['post']                = $post;
$context['header_title']        = $page_meta_data['p4_title'][0] ?? is_front_page() ? '' : $post->title;
$context['header_subtitle']     = $page_meta_data['p4_subtitle'][0] ?? '';
$context['header_description']  = $page_meta_data['p4_description'][0] ?? '';
$context['header_button_title'] = $page_meta_data['p4_button_title'][0] ?? '';
$context['header_button_link']  = $page_meta_data['p4_button_link'][0] ?? '';

$context['page_category']       = is_front_page() ? 'Front Page' : ( $category->name ?? 'Unknown page' );

$context['background_image']    = wp_get_attachment_url( get_post_meta( get_the_ID(), 'background_image_id', 1 ), 'medium' );

Timber::render( array( 'page-' . $post->post_name . '.twig', 'page.twig' ), $context );
