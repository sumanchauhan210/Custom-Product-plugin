<?php
 /*Template Name: Product Listing
 */
 
get_header(); ?>
<div id="primary">
    <div id="content" role="main">
    <?php
    $mypost = array( 'post_type' => 'create_product_list', );
	print_r($mypost); exit;
    $loop = new WP_Query( $mypost );
    ?>
    <?php while ( $loop->have_posts() ) : $loop->the_post();?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
 
                <!-- Display featured image in right-aligned floating div -->
                <div style="float: right; margin: 10px">
                    <?php the_post_thumbnail( array( 100, 100 ) ); ?>
                </div>
 
                <!-- Display Title and Author Name -->
                <strong>Title: </strong><?php the_title(); ?><br />
                <strong>product Price: </strong>
                <?php echo esc_html( get_post_meta( get_the_ID(), 'product_price', true ) ); ?>
                <br />
 
                <!-- Display yellow stars based on rating -->
                <strong>Product Quantity: </strong>
                <?php
                $product_qty = esc_html( get_post_meta( get_the_ID(), 'product_quanitity', true ) );
                 echo $product_qty; ?>
                
            </header>
 
            <!-- Display movie review contents -->
            <div class="entry-content"><?php the_content(); ?></div>
        </article>
 
    <?php endwhile; ?>
    </div>
</div>
<?php wp_reset_query(); ?>
<?php get_footer(); ?>