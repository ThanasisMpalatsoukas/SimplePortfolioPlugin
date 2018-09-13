<?php

/*
* @package portfolioPerfectPlugin
*/

/*
Plugin Name: Portfolio Perfect Plugin
Description: The assistance plugin to use the portfolioPerfect theme
Author: Thanasis Mpalatsoukas
Author URI: https://www.facebook.com/sakis.mpalatsoukas.5
Text Domain: plugin-helper
License: GPLv2 or later
Version: 1.1.0
*/

if(!defined('ABSPATH')){
  die;
}

if( ! function_exists('add_action') ){
  echo 'Silly human you cannot visit this , in this way';
  exit;
}

class portfolioPerfectPlugin
{

  function __construct(){
    add_action('init',array($this,'register_custom_taxonomies'));
    add_action('init',array($this,'register_custom_post_type'));
  }

  function activate(){
    $this->register_custom_post_type();
    $this->register_custom_taxonomies();
    $this->createPages();
    flush_rewrite_rules();
  }

  function deactivate(){
    flush_rewrite_rules();
  }

  function unistall(){
    /*

    */
  }

  function register_custom_taxonomies(){
    $labels = array(
      'name' => _x( 'Tags', 'taxonomy general name' ),
      'singular_name' => _x( 'Tag', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search Tags' ),
      'popular_items' => __( 'Popular Tags' ),
      'all_items' => __( 'All Tags' ),
      'parent_item' => null,
      'parent_item_colon' => null,
      'edit_item' => __( 'Edit Tag' ),
      'update_item' => __( 'Update Tag' ),
      'add_new_item' => __( 'Add New Tag' ),
      'new_item_name' => __( 'New Tag Name' ),
      'separate_items_with_commas' => __( 'Separate tags with commas' ),
      'add_or_remove_items' => __( 'Add or remove tags' ),
      'choose_from_most_used' => __( 'Choose from the most used tags' ),
      'menu_name' => __( 'Tags' ),
    );

    register_taxonomy('tag','blog',array(
      'hierarchical' => false,
      'labels' => $labels,
      'show_ui' => true,
      'update_count_callback' => '_update_post_term_count',
      'query_var' => true,
      'rewrite' => array( 'slug' => 'tag' ),
    ));

    register_taxonomy(
          'portfolio_categories',
          'portfolio',
          array(
              'label' => __( 'Category' ),
              'rewrite' => array( 'slug' => 'portfolio' ),
              'hierarchical' => true,
              'show_ui'           => true,
              'show_admin_column' => true,
              'query_var'         => true,
              'rewrite'           => array( 'slug' => 'categories' )
          )
      );
  }

  function register_custom_post_type(){
    $blog = get_option( 'content_blog' );

    if( $blog == 1 ){

      register_post_type( 'Blog',
        array(
          'labels' => array(
            'name' => __( 'Blog' ),
            'singular_name' => __( 'Blog' )
          ),
          'taxonomies'=>array('category'),
          'public' => true,
          'has_archive' => true,
          'supports' => array( 'title','post-formats','editor','thumbnail','widget','comments')
        )
      );

    }

		$contact = get_option( 'content_contact' );

		if( 'checked' == $contact ):

			register_post_type( 'messages',
				array(
					'labels' => array(
						'name' 					 => __( 'Messages' ),
						'singular_name'	 => __( 'Message' ),
						'Menu messages'	 => __( 'Messages' ),
						'Name admin bar' => __( 'Messages' )
					),
					'show_ui' 				=> true,
					'public'				  => true,
					'show_in_menu' 		=> true,
					'has_archive'		  => true,
					'capability_type' => 'post',
					'hierarchical' 		=> false,
					'menu_icon'				=> 'dashicons-email-alt',
					'menu_position'		=> 26,
					'supports' => array( 'title','editor','author' )
				)
			);

			add_filter( 'manage_messages_posts_columns' , 'simple_portfolio_set_messages_columns'	);
			add_action(	'manage_messages_posts_custom_column' , 'simple_portfolio_set_custom_columns' , 10 , 2);

			function simple_portfolio_set_custom_columns( $column , $post_id	) {

				switch( $column ){
					case 'message':
						echo get_the_excerpt();
						break;
					case  'Email':
						echo get_post_meta( $post_id , '_simple_email' , true );
					 	break;
				}

			}

			function simple_portfolio_set_messages_columns( $columns ) {
				$newColumn = array();
				$newColumn['title'] = 'Username';
				$newColumn['message'] = 'Message';
				$newColumn['Email'] = 'Email';
				$newColumn['date'] = 'date';
				return $newColumn;
			}

		endif;

    $testimonials = get_option( 'content_testimonials' );

    if( $testimonials == 1 ){

      register_post_type( 'Testimonials',
        array(
          'labels' => array(
            'name'             => __( 'Testimonials' ),
            'singular_name'    => __( 'Testimonials' )
          ),
          'public'             => true,
          'has_archive'        => true,
          'menu_icon'          => 'dashicons-heart'

        )
      );

      add_post_type_support( 'testimonials', 'thumbnail','comments' );

    }

    register_post_type( 'Portfolio',
      array(
        'labels' => array(
          'name'             => __( 'Portfolio' ),
          'singular_name'    => __( 'Portfolio' )
        ),
        'public'             => true,
        'has_archive'        => true,
        'menu_icon'          => 'dashicons-businessman',
        'supports' => array('title', 'page-attributes')

      )
    );

    $args = array('editor','title','thumbnail');
    add_post_type_support( 'portfolio', $args );


  }

  function createPages(){
    $this->createPage('portfolio',__('Work 1','text-domain'),'hello world');
    $this->createPage('portfolio',__('Work 2','text-domain'),'hello world');
    $this->createPage('blog',__('My best work','text-domain'),'One of my best works');
    $this->createPage('blog',__('My best work 2','text-domain'),'One of my best works');
    $this->createPage('blog',__('My best work 3','text-domain'),'One of my best works');
    $this->createPage('testimonials',__('Jack anderson','text-domain'),'Great developer to work with');
    $this->createPage('testimonials',__('Michael bay','text-domain'),'Great developer to work with indeed');
    $this->createPage('page',__('front-page','text-domain'),'');
  }

  function createPage($post_type,$page_title,$page_content){
    $new_page_title     = __('About Us','text-domain'); // Page's title
    $new_page_content   = '';                           // Content goes here
    $new_page_template  = 'page-custom-page.php';       // The template to use for the page
    $page_check = get_page_by_title($new_page_title);   // Check if the page already exists
    // Store the above data in an array
    $new_page = array(
            'post_type'     => $post_type,
            'post_title'    => $page_title,
            'post_content'  => $page_content,
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_slug'     => $page_title
    );
    // If the page doesn't already exist, create it
    if(!isset($page_check->ID)){
        $new_page_id = wp_insert_post($new_page);

        if($image_url):
        //  Generate_Featured_Image( $image_url ,   $new_page_id );
        endif;

        if(!empty($new_page_template)){
            update_post_meta($new_page_id, '_wp_page_template', $new_page_template);
        }
    }
  }


}

if(class_exists( 'portfolioPerfectPlugin' )):
  $portfolioPerfectPlugin = new portfolioPerfectPlugin();
endif;

//activation
register_activation_hook(__FILE__ , array($portfolioPerfectPlugin,'activate'));

//deactivation
register_activation_hook(__FILE__ , array($portfolioPerfectPlugin,'deactivate'));

//Uninstall
register_uninstall_hook(__FILE__, 'delete_everything');

function delete_everything(){
  $blogs = get_posts( array('post_type' => 'blog' , 'numberposts' => -1) );
  $portfolio = get_posts( array('post_type' => 'portfolio', 'numberposts' => -1) );
  $testimonials = get_posts( array('post_type' => 'testimonials', 'numberposts' => -1) );
	$messages = get_posts( array('post_type' => 'messages', 'numberposts' => -1) );

  deletePosts($blogs);
  deletePosts($portfolio);
  deletePosts($testimonials);
	deletePosts($messages);

  deleteTaxonomies('tag');
  deleteTaxonomies('portfolio_categories');

	delete_metadata( 'post', null , '_simple_email' , null , true );
  delete_metadata( 'post', null , '_background_image_value_key' , null , true );
  delete_metadata( 'post', null , '_choices_amount' , null , true );
}

function deletePosts($posts){
  foreach($posts as $post){
    wp_delete_post( $post->ID , true );
  }
}

function deleteTaxonomies($taxonomy){
  $terms = get_terms( $taxonomy, array( 'fields' => 'ids', 'hide_empty' => false ) );
          foreach ( $terms as $value ) {
               wp_delete_term( $value, 'category' );
          }
}
