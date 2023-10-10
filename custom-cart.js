jQuery(document).ready(function ($) {
    const checkbox = $("#wi_insurance-checkbox");

    let action = "&action=wi_check_insurance";

    // Check if insurance was selected
    $.ajax({
        url: customCartAjax.ajaxurl,
        type: "POST",
        data: action,
        success: function (response) {
            // Handle success (e.g., update the cart totals)
            console.log(response);
            
            if (response) {
                checkbox.prop("checked", true);
            } else {
                checkbox.prop("checked", false);
            }
        },
    });

    $("#wi_insurance-checkbox").on("input", function (event) {
        event.preventDefault(); // Prevent the default form submission

        let formData = $(this).serialize();
        
        if (checkbox.prop('checked')) {
            formData += "&action=wi_custom_add_to_cart";

            $.ajax({
                url: customCartAjax.ajaxurl,
                type: "POST",
                data: formData,
                success: function (response) {
                    // Handle success (e.g., update the cart totals)
                    console.log(response);
                },
            });
        } else {
            formData += "&action=wi_custom_remove_from_cart";
            
            $.ajax({
                url: customCartAjax.ajaxurl,
                type: "POST",
                data: formData,
                success: function (response) {
                    // Handle success (e.g., update the cart totals)
                    console.log(response);
                },
            });
        }
    });
});
