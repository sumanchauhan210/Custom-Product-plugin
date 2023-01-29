<?php
/*
Plugin Name: Product Listing
Plugin URI: https://git-scm.com/book/en/v2/GitHub-Account-Setup-and-Configuration
Description: This plugin will small ecomerce plugin which will provide shipoing cart functionality
Version: 1.0
Author: Suman Chauhan
Author URI: https://git-scm.com/book/en/v2/GitHub-Account-Setup-and-Configuration
License: 123
*/

/*enque jquery  */
function james_adds_to_the_head() {
 
    wp_enqueue_script('jquery','https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js'); 
}
add_action( 'wp_enqueue_scripts', 'james_adds_to_the_head' );

/* enque admin ajax	*/
function my_enqueue() {
    wp_enqueue_script( 'ajax-script', get_template_directory_uri() . '/js/my-ajax-script.js', array('jquery') );
    wp_localize_script( 'ajax-script', 'my_ajax_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}
add_action( 'wp_enqueue_scripts', 'my_enqueue' );


/*	Intializa plugin for activation and deactivation and create CPT	*/
add_action( 'init', 'create_product_list' );

function create_product_list() {
    register_post_type( 'create_product_list',
        array(
            'labels' => array(
                'name' => 'Products',
                'singular_name' => 'Products',
                'add_new' => 'Add New Products',
                'add_new_item' => 'Add New Products',
                'edit' => 'Edit',
                'edit_item' => 'Edit Products',
                'new_item' => 'New Products',
                'view' => 'View',
                'view_item' => 'View Products',
                'search_items' => 'Search Products',
                'not_found' => 'No Products found',
                'not_found_in_trash' => 'No Products found in Trash',
                'parent' => 'Parent Products'
            ),
 
            'public' => true,
            'menu_position' => 15,
            'supports' => array( 'title', 'editor', 'comments', 'thumbnail', 'custom-fields' ),
            'taxonomies' => array( '' ), 
            'menu_icon' => "dashicons-products",
            'has_archive' => true
        )
    );
}

//hook into the init action and call create_book_taxonomies when it fires
 
add_action( 'init', 'create_product_taxonomy', 0 );
 
//create a custom taxonomy name it Product Category
 
function create_product_taxonomy() {
 
// Add new taxonomy, make it hierarchical like categories
//first do the translations part for GUI
 
  $labels = array(
    'name' => _x( 'Product Category', 'taxonomy general name' ),
    'singular_name' => _x( 'Product Category', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Product Category' ),
    'all_items' => __( 'All Product Category' ),
    'parent_item' => __( 'Parent Product Category' ),
    'parent_item_colon' => __( 'Parent Product Category:' ),
    'edit_item' => __( 'Edit Product Category' ), 
    'update_item' => __( 'Update Product Category' ),
    'add_new_item' => __( 'Add New Product Category' ),
    'new_item_name' => __( 'New Product Category Name' ),
    'menu_name' => __( 'Product Category' ),
  );    
 
// Now register the taxonomy
  register_taxonomy('product_category',array('create_product_list'), array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'show_in_rest' => true,
    'show_admin_column' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'prduct_category' ),
  ));
 
}

// Add custom fields to product posts
add_action( 'admin_init', 'my_admin' );

function my_admin() {
    add_meta_box( 'product_list_meta_box',
        'Product List Details',
        'display_product_list_meta_box',
        'create_product_list', 'normal', 'high'
    );
}

function display_product_list_meta_box( $product_list ) {
    // Retrieve current product price and product quantity
    $product_price = esc_html( get_post_meta( $product_list->ID, 'product_price', true ) );
    $product_quantity = esc_html( get_post_meta( $product_list->ID, 'product_quantity', true ) );
    ?>
    <table>
        <tr>
            <td style="width: 100%">Product Price</td>
            <td><input type="text" size="80" name="product_price" value="<?php echo $product_price; ?>" /></td>
        </tr>
        <tr>
            <td style="width: 150px">Product Quantity</td>
            <td>
               	<input type="text" size="80" name="product_quantity" value="<?php echo $product_quantity; ?>" /></td>
                    
                </select>
            </td>
        </tr>
    </table>
    <?php
}

add_action( 'save_post', 'add_products_fields', 10, 2 );

function add_products_fields( $product_id, $product_data ) {
    // Check post type for movie reviews
    if ( $product_data->post_type == 'create_product_list' ) {
        // Store data in post meta table if present in post data
        if ( isset( $_POST['product_price'] ) && $_POST['product_price'] != '' ) {
            update_post_meta( $product_id, 'product_price', $_POST['product_price'] );
        }
        if ( isset( $_POST['product_quantity'] ) && $_POST['product_quantity'] != '' ) {
            update_post_meta( $product_id, 'product_quantity', $_POST['product_quantity'] );
        }
    }
}

// Create shortcode to display all the product list
function my_product_list_shortcode() {
   ob_start();
   
   $args = array(
			'post_type'=> 'create_product_list', 
			'order'    => 'ASC'
		);              
		$the_query = new WP_Query( $args );?>
		
		<div>
		<?php if($the_query->have_posts() ) : 
			while ( $the_query->have_posts() ) : 
			   $the_query->the_post(); 
			 
			   $id=get_the_id();
			   $product_title=get_the_title();
			   $product_price=get_post_meta($id, 'product_price'); 
			   $product_quantity=get_post_meta($id, 'product_quantity' );
			   ?>
			   <div class="listing">
				   <h4 style="margin-bottom:0;margin-top:0" class="product_name" data-id="<?php echo $id; ?>"><b>Name : </b><?php echo $product_title; ?></h4>
				   <h4 style="margin-bottom:0;margin-top:0"><b>Price : </b><?php echo $product_price[0]; ?></h4>
				   <h4 style="margin-bottom:0;margin-top:0" class="stock">Stock:<b><?php echo $product_quantity[0]; ?></b></h4>
				   Add Quantity :<input type="textbox" name="prod_qty" value=""/>
				   <input type="button" name="add_to_cart"value="Add to Cart"/>
			   </div>
			   <?php
			endwhile; 
			wp_reset_postdata(); 
		else: 
		endif;?>
		<script>
		jQuery(document).ready(function($){
		  jQuery('input[name=prod_qty]').on('input', function() {
				currentqty=$(this).val();
				actual_qty=$(this).parent(".listing").find(".stock b").html();
				if(currentqty>actual_qty){
					alert("Sorry! we don't have that much stock available");
					$(this).val("");
				}
			});
			jQuery("input[name=add_to_cart]").click(function(){
				
				currentqty=$('input[name=prod_qty]').val();
				current_id=$(this).parent(".listing").find(".product_name").attr("data-id"); 
				jQuery.ajax({
				  type:'POST',
				  data:{action:'add_to_cart_items',data:currentqty,id:current_id},
				  url: my_ajax_object.ajax_url,
				  success: function(value) {
				  window.location.replace(value);  
				  }
				});
			});
			
		}); 
		</script>
		<div>
   <?php return ob_get_clean();   
} 
add_shortcode( 'product_list', 'my_product_list_shortcode' );

// Create shortcode to display all the product list
function my_cart_list_shortcode() {
   ob_start();
    $logged_user = wp_get_current_user(); // Get current user info
	$user_id=$logged_user->ID;
    $userEmailid= $logged_user->user_email;
	
	$productid=get_user_meta( $user_id, 'productID', true );	 
	return ob_get_clean();   
} 
add_shortcode( 'cart_list', 'my_cart_list_shortcode' );

/* Ajax call */
add_action("wp_ajax_add_to_cart_items", "add_to_cart_items");
add_action("wp_ajax_nopriv_add_to_cart_items", "add_to_cart_items");

function add_to_cart_items() {
	
   $productid = $_REQUEST["id"];
   $product_qty = $_REQUEST["data"];
   
   //Get current user details
    $logged_user = wp_get_current_user(); // Get current user info
	$user_id=$logged_user->ID;
    $userEmailid= $logged_user->user_email;
	if( is_user_logged_in() ) : 
		update_user_meta( $user_id,"productID", $productid);	
		update_user_meta( $user_id,"productQty",$product_qty);	
	
	endif;
	echo $url=home_url()."/cart";
	
	exit;
}
