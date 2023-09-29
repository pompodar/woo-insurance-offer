<?php
/*
Plugin Name: Woo Events Early Birds
Description: Discount Addon For WooEvents to apply a 10% discount to products starting 3 months late than the current date.
Version: 1.0
*/

// Define a global flag variable to track eligibility
$discount_eligible = false;

// Apply a discount based on purchase date
add_action('woocommerce_before_calculate_totals', 'apply_discount_based_on_purchase_date');

function apply_discount_based_on_purchase_date($cart) {

    // Set the desired discount percentage
    $discount_percentage = 10;

    // Calculate the date 3 months from the current date
    $three_months = strtotime('+3 months');

    global $discount_eligible;

    foreach ($cart->get_cart() as $cart_item) {
        $product_id = $cart_item['product_id'];

        // Get the start date from the product's we_startdate post meta
        $start_date = get_post_meta($product_id, 'we_startdate', true);

        if ($start_date && $start_date >= $three_months) {
            // Calculate the discount amount
            $discount = ($cart_item['data']->get_price() * $discount_percentage) / 100;
            
            // Apply the discount to the cart item
            $cart_item['data']->set_price($cart_item['data']->get_price() - $discount);
        
            // Set the flag to true if an eligible product is found
            $discount_eligible = true;
        }
    }
}

// Display the notice on the cart page
add_action('woocommerce_before_cart', 'display_discount_notice_in_cart');

// Display the notice at the bottom of the checkout page
add_action('woocommerce_review_order_before_submit', 'display_discount_notice_in_checkout');

function display_discount_notice_in_cart() {
    global $discount_eligible;

    // Display the notice if eligible products are found
    if ($discount_eligible) {
        echo '<p class="discount-notice">You are eligible for a 10% discount on certain products starting 3 months or more from now.</p>';
    }
}

function display_discount_notice_in_checkout() {
    global $discount_eligible;

    // Display the notice if eligible products are found
    if ($discount_eligible) {
        echo '<p class="discount-notice">You are eligible for a 10% discount on certain products starting 3 months or more from now.</p>';
    }
}