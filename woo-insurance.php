<?php
/*
Plugin Name: Woo Insurance
Description: Adds the option to offer insurance.
Version: 1.0
*/

// Display the custom form in the cart
add_action('woocommerce_cart_contents', 'wi_display_custom_cart_form');

function wi_display_custom_cart_form() {
    echo '<div class="wi_custom-cart-form">';
    echo '<h2>Insurance Options</h2>';
    
    // Include the HTML form you created earlier
    ?>
<form id="wi_custom-form" method="post" action="">
    <label for="insurance-checkbox">
        <input type="checkbox" id="wi_insurance-checkbox" name="insurance" value="yes" /> Add Insurance
    </label>
</form>
<?php
    echo '</div>';
}

add_action('woocommerce_cart_totals_before_order_total', 'add_custom_field_before_total', 10);

function add_custom_field_before_total() {
    $custom_field_value = WC()->session->get('wi_insurance');

    // Get the current cart total
    $cart_total = WC()->cart->subtotal;

    $insurance_percentage = 10;

    // Calculate the fee
    $insurance_amount = number_format(($cart_total * $insurance_percentage), 2); // 10% increase is equivalent to multiplying by 1.1


    if ($custom_field_value == 'yes') {
        echo "<h4>Sayan Protector <span>$ {$insurance_amount}</span></h4>";
    }
}

add_action('woocommerce_review_order_after_cart_contents', 'wi_add_custom_field_before_total_in_checkout', 10);

function wi_add_custom_field_before_total_in_checkout() {
    $custom_field_value = WC()->session->get('wi_insurance');

    // Get the current cart total
    $cart_total = WC()->cart->subtotal;

    $insurance_percentage = 10;

    // Calculate the fee
    $insurance_amount = number_format(($cart_total * $insurance_percentage), 2); // 10% increase is equivalent to multiplying by 1.1


    if ($custom_field_value == 'yes') {
        echo "<h4>Sayan Protector <span>$ {$insurance_amount}</span></h4>";
    }
}


add_filter( 'woocommerce_calculated_total', 'wi_add_fee_to_cart_total', 10, 2 );

function wi_add_fee_to_cart_total( $total, $cart ) {
    $custom_field_value = WC()->session->get('wi_insurance');

    if ($custom_field_value == 'yes') {
        $insurance_percentage = 10;

        // Calculate the fee
        $insurance_amount = $total * $insurance_percentage; // 10% increase is equivalent to multiplying by 1.1

        $total = number_format(($total + $insurance_amount), 2);
    }

    return $total;
}

add_action('wp_ajax_wi_custom_remove_from_cart', 'wi_custom_remove_from_cart');
add_action('wp_ajax_nopriv_wi_custom_remove_from_cart', 'wi_custom_remove_from_cart');

function wi_custom_remove_from_cart() {
    // Unset the 'custom_field' session variable
    WC()->session->set('wi_insurance', null);

    $custom_field_value = WC()->session->get('wi_insurance');

    echo $custom_field_value;
    wp_die(); // This is required to terminate AJAX requests properly
}

add_action('wp_ajax_wi_custom_add_to_cart', 'wi_custom_add_to_cart');
add_action('wp_ajax_nopriv_wi_custom_add_to_cart', 'wi_custom_add_to_cart');

function wi_custom_add_to_cart() {
    WC()->session->set('wi_insurance', 'yes');

    $custom_field_value = WC()->session->get('wi_insurance');

    echo $custom_field_value;
    
    wp_die(); // This is required to terminate AJAX requests properly
}

add_action('wp_ajax_wi_check_insurance', 'wi_check_insurance');
add_action('wp_ajax_nopriv_wi_check_insurance', 'wi_check_insurance');

function wi_check_insurance() {
    $custom_field_value = WC()->session->get('wi_insurance');

    if ($custom_field_value == 'yes') {
        echo $custom_field_value;
    } else {
        echo false;
    }
       
    wp_die(); // This is required to terminate AJAX requests properly
}

add_action('woocommerce_thankyou', 'wi_unset_custom_field_session_variable');

function wi_unset_custom_field_session_variable($order_id) {
    // Get the custom field value (replace 'custom_field_key' with your actual field key)
    $custom_field_value = WC()->session->get('wi_insurance');

    if (!empty($custom_field_value)) {
        // Add the custom field to the order's post meta
        update_post_meta($order_id, 'wi_insurance', $custom_field_value);
    }
    
    // Unset the 'custom_field' session variable
    WC()->session->set('wi_insurance', null);
}

add_action('woocommerce_admin_order_data_after_billing_address', 'wi_display_custom_field_in_admin_order_details');

function wi_display_custom_field_in_admin_order_details($order) {
    // Get the custom field value from the order's post meta
    $custom_field_value = get_post_meta($order->get_id(), 'wi_insurance', true);

    if ($custom_field_value) {
        echo '<p><strong>Insurance:</strong> ' . esc_html($custom_field_value) . '</p>';
    }
}

add_filter('woocommerce_email_order_meta', 'wi_display_custom_field_in_order_email', 10, 3);

function wi_display_custom_field_in_order_email($order, $sent_to_admin, $plain_text) {
    // Get the custom field value from the order's post meta
    $custom_field_value = get_post_meta($order->get_id(), 'wi_insurance', true);

    if ($custom_field_value) {
        echo '<h2>Insurance:</h2>';
        echo '<p>' . esc_html($custom_field_value) . '</p>';
    }
}

add_action('woocommerce_order_details_after_order_table', 'wi_display_custom_field_in_order_history');

// Display custom field in order history
function wi_display_custom_field_in_order_history($order) {
    $custom_field_value = get_post_meta($order->get_id(), 'wi_insurance', true);
    
    if (!empty($custom_field_value)) {
        echo '<p><strong>Insurance: </strong> ' . $custom_field_value . '</p>';
    }
}

add_action('wp_enqueue_scripts', 'wi_enqueue_custom_cart_script');

function wi_enqueue_custom_cart_script() {
    wp_enqueue_script('wi_custom-cart-script', plugin_dir_url(__FILE__) . 'custom-cart.js', array('jquery'), '1.0', true);

    // Pass the AJAX URL to the script
    wp_localize_script('wi_custom-cart-script', 'customCartAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
    ));
}