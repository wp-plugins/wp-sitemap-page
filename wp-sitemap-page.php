<?php
/**
Plugin Name: WP Sitemap Page
Plugin URI: http://tonyarchambeau.com/
Description: Add a sitemap on any page/post using the simple shortcode [wp_sitemap_page]
Version: 1.0.5
Author: Tony Archambeau
Author URI: http://tonyarchambeau.com/
Text Domain: wp-sitemap-page
Domain Path: /languages

Copyright 2013 Tony Archambeau
*/


load_plugin_textdomain( 'wp_sitemap_page', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );



/***************************************************************
 * Define
 ***************************************************************/

if ( !defined('WSP_USER_NAME') )       define('WSP_USER_NAME', basename(dirname(__FILE__)) );
if ( !defined('WSP_USER_PLUGIN_DIR') ) define('WSP_USER_PLUGIN_DIR', WP_PLUGIN_DIR .'/'. WSP_USER_NAME );
if ( !defined('WSP_USER_PLUGIN_URL') ) define('WSP_USER_PLUGIN_URL', WP_PLUGIN_URL .'/'. WSP_USER_NAME );



/***************************************************************
 * Install and uninstall
 ***************************************************************/


/**
 * Hooks for install
 */
if (function_exists('register_uninstall_hook')) {
	register_deactivation_hook(__FILE__, 'wsp_uninstall');
}


/**
 * Hooks for uninstall
 */
if( function_exists('register_activation_hook')){
	register_activation_hook(__FILE__, 'wsp_install');
}


/**
 * Install this plugin
 */
function wsp_install() {
	// Initialise the RSS footer and save it
	$wsp_posts_by_category = '<a href="{permalink}">{title}</a>';
	add_option( 'wsp_posts_by_category', $wsp_posts_by_category );
}


/**
 * Uninstall this plugin
 */
function wsp_uninstall() {
	// Unregister an option
	delete_option( 'wsp_posts_by_category' );
	delete_option( 'wsp_exclude_pages' );
	unregister_setting('wp-sitemap-page', 'wsp_posts_by_category');
}



/***************************************************************
 * Menu + settings page
 ***************************************************************/


/**
 * Add menu on the Back-Office for the plugin
 */
function wsp_add_options_page() {
	$page_title = __('WP Sitemap Page', 'wp_sitemap_page');
	$menu_title = __('WP Sitemap Page', 'wp_sitemap_page');
	$capability = 'administrator';
	$menu_slug = 'wp_sitemap_page';
	$function = 'wsp_settings_page'; // function that contain the page
	add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function );
}
add_action('admin_menu', 'wsp_add_options_page');


/**
 * Add the settings page
 * 
 * @return boolean
 */
function wsp_settings_page() {
  $path = trailingslashit(dirname(__FILE__));
  
  if (!file_exists( $path . 'settings.php')) {
    return false;
  }
  require_once($path . 'settings.php');
}


/**
 * Additional links on the plugin page
 *
 * @param array $links
 * @param str $file
 */
function wsp_plugin_row_meta($links, $file) {
  if ($file == plugin_basename(__FILE__)) {
    $settings_page = 'wp_sitemap_page';
    $links[] = '<a href="options-general.php?page=' . $settings_page .'">' . __('Settings','wp_sitemap_page') . '</a>';
    $links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=FQKK22PPR3EJE&lc=GB&item_name=WP%20Sitemap%20Page&item_number=wp%2dsitemap%2dpage&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted">'.__('Donate', 'wp_sitemap_page').'</a>';
  }
  return $links;
}
add_filter('plugin_row_meta', 'wsp_plugin_row_meta',10,2);



/**
 * Manage the option when we submit the form
 */
function wsp_save_settings() {
	register_setting( 'wp-sitemap-page', 'wsp_posts_by_category' ); 
	register_setting( 'wp-sitemap-page', 'wsp_exclude_pages' ); 
} 
add_action( 'admin_init', 'wsp_save_settings' );



/***************************************************************
 * Manage the option
 ***************************************************************/

/**
 * Fonction de callback
 * 
 * @param $matches
 */
function wsp_manage_option( array $matches = array() )
{
	global $the_post_id;
	
	if (isset($matches[1])) {
		$key = strtolower( $matches[1] );
		
		switch ($key) {
			// Get the title of the post
			case 'title':
				return get_the_title($the_post_id);
				break;
			
			// Get the URL of the post
			case 'permalink':
				return get_permalink($the_post_id);
				break;
			
			// Get the year of the post
			case 'year':
				return get_the_time('Y', $the_post_id);
				break;
			
			// Get the month of the post
			case 'monthnum':
				return get_the_time('m', $the_post_id);
				break;
			
			// Get the day of the post
			case 'day':
				return get_the_time('d', $the_post_id);
				break;
			
			// Get the day of the post
			case 'hour':
				return get_the_time('H', $the_post_id);
				break;
			
			// Get the day of the post
			case 'minute':
				return get_the_time('i', $the_post_id);
				break;
			
			// Get the day of the post
			case 'second':
				return get_the_time('s', $the_post_id);
				break;
			
			// Get the day of the post
			case 'post_id':
				return $the_post_id;
				break;
			
			// Get the day of the post
			case 'category':
				$categorie_info = get_the_category($the_post_id);
				if (!empty($categorie_info)) {
					$categorie_info = current($categorie_info);
					//return print_r($categorie_info,1);
					return (isset($categorie_info->name) ? $categorie_info->name : '');
				}
				return '';
				break;
			
			// default value
			default:
				if (isset($matches[0])) {
					return $matches[0];
				}
				return false;
				break;
		}
		
	}
	return false;
}



