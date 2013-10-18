<div class="wrap">
	
	<div id="icon-options-general" class="icon32"></div>
	<h2><?php _e('WP Sitemap Page', 'wp_sitemap_page'); ?></h2>
	
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<!-- main content -->
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<div class="postbox">
						<h3><span><?php _e('Settings', 'wp_sitemap_page'); ?></span></h3>
						<div class="inside">

	<p><?php _e('Please choose how you want to display the posts on the sitemap.', 'wp_sitemap_page');?></p>
	<ul>
		<li><?php echo sprintf( __('%1$s: title of the post.', 'wp_sitemap_page'), '<strong>{title}</strong>' );?></li>
		<li><?php echo sprintf( __('%1$s: URL of the post.', 'wp_sitemap_page'), '<strong>{permalink}</strong>' );?></li>
		<li><?php echo sprintf( __('%1$s: The year of the post, four digits, for example 2004.', 'wp_sitemap_page'), '<strong>{year}</strong>' );?></li>
		<li><?php echo sprintf( __('%1$s: Month of the year, for example 05.', 'wp_sitemap_page'), '<strong>{monthnum}</strong>' );?></li>
		<li><?php echo sprintf( __('%1$s: Day of the month, for example 28.', 'wp_sitemap_page'), '<strong>{day}</strong>' );?></li>
		<li><?php echo sprintf( __('%1$s: Hour of the day, for example 15.', 'wp_sitemap_page'), '<strong>{hour}</strong>' );?></li>
		<li><?php echo sprintf( __('%1$s: Minute of the hour, for example 43.', 'wp_sitemap_page'), '<strong>{minute}</strong>' );?></li>
		<li><?php echo sprintf( __('%1$s: Second of the minute, for example 33.', 'wp_sitemap_page'), '<strong>{second}</strong>' );?></li>
		<li><?php echo sprintf( __('%1$s: The unique ID # of the post, for example 423.', 'wp_sitemap_page'), '<strong>{post_id}</strong>' );?></li>
		<li><?php echo sprintf( __('%1$s: Category name. Nested sub-categories appear as nested directories in the URI.', 'wp_sitemap_page'), '<strong>{category}</strong>' );?></li>
	</ul>
	
	<form method="post" action="options.php">
		<?php settings_fields('wp-sitemap-page');?>
		<table class="form-table">
			<tbody>
			<tr valign="top">
				<th scope="row">
					<label for="wsp_posts_by_category">
					<?php _e('How to display the posts', 'wp_sitemap_page');?>
					</label>
				</th>
				<td>
					<?php
					// determine the code to place in the textarea
					$wsp_posts_by_category = get_option('wsp_posts_by_category');
					if ( $wsp_posts_by_category === false ) {
						// this option does not exists
						$wsp_posts_by_category = '<a href="{permalink}">{title}</a>';
						
						// save this option
						add_option( 'wsp_posts_by_category', $textarea );
					} else {
						// this option exists, display it in the textarea
						$textarea = $wsp_posts_by_category;
					}
					?>
					<textarea name="wsp_posts_by_category" id="wsp_posts_by_category" 
						rows="2" cols="50"
						class="large-text code"><?php
						echo $textarea;
						?></textarea>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="wsp_exclude_pages">
					<?php _e('Exclude pages', 'wp_sitemap_page'); ?>
					</label>
				</th>
				<td>
					<?php
					// Exclude some pages
					$wsp_exclude_pages = get_option('wsp_exclude_pages');
					?>
					<input type="text" class="large-text code" 
						name="wsp_exclude_pages" id="wsp_exclude_pages" 
						value="<?php echo $wsp_exclude_pages; ?>" />
					<p class="description"><?php _e('Just add the IDs, separated by a comma, of the pages you want to exclude.', 'wp_sitemap_page'); ?></p>
				</td>
			</tr>
			</tbody>
		</table>
		<?php
		// idea to evolve : button to restaure initial code
		?>
		<?php submit_button();?>
	</form>

						</div><!-- .inside -->
					</div><!-- .postbox -->
				</div><!-- .meta-box-sortables .ui-sortable -->
			</div><!-- post-body-content -->
			<!-- sidebar -->
			<div id="postbox-container-1" class="postbox-container">
				<div class="meta-box-sortables">
					<div class="postbox">
					<h3><span><?php _e('About', 'wp_sitemap_page'); ?></span></h3>
					<div style="padding:0 5px;">
						<?php
						$fr_lang = array('fr_FR', 'fr_BE', 'fr_CH', 'fr_LU', 'fr_CA');
						$is_fr = (in_array(WPLANG, $fr_lang) ? true : false);
						// Get the URL author depending on the language
						$url_author = ( $is_fr===true ? 'http://tonyarchambeau.com/' : 'http://en.tonyarchambeau.com/' );
						?>
						<p><?php _e('To display the sitemap, just use [wp_sitemap_page] on any page or post.', 'wp_sitemap_page'); ?></p>
						<p><?php printf(__('Plugin developed by <a href="%1$s">Tony Archambeau</a>.', 'wp_sitemap_page'), $url_author); ?></p>
						<?php
						$url_paypal = 'https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=FQKK22PPR3EJE&lc=GB&item_name=WP%20Sitemap%20Page&item_number=wp%2dsitemap%2dpage&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted';
						?>
						<p><a href="<?php echo $url_paypal; ?>"><?php _e('Donate', 'wp_sitemap_page'); ?></a></p>
					</div>
					</div><!-- .postbox -->
				</div><!-- .meta-box-sortables -->
			</div><!-- #postbox-container-1 .postbox-container -->
		</div><!-- #post-body .metabox-holder .columns-2 -->
		<br class="clear" />
	</div><!-- #poststuff -->
</div><!-- .wrap -->
