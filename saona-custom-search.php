<?php

/*
Plugin Name: Saona custom search
Description: Conçue spécialement pour Saona par <a href="https://www.sn-ecommerce.fr/fr">SN E-Commerce</a> permet de faire des recherches de produits en fonction des catégories sur le site
Version: 0.0.0
Author: Luwee
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

	  $args = array(
	         'taxonomy'     => $taxonomy,
	         'orderby'      => $orderby,
	         'show_count'   => $show_count,
	         'pad_counts'   => $pad_counts,
	         'hierarchical' => $hierarchical,
	         'title_li'     => $title,
	         'hide_empty'   => $empty
	  );
		$categories = get_categories( $args );

		// Woo commerce product categories select list
		echo '<select id="scs_widget_select_list">';
		echo  '<option value=""> '; _e('Select a category', 'scs_widget_domain'); echo '</option>';
		foreach( $categories as $category ) {
		    echo  '<option value="'.$category->slug.'"> ' . $category->name .' (' . $category->count . ') </option>';
		}
		echo '</select>';

		// category search results will be displayed here,
		// this select will be populated with products related to the selected category using ajax
		// see get_products_by_category function
		echo '<select id="saona-custom-search-results" class="form-control">';
            get_products_by_category();
		echo '</select>';

	}

	// Widget Backend
	public function form( $instance ) {

	}

	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {

	}
} // Class scs_widget ends here


// Ajax actions, javascript, css and stuff
add_action( 'wp_ajax_get_products_by_category', 'get_products_by_category' );
add_action( 'wp_enqueue_scripts', 'enqueue_scripts');

function enqueue_scripts() {
	wp_enqueue_script( 'scs_widget_script', plugins_url( '/js/scs_widget_script.js', __FILE__ ), array('jquery'));
	wp_localize_script( 'scs_widget_script', 'ajax_object',
	            array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );
	wp_enqueue_style( 'scs_widget_style', plugins_url( '/css/scs_widget_style.css', __FILE__ ));
}

// this function will be triggered using ajax and will populate products select list
function get_products_by_category() {

	if(isset($_POST['category'])) {
		$category = $_POST['category'];
		$search_by_category = true;
	} else {
		$search_by_category = false;
	}

	if($search_by_category) {
		$args = array(
        'post_type'      => 'product',
				'nopaging' => true,
        'product_cat'    => $category
    );
	} else {
		$args = array(
        'post_type'      => 'product',
				'nopaging' => true
    );
	}

		$output = '';
		$output .= '<option value=""> Produits';
		$output .= '</option>';

    $loop = new WP_Query( $args );
    while ( $loop->have_posts() ) : $loop->the_post();
        global $product;
        $output .= '<option value="'.get_permalink().'">' .get_the_title(). ' </option>';
    endwhile;

    wp_reset_query();

		if($search_by_category) {
			$response['data'] = $output;
	    wp_send_json($response);
		} else {
			echo $output;
		}
}

// Add shortcode to render widget
add_shortcode('scs_widget', 'render_scs_widget');
function render_scs_widget() {
  $scs_widget = new scs_widget();
  $scs_widget->widget();
}