/***************************************************************
 * Generate the sitemap
 ***************************************************************/


/**
 * Shortcode function that generate the shortcode
 * 
 * @param $atts
 * @param $content
 */
function wsp_wp_sitemap_page_func( $atts, $content=null ) 
{
	// init
	$return = '';
	
	// Exclude some pages
	$wsp_exclude_pages = trim(get_option('wsp_exclude_pages'));
	
	// define the way the pages should be displayed
	$args = array();
	$args['title_li'] = '';
	$args['echo']     = '0';
	
	// exclude some pages ?
	if (!empty($wsp_exclude_pages)) {
		$args['exclude'] = $wsp_exclude_pages;
	}
	
	// List the pages
	$list_pages = wp_list_pages($args);
	if (!empty($list_pages)) {
		$return .= '<h2 class="wsp-pages-title">'.__('Pages', 'wp_sitemap_page').'</h2>';
		$return .= '<ul class="wsp-pages-list">';
		$return .= $list_pages;
		$return .= '</ul>';
	}
	
	// List the posts by category
	$cats = get_categories();
	if (!empty($cats)) {
		$return .= '<h2 class="wsp-posts-list">'.__('Posts by category', 'wp_sitemap_page').'</h2>';
		
		// Get the categories
		$cats = wsp_generateMultiArray($cats);
		$return .= wsp_htmlFromMultiArray($cats);
	}
	
	return $return;
}
add_shortcode( 'wp_sitemap_page', 'wsp_wp_sitemap_page_func' );


/**
 * Generate a multidimensional array from a simple linear array using a recursive function
 * 
 * @param array $arr
 * @param int $parent
 */
function wsp_generateMultiArray( array $arr = array() , $parent = 0 )
{
	// check if not empty
	if (empty($arr)) {
		return array();
	}
	
	$pages = array();
	// go through the array
	foreach($arr as $k => $page) {
		if ($page->parent == $parent) {
			$page->sub = isset($page->sub) ? $page->sub : wsp_generateMultiArray($arr, $page->cat_ID);
			$pages[] = $page;
		}
	}
	
	return $pages;
}


/**
 * Display the multidimensional array using a recursive function
 * 
 * @param array $nav
 * @param bool $useUL
 */
function wsp_htmlFromMultiArray( array $nav = array() , $useUL = true )
{
	// check if not empty
	if (empty($nav)) {
		return '';
	}
	
	$html = '';
	if ($useUL === true) {
		$html .= '<ul class="wsp-posts-list">'."\n";
	}
	
	foreach($nav as $page) {
		$html .= "\t".'<li><strong class="wsp-category-title">'
			.sprintf( __('Category: %1$s', 'wp_sitemap_page'), '<a href="'.get_category_link($page->cat_ID).'">'.$page->name.'</a>' )
			.'</strong>'."\n";
		
		$post_by_cat = wsp_displayPostByCat($page->cat_ID);
		
		// List of posts for this category
		$category_recursive = '';
		if (!empty($page->sub)) {
			// Use recursive function to get the childs categories
			$category_recursive = wsp_htmlFromMultiArray($page->sub, false);
		}
		
		// display if it exist
		if ( !empty($post_by_cat) || !empty($category_recursive) ) {
			$html .= '<ul class="wsp-posts-list">';
		}
		if ( !empty($post_by_cat) ) {
			$html .= $post_by_cat;
		}
		if ( !empty($category_recursive) ) {
			$html .= $category_recursive;
		}
		if ( !empty($post_by_cat) || !empty($category_recursive) ) {
			$html .= '</ul>';
		}
		
		$html .= '</li>'."\n";
	}
	
	if ($useUL === true) {
		$html .= '</ul>'."\n";
	}
	return $html;
}


/**
 * Display the multidimensional array using a recursive function
 * 
 * @param int $cat_id
 */
function wsp_displayPostByCat($cat_id)
{
	global $the_post_id;
	
	// init
	$html = '';
	
	// List of posts for this category
	$the_posts = get_posts('numberposts=9999&cat='.$cat_id);
	
	// check if not empty
	if (empty($the_posts)) {
		return '';
	}
	
	// determine the code to place in the textarea
	$wsp_posts_by_category = get_option('wsp_posts_by_category');
	if ( $wsp_posts_by_category === false ) {
		// this option does not exists
		$wsp_posts_by_category = __('<a href="{permalink}">{title}</a> ({monthnum}/{day}/{year})', 'wp_sitemap_page');
		
		// save this option
		add_option( 'wsp_posts_by_category', $wsp_posts_by_category );
	}
	
	foreach ($the_posts as $the_post) {
		// Display the line of a post
		$get_category = get_the_category($the_post->ID);
		
		// Display the post only if it is on the deepest category
		if ($get_category[0]->cat_ID == $cat_id) {
			
			// get post ID
			$the_post_id = $the_post->ID;
			
			// replace the ID by the real value
			$html .= "\t\t".'<li class="wsp-post">'
				.preg_replace_callback( '#\{(.*)\}#Ui', 'wsp_manage_option', $wsp_posts_by_category)
				.'</li>'."\n";
		}
	}
	
	return $html;
}
