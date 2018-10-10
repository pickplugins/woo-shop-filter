<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access





function WCShopFilter_pre_get_posts_query( $query_args ) {

    //var_dump($q);

    $WCShopFilter = isset($_GET['WCShopFilter']) ? sanitize_text_field($_GET['WCShopFilter']) :"";


    if($WCShopFilter):

        $keyword = isset($_GET['keyword']) ? sanitize_text_field($_GET['keyword']) :"";
        $_product_cat = isset($_GET['_product_cat']) ? stripslashes_deep($_GET['_product_cat']) :"";
        $_product_tag = isset($_GET['_product_tag']) ? sanitize_text_field($_GET['_product_tag']) :"";
        $_price = isset($_GET['_price']) ? sanitize_text_field($_GET['_price']) :"";

        $_order = isset($_GET['_order']) ? sanitize_text_field($_GET['_order']) :"";
        $_orderby = isset($_GET['_orderby']) ? sanitize_text_field($_GET['_orderby']) :"";
        $_onsale = isset($_GET['_onsale']) ? sanitize_text_field($_GET['_onsale']) :"";
        $_stock_status = isset($_GET['_stock_status']) ? sanitize_text_field($_GET['_stock_status']) :"";
        $_sku = isset($_GET['_sku']) ? sanitize_text_field($_GET['_sku']) :"";


        if($keyword){
            $query_args->set( 's', $keyword );
        }

        if($_product_cat){

            $tax_query = (array) $query_args->get( 'tax_query' );

            //var_dump($_product_cat);


            $tax_query[] = array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $_product_cat,
                'operator' => 'IN'
            );

            $query_args->set( 'tax_query', $tax_query );
        }


        if($_product_tag){

            $tax_query = (array) $query_args->get( 'tax_query' );

            $_product_tag = explode(',', $_product_tag);


            $tax_query_tag = array();
            $tax_query_tag['relation'] = 'OR';

            if($_product_tag)
            foreach ($_product_tag as $tag){

                $tax_query_tag[] = array(
                    'taxonomy' => 'product_tag',
                    'field' => 'name',
                    'terms' => $tag,
                    //'operator' => 'IN'
                );
            }

            $tax_query = array($tax_query_tag);

            //var_dump($tax_query_tag);
            //var_dump($tax_query);

            $query_args->set( 'tax_query', $tax_query );
        }





        if($_price){

            $meta_query = (array) $query_args->get( 'meta_query' );

            $_price_array = explode('-', $_price);
            $_price_max = (int)isset($_price_array[0]) ? $_price_array[0]: 0;
            $_price_min = (int)isset($_price_array[1]) ? $_price_array[1]: 0;


            $meta_query[] =  array(
                //'relation' => 'AND',
                array(
                    'key' => '_price',
                    'value' => array($_price_max,$_price_min ),
                    'compare' => 'BETWEEN',
                    'type' => 'NUMERIC'
                ),

            );

            //echo var_export($meta_query, true);

            $query_args->set( 'meta_query', $meta_query );
        }










        if($_order){
            $query_args->set( 'order', $_order );
        }

        if($_orderby){
            $query_args->set( 'orderby', $_orderby );
        }

        if($_onsale){

            $meta_query = (array) $query_args->get( 'meta_query' );

            $meta_query[] =  array(
                'relation' => 'OR',
                array( // Variable products type
                    'key'           => '_min_variation_sale_price',
                    'value'         => 0,
                    'compare'       => '>',
                    'type'          => 'numeric'
                ),
                array(
                    'key' => '_sale_price',
                    'value' => 0,
                    'compare' => '>',
                    'type' => 'NUMERIC'
                )
            );


            $query_args->set( 'meta_query', $meta_query );
        }


        if($_stock_status){

            $tax_query = (array) $query_args->get( 'tax_query' );

            $tax_query[] = array(
                'taxonomy' => 'product_visibility',
                'field' => 'name',
                'terms' => 'outofstock',
                'operator' => 'NOT IN',
            );


            $query_args->set( 'tax_query', $tax_query );
        }

        if($_sku){

            $meta_query = (array) $query_args->get( 'meta_query' );
            $meta_query[] =
                array(
                    'key' => '_sku',
                    'value' => $_sku,
                    'compare' => 'IN'
                );

            $query_args->set( 'meta_query', $meta_query );
        }







    endif;



}
add_action( 'woocommerce_product_query', 'WCShopFilter_pre_get_posts_query',99 );





















//add_action('woocommerce_before_shop_loop', 'woocommerce_before_shop_loop_WCShopFilter');

function woocommerce_before_shop_loop_WCShopFilter(){

    echo do_shortcode('[WCShopFilter]');
}





add_shortcode('WCShopFilter','woocommerce_before_shop_loop_search');


