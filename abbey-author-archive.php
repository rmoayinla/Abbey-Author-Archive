<?php
/*
* Plugin Name: Abbey Author Archive
* Description: Customize author archives and provide templates for author archive pages
* Author: Rabiu Mustapha
* Version: 0.1
* Text Domain: abbey-author-archive

*/
class Abbey_Author_Archive{

	public function __construct(){

		add_action( "init", array( $this, "add_custom_rewrite" ) ); 

		add_action ( "pre_get_posts", array( $this, "show_custom_posts" ) );

		/**
		 * hook to worpress plugin deactivation action hook 
		 * this hook is fired when plugin is de-activated
		 * wordpress rewrite rules are flushed when this plugin is deactivated
		 * as this plugin adds its own rewrite rules, it is important that the rewrite rules are reverted when this plugin is inactive 
		 */
		register_deactivation_hook( __FILE__, array( $this, 'flush_rewrite_rules' ) );

		/**
		 * hook to wordpress plugin activation action hook 
		 * this hook is fired when plugn is activated 
		 * wordpress rewrite rules are flushed when this plugin is activated 
		 * as this plugin adds rewrite rules, it is important that the rewrite rules are added to wp when activating the plugin
		 */
		register_activation_hook( __FILE__, array( $this, 'flush_rewrite_rules' ) );
	}

	function add_custom_rewrite(){
		/**
		 * Adds a new rewrite rule for wordpress 
		 * the rewrite rules is for showing author posts for specific post type 
		 * example: wordpress.com/author/rmoayinla/reviews - to show reviews by author rmoayinla 
		 * the rewrite rule also allow pagination e.g. wordpress.com/author/rmoayinla/reviews/page/2
		 * @since: 0.1 
		 */
		add_rewrite_rule(
        '(.+?)/author/([^/]*)/?(page/([0-9]{1,}))?/?$',
        'index.php?post_type=$matches[1]&author_name=$matches[2]&paged=$matches[4]',
        'top'
    	);
	}

	/**
	 * flush wordpress rewrite rules
	 * this function is run on plugin activation so that the plugin rewrite rules are saved in the database
	 * @uses: flush_rewrite_rules() 	wordpress native function to flushing rewrite 
	 * @since: 0.1 
	 */
	function flush_rewrite_rules(){
		flush_rewrite_rules();
	}

	function show_custom_posts( $query ){
		/**
		 * bail early if the current query is an admin page, single page or a single post type page 
		 */
		if( is_admin() || is_single() || is_singular() )
        	return;

    	/**
    	 * get all the registered post types, both the defaul wp post types i.e posts, pages, attachement
    	 * and all the custom post types the user might have registered 
    	 */
    	$post_types = get_post_types( array( 'public' =>  true ), 'names' );
    	
    	/**
    	 * check if the query is an archive and the post_type is empty 
    	 * if the check is true, set post_type to all registered post type
    	 * the default wordpress behaviour is to check in only 'posts' 
    	 * with this check, we check in all post_types
    	 */
    	if( ( $query->is_archive ) && ! get_query_var( "post_type" ))
       		$query->set( 'post_type', $post_types );

       	/**
       	 * check if the query is an author archive page
       	 * if the check is true, get all the author posts at once without pagination 
       	 */
       	if( $query->is_author )
       		$query->set( 'posts_per_page', -1 );
    	
    	/**
    	 * remove this action hook from 'pre_get_posts'
    	 */
    	remove_action( "pre_get_posts", array( $this, "show_custom_posts" ) );
	
	}

}

new Abbey_Author_Archive();