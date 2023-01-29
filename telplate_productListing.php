

<?php
 

$args = array(
    'post_type'=> 'create_product_list', 
    'order'    => 'ASC'
);              
$the_query = new WP_Query( $args );
if($the_query->have_posts() ) : 
    while ( $the_query->have_posts() ) : 
       $the_query->the_post(); 
	 
	   $id=get_the_id();
	   $product_title=get_the_title();
	   $product_price=get_post_meta( get_the_ID(), 'product_price', true );
	   $product_quantity=get_post_meta( get_the_ID(), 'product_quantity', true );
	   ?>
	   <input type="textbox" name="prod_qty" value=""/>
	   <input type="button" name="add_to_cart"value="Add to Cart"/>
       <?php
    endwhile; 
    wp_reset_postdata(); 
else: 
endif;


?>