function woocommerce_before_shop_loop_search($atts, $content = null){

    $atts = shortcode_atts(
        array(
            'id' => "",

        ), $atts);

    $html = '';
    $post_id = $atts['id'];



    ?>





    <div class="WCShopFilter sidebar">

        <form action="" method="get">

            <input type="hidden" name="WCShopFilter" value="Y">

            <?php
            do_action('WCShopFilter_fields');
            ?>

            <div class="field-wrapper">
                <input type="submit" value="Submit">
            </div>
        </form>

    </div>
    <?php
}



add_action('WCShopFilter_fields','WCShopFilter_field_keyword',10);
function WCShopFilter_field_keyword(){

    $WCShopFilter = isset($_GET['WCShopFilter']) ? sanitize_text_field($_GET['WCShopFilter']) :"";
    $keyword = isset($_GET['keyword']) ? sanitize_text_field($_GET['keyword']) :"";

    if(!$WCShopFilter):
        $keyword = '';
    endif;


    ?>
    <div class="field-wrapper">
        <div class="label-wrapper">
            <label class=""><?php echo __('Keyword','wc-shop-filter'); ?></label>
        </div>
        <div class="input-wrapper">
            <input type="search" placeholder="<?php echo __('Type Keyword','wc-shop-filter'); ?>" name="keyword" value="<?php echo $keyword; ?>">
        </div>
    </div>
    <?php
}


add_action('WCShopFilter_fields','WCShopFilter_field_categories',20);
function WCShopFilter_field_categories(){

    $WCShopFilter = isset($_GET['WCShopFilter']) ? sanitize_text_field($_GET['WCShopFilter']) :"";
    $_product_cat = isset($_GET['_product_cat']) ? stripslashes_deep($_GET['_product_cat']) : array();

    if(!$WCShopFilter):
        $_product_cat = array();
    endif;


    $product_cats = get_terms( array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
    ) );

    ?>
    <div class="field-wrapper">
        <div class="label-wrapper">
            <label class=""><?php echo __('Categories','wc-shop-filter'); ?></label>
        </div>
        <div class="input-wrapper">
            <select name="_product_cat[]" multiple >
                <?php

                if(!empty($product_cats)):
                    foreach ($product_cats as $product_cat){

                        $term_id = $product_cat->term_id;
                        $name = $product_cat->name;
                        $count = $product_cat->count;
                        $slug = $product_cat->slug;
                        ?>
                        <option value="<?php echo $slug; ?>" <?php if(in_array($slug, $_product_cat)) echo 'selected';?> ><?php echo $name; ?>(<?php echo $count; ?>)</option>
                        <?php

                    }
                endif;
                ?>

            </select>
        </div>
    </div>
    <?php
}



add_action('WCShopFilter_fields','WCShopFilter_field_tags',20);
function WCShopFilter_field_tags(){

    $WCShopFilter = isset($_GET['WCShopFilter']) ? sanitize_text_field($_GET['WCShopFilter']) :"";
    $_product_tag = isset($_GET['_product_tag']) ? sanitize_text_field($_GET['_product_tag']) :"";

    if(!$WCShopFilter):
        $_product_tag = '';
    endif;


    ?>
    <div class="field-wrapper">
        <div class="label-wrapper">
            <label class=""><?php echo __('Tags','wc-shop-filter'); ?></label>
        </div>
        <div class="input-wrapper">

            <input placeholder="Tag 1, Tag 2" type="search" name="_product_tag" value="<?php echo $_product_tag; ?>">
        </div>
    </div>
    <?php
}












add_action('WCShopFilter_fields','WCShopFilter_field_price_range',20);
function WCShopFilter_field_price_range(){

    $WCShopFilter = isset($_GET['WCShopFilter']) ? sanitize_text_field($_GET['WCShopFilter']) :"";
    $_price = isset($_GET['_price']) ? sanitize_text_field($_GET['_price']) :"";

    if(!$WCShopFilter):
        $_price = '';
    endif;

    if(!empty($_price)){

        $_price_array = explode('-', $_price);
        $_price_min = isset($_price_array[0]) ? $_price_array[0]: 0;
        $_price_max = isset($_price_array[1]) ? $_price_array[1]: 0;
    }
    else{
        $_price_max = 500;
        $_price_min = 0;
    }


    $store_max_price = 500;
    $store_min_price = 0;

    $woocommerce_currency_symbol = get_woocommerce_currency_symbol();

    ?>


    <div class="field-wrapper">
        <div class="label-wrapper">
            <label class=""><?php echo __('Price range','wc-shop-filter'); ?></label>
        </div>
        <div class="input-wrapper">
            <input type="hidden" id="price_range" name="_price" value="<?php echo $_price; ?>"></input>
            <div id="price_range_slider"></div>

            <div class="price_range_display"><?php echo $woocommerce_currency_symbol.$_price_max; ?> - <?php echo $woocommerce_currency_symbol.$_price_min?></div>


        </div>
    </div>


    <script>
        jQuery(document).ready(function($) {

            woocommerce_currency = "<?php echo $woocommerce_currency_symbol?>";

            $( "#price_range_slider" ).slider({
                range: true,
                min: <?php echo $store_min_price; ?>,
                max: <?php echo $store_max_price; ?>,
                values: [ <?php echo $_price_min; ?>, <?php echo $_price_max?> ],
                slide: function( event, ui ) {
                    $( "#price_range" ).val( ui.values[ 0 ] + "-" + ui.values[ 1 ] );

                    $('.price_range_display').html(woocommerce_currency+ui.values[ 0 ]+'- '+woocommerce_currency+ui.values[ 1 ]);
                }
            });
        })


    </script>
    <?php
}


