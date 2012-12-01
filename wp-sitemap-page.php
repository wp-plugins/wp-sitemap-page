<?php
/**

Plugin Name: WP Sitemap Page
Plugin URI: http://tonyarchambeau.com/
Description: Add a sitemap on any page/post using the simple shortcode [wp_sitemap_page]
Version: 1.0.3
Author: Tony Archambeau
Author URI: http://tonyarchambeau.com/
Text Domain: wp-sitemap-page
Domain Path: /languages

Copyright 2012 Tony Archambeau
*/


load_plugin_textdomain( 'wp_sitemap_page', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


/**
 * Shortcode function that generate the shortcode
 * 
 * @param $atts
 * @param $content
 */
function wsp_wp_sitemap_page_func( $atts, $content=null )
{
	$return = '';
	
	// List the pages
	$return .= '<h2 class="wsp-pages-title">'.__('Pages', 'wp_sitemap_page').'</h2>';
	$return .= '<ul class="wsp-pages-list">';
	$return .= wp_list_pages('title_li=&echo=0');
	$return .= '</ul>';
	
	// List the posts by category
	$return .= '<h2 class="wsp-posts-list">'.__('Posts by category', 'wp_sitemap_page').'</h2>';
	
	// Get the categories
	$cats = get_categories();
	$cats = wsp_generateMultiArray($cats);
	
	$return .= wsp_htmlFromMultiArray($cats);
	
	return $return;
}
add_shortcode( 'wp_sitemap_page', 'wsp_wp_sitemap_page_func' );


/**
 * Generate a multidimensional array from a simple linear array using a recursive function
 * 
 * @param $arr
 * @param $parent
 */
function wsp_generateMultiArray($arr, $parent = 0)
{
	$pages = Array();
	foreach($arr as $k => $page) {
		if($page->parent == $parent) {
			$page->sub = isset($page->sub) ? $page->sub : wsp_generateMultiArray($arr, $page->cat_ID);
			$pages[] = $page;
		}
	}
	return $pages;
}


/**
 * Display the multidimensional array using a recursive function
 * 
 * @param $cat_id
 */
function wsp_displayPostByCat($cat_id)
{
	$html = '';
	
	// List of posts for this category
	$the_posts = get_posts('numberposts=999&cat='.$cat_id);
	foreach ($the_posts as $the_post) {
		// Display the line of a post
		$get_category = get_the_category($the_post->ID);
		
		// Display the post only if it is on the deepest category
		if ($get_category[0]->cat_ID == $cat_id) {
			// Get the date of the post
			$date_fragments = explode('-', substr_replace($the_post->post_date, '', 10));
			$the_date = $date_fragments[2].'/'.$date_fragments[1].'/'.$date_fragments[0];
			
			$html .= "\t\t".'<li class="wsp-post"><a href="'.get_permalink($the_post->ID).'">'.$the_post->post_title.'</a> ('.$the_date.')</li>'."\n";
		}
	}
	
	return $html;
}


/**
 * Display the multidimensional array using a recursive function
 * 
 * @param $nav
 * @param $useUL
 */
function wsp_htmlFromMultiArray($nav, $useUL = true)
{
	$html = '';
	if ($useUL) {
		$html .= '<ul>'."\n";
	}
	
	foreach($nav as $page) {
		$html .= "\t".'<li><strong class="wsp-category-title">'.__('Category:', 'wp_sitemap_page').' <a href="'.get_category_link($page->cat_ID).'">'.$page->name.'</a></strong>'."\n";
		
		$post_by_cat = wsp_displayPostByCat($page->cat_ID);
		
		// List of posts for this category
		$category_recursive = '';
		if (!empty($page->sub)) {
			// Use recursive function to get the childs categories
			$category_recursive = wsp_htmlFromMultiArray($page->sub, false);
		}
		
		// display if it exist
		if ($post_by_cat != '' || $category_recursive!= '') {
			$html .= '<ul>';
		}
		if ($post_by_cat != '') {
			$html .= $post_by_cat;
		}
		if ($category_recursive != '') {
			$html .= $category_recursive;
		}
		if ($post_by_cat != '' || $category_recursive!= '') {
			$html .= '</ul>';
		}
		
		$html .= '</li>'."\n";
	}
	
	if ($useUL) {
		$html .= '</ul>'."\n";
	}
	return $html;
}
