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

		register_deactivation_hook( __FILE__, array( $this, 'flush_rewrite_rules' ) );
		register_activation_hook( __FILE__, array( $this, 'flush_rewrite_rules' ) );
	}

	function add_custom_rewrite(){
		add_rewrite_rule(
        '(.+?)/author/([^/]*)/?(page/([0-9]{1,}))?/?$',
        'index.php?post_type=$matches[1]&author_name=$matches[2]&paged=$matches[4]',
        'top'
    	);
	}

	function flush_rewrite_rules(){
		flush_rewrite_rules();
	}

	function show_custom_posts( $query ){
		if( is_admin() )
        	return;
    	$post_types = get_post_types( array( 'public' =>  true ), 'names' );
    	if( ( $query->is_author || $query->is_category ) && ! get_query_var( "post_type" ))
       		$query->set( 'post_type', $post_types );

       	if( $query->is_author )
       		$query->set( 'posts_per_page', -1 );
    	
    	remove_action( "pre_get_posts", array( $this, "show_custom_posts" ) );
	
	}

}

new Abbey_Author_Archive();