add_action('WCShopFilter_fields','WCShopFilter_field_order',20);
function WCShopFilter_field_order(){

    $WCShopFilter = isset($_GET['WCShopFilter']) ? sanitize_text_field($_GET['WCShopFilter']) :"";
    $_order = isset($_GET['_order']) ? sanitize_text_field($_GET['_order']) :"";

    if(!$WCShopFilter):
        $_order = '';
    endif;

    ?>
    <div class="field-wrapper">
        <div class="label-wrapper">
            <label class=""><?php echo __('Order','wc-shop-filter'); ?></label>
        </div>
        <div class="input-wrapper">
            <select name="_order" >
                <option value="DESC" <?php if($_order == 'DESC') echo 'selected';?>><?php echo __('DESC','wc-shop-filter'); ?></option>
                <option value="ASC" <?php if($_order == 'ASC') echo 'selected';?>><?php echo __('ASC','wc-shop-filter'); ?></option>


            </select>
        </div>
    </div>
    <?php
}


add_action('WCShopFilter_fields','WCShopFilter_field_orderby',20);
function WCShopFilter_field_orderby(){

    $WCShopFilter = isset($_GET['WCShopFilter']) ? sanitize_text_field($_GET['WCShopFilter']) :"";
    $_orderby = isset($_GET['_orderby']) ? sanitize_text_field($_GET['_orderby']) :"";

    if(!$WCShopFilter):
        $_orderby = '';
    endif;

    ?>
    <div class="field-wrapper">
        <div class="label-wrapper">
            <label class=""><?php echo __('Orderby','wc-shop-filter'); ?></label>
        </div>
        <div class="input-wrapper">
            <select name="_orderby" >
                <option value="price" <?php if($_orderby == 'price') echo 'selected';?>>Price</option>
                <option value="date" <?php if($_orderby == 'date') echo 'selected';?>>Date</option>
                <option value="rating" <?php if($_orderby == 'rating') echo 'selected';?>>Rating</option>
                <option value="popularity" <?php if($_orderby == 'popularity') echo 'selected';?>>Popularity</option>

            </select>
        </div>
    </div>
    <?php
}



add_action('WCShopFilter_fields','WCShopFilter_field_onsale',20);
function WCShopFilter_field_onsale(){

    $WCShopFilter = isset($_GET['WCShopFilter']) ? sanitize_text_field($_GET['WCShopFilter']) :"";
    $_onsale = isset($_GET['_onsale']) ? sanitize_text_field($_GET['_onsale']) :"";

    if(!$WCShopFilter):
        $_onsale = '';
    endif;

    ?>
    <div class="field-wrapper">

        <div class="input-wrapper">

            <label><input type="checkbox" name="_onsale" <?php if($_onsale == '1') echo 'checked';?> value="1">Display Onsale</label>
        </div>
    </div>
    <?php
}


add_action('WCShopFilter_fields','WCShopFilter_field_in_stock',20);
function WCShopFilter_field_in_stock(){


    $WCShopFilter = isset($_GET['WCShopFilter']) ? sanitize_text_field($_GET['WCShopFilter']) :"";
    $_stock_status = isset($_GET['_stock_status']) ? sanitize_text_field($_GET['_stock_status']) :"";

    if(!$WCShopFilter):
        $_stock_status = '';
    endif;

    ?>
    <div class="field-wrapper">

        <div class="input-wrapper">
            <label><input type="checkbox" name="_stock_status" <?php if($_stock_status == '1') echo 'checked';?> value="1">In stock</label>

        </div>
    </div>
    <?php
}





add_action('WCShopFilter_fields','WCShopFilter_field_sku',30);
function WCShopFilter_field_sku(){

    $WCShopFilter = isset($_GET['WCShopFilter']) ? sanitize_text_field($_GET['WCShopFilter']) :"";
    $_sku = isset($_GET['_sku']) ? sanitize_text_field($_GET['_sku']) :"";

    if(!$WCShopFilter):
        $_sku = '';
    endif;


    ?>
    <div class="field-wrapper">
        <div class="label-wrapper">
            <label class=""><?php echo __('SKU','wc-shop-filter'); ?></label>
        </div>
        <div class="input-wrapper">
            <input type="search" name="_sku" value="<?php echo $_sku; ?>">
        </div>
    </div>
    <?php
}












