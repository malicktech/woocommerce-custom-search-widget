<?php

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

// Base ID of your widget
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
  $show_count   = 1;      // 1 for yes, 0 for no
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

	echo '<select>';
	foreach( $categories as $category ) {
	    $category_link = sprintf(
	        '<a href="%1$s" alt="%2$s">%3$s</a>',
	        esc_url( get_category_link( $category->term_id ) ),
	        esc_attr( sprintf( __( 'View all posts in %s', 'textdomain' ), $category->name ) ),
	        esc_html( $category->name )
	    );

	    echo '<p>' . sprintf( esc_html__( 'Category: %s', 'textdomain' ), $category_link ) . '</p> ';
	    echo '<p>' . sprintf( esc_html__( 'Description: %s', 'textdomain' ), $category->description ) . '</p>';
	    echo '<p>' . sprintf( esc_html__( 'Post Count: %s', 'textdomain' ), $category->count ) . '</p>';
	}

	echo '</select>';
}


// Widget Backend
public function form( $instance ) {

}

// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {

}
} // Class scs_widget ends here
