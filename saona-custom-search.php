<?php

/*
Plugin Name: Saona custom search
Description: Conçue spécialement pour Saona par <a href="https://www.sn-ecommerce.fr/fr">SN E-Commerce</a> permet de faire des recherches de produits en fonction des catégories sur le site
Version: 0.0.0
Author: Luwee, citizendiop
Author URI: https://www.linkedin.com/in/louis-diatta-0378bab3/

*/

defined ('ABSPATH') or die ('Acces non autorisé');

// Register and load the widget
function scs_load_widget() {
    register_widget( 'scs_widget' );
}
add_action( 'widgets_init', 'scs_load_widget' );

// Creating the widget
class scs_widget extends WP_Widget {

	function __construct() {
	parent::__construct(

	// Base ID of widget
	'scs_widget',

	// Widget name will appear in UI
	__('SCS Widget', 'scs_widget_domain'),

	// Widget description
	array( 'description' => __( 'Saona custom search widget', 'scs_widget_domain' ), )
	);
	}

	// Creating widget front-end
	public function widget( $args, $instance ) {

		$taxonomy     = 'product_cat';
	  $orderby      = 'name';
	  $show_count   = 0;      // 1 for yes, 0 for no
	  $pad_counts   = 0;      // 1 for yes, 0 for no
	  $hierarchical = 1;      // 1 for yes, 0 for no
	  $title        = '';
	  $empty        = 0;
    $slugs        =  get_categories_slugs();

	  $args = array(
	         'taxonomy'     => $taxonomy,
	         'orderby'      => $orderby,
	         'show_count'   => $show_count,
	         'pad_counts'   => $pad_counts,
	         'hierarchical' => $hierarchical,
	         'title_li'     => $title,
	         'hide_empty'   => $empty,
           'slug'         => $slugs
	  );
		$categories = get_categories( $args );

		// Product categories select list
		echo '<div id="widget_container">';

		echo '<div id="scs_widget_container">';
		echo '<select id="scs_widget_select_list">';
		echo  '<option value=""> '; _e('Sélectionnez une catégorie', 'scs_widget_domain'); echo '</option>';
		foreach( $categories as $category ) {
		    echo  '<option data-catlink="'.get_term_link( $category->slug, 'product_cat' ).'" value="'.$category->slug.'"> ' . $category->name .' (' . $category->count . ') </option>';
		}
		echo '</select>';
		// category search results will be displayed here,
		// this select will be populated with all products  using ajax, @see get_all_products function
		echo '<select id="saona-custom-search-results">';
    echo '<option id="scs-select-product-option" value=""> '; _e('Sélectionnez un produit', 'scs_widget_domain'); echo '</option>';
		echo '</select>';
		echo '</div>';
		
		echo '<div id="scs-widget-search-button-wrap"> <button id="scs-widget-search-button" disabled>';  _e('Réserver', 'scs_widget_domain'); '</button> </div>';
		
		echo '</div>';
	}

	// Widget Backend
	public function form( $instance ) {

	}

	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {

	}
} // Class scs_widget ends here


// Ajax actions, javascript, css and stuff
add_action( 'wp_ajax_get_all_products', 'get_all_products' );
add_action( 'wp_ajax_nopriv_get_all_products', 'get_all_products' );
add_action( 'wp_enqueue_scripts', 'enqueue_scripts');

function enqueue_scripts() {
	wp_enqueue_script( 'scs_widget_script', plugins_url( '/js/scs_widget_script.js', __FILE__ ), array('jquery'));
	wp_localize_script( 'scs_widget_script', 'ajax_object',
	            array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );
	wp_enqueue_style( 'scs_widget_style', plugins_url( '/css/scs_widget_style.css', __FILE__ ));
}

// this function will be triggered using ajax and will return all products as json array
function get_all_products() {

    $all_products = array();
    $slugs        =  get_categories_slugs();

		$args = array(
        'post_type'      => 'product',
				'nopaging' => true
    );

    $loop = new WP_Query( $args );

    while ( $loop->have_posts() ) : $loop->the_post();
        global $product;
        $custom_product = array();
        $include_product = false;

        $terms = get_the_terms ( get_the_id(), 'product_cat');
        $categories = array();

        foreach($terms as $term) {
          if(in_array($term->slug, get_categories_slugs())) {
            array_push($categories, $term->slug);
            $include_product = true;
          }
        }

        if($include_product) {
          array_push($custom_product, get_the_title());
          array_push($custom_product, get_permalink());
          array_push($custom_product, $categories);
          array_push($all_products, $custom_product);
        }
    endwhile;

    wp_reset_query();

		$response['data'] = $all_products;
    wp_send_json($response);

}

// render only specified categories
function get_categories_slugs() {
  $slugs_fr = array( 'packs-vip','espaces-privatifs', 'massages-et-soins');
  $slugs_en = array( 'packs-vip-en','privates-spaces', 'massage-and-care');

  // check if wpml is installed and active
  if ( function_exists('icl_object_id') ) {
    if( 'en' == ICL_LANGUAGE_CODE ) {
      return $slugs_en;
    } else {
      return $slugs_fr;
    }
  } else { // if wpml is not active return in french
    return $slugs_fr;
  }
}

// Add shortcode to render widget
add_shortcode('scs_widget', 'render_scs_widget');
function render_scs_widget() {
  $scs_widget = new scs_widget();
  $args = array();
  $instance = array();
  $scs_widget->widget($args, $instance);
}
