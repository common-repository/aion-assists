<?php

add_action("wp_ajax_aionassists_save_settings", "aionassists_save_settings");
add_action("wp_ajax_nopriv_aionassists_save_settings", "aionassists_save_settings");

add_action("wp_ajax_save_company_details", "save_company_details");

add_action("wp_ajax_aionassists_save_preferences", "aionassists_save_preferences");
add_action("wp_ajax_nopriv_aionassists_save_preferences", "aionassists_save_preferences");

add_action("wp_ajax_aionassists_save_main_settings", "aionassists_save_main_settings");
add_action("wp_ajax_nopriv_aionassists_save_main_settings", "aionassists_save_main_settings");

add_action("wp_ajax_fetch_sessions_data", "aionassists_fetch_sessions_data_callback");
add_action("wp_ajax_nopriv_fetch_sessions_data", "aionassists_fetch_sessions_data_callback");

add_action("admin_notices", "aionassists_show_admin_notices");

function aionassists_show_admin_notices()
{
    $status = get_option("aionassists_last_action_status", false);
    if ($status == "success") {
        echo '<div class="notice notice-success is-dismissible"><p>' .
            esc_html__(
                "Your settings have been successfully saved.",
                "aion-assists"
            ) .
            "</p></div>";
        delete_option("aionassists_last_action_status");
    }
}

function aionassists_fetch_sessions_data_callback()
{
    $user_status = get_option("aionassists_plan_name", "free");

    if ($user_status === "advanced") {
        wp_send_json_success(["status" => "advanced"]);
    } elseif ($user_status === "enterprise") {
        wp_send_json_success(["status" => "enterprise"]);
    } else {
        wp_send_json_success(["status" => "free"]);
    }
    wp_die();
}



function aionassists_save_preferences()
{
    $courierCompany = sanitize_text_field($_POST["courierCompany"]);
    $returnConditions = sanitize_text_field($_POST["returnConditions"]);
    $warrantyTerms = sanitize_text_field($_POST["warrantyTerms"]);
    $dispatchDuration = sanitize_text_field($_POST["dispatchDuration"]);
    $phoneNumber = sanitize_text_field($_POST["phoneNumber"]);
    $emailAddress = sanitize_text_field($_POST["emailAddress"]);
    $freeShippingThreshold = sanitize_text_field(
        $_POST["freeShippingThreshold"]
    );
    $refundDuration = sanitize_text_field($_POST["refundDuration"]);

    update_option("aionassists_courierCompany", $courierCompany);
    update_option("aionassists_returnConditions", $returnConditions);
    update_option("aionassists_warrantyTerms", $warrantyTerms);
    update_option("aionassists_dispatchDuration", $dispatchDuration);
    update_option("aionassists_phoneNumber", $phoneNumber);
    update_option("aionassists_emailAddress", $emailAddress);
    update_option("aionassists_freeShippingThreshold", $freeShippingThreshold);
    update_option("aionassists_refundDuration", $refundDuration);

    update_option("aionassists_last_action_status", "success");
    wp_send_json_success("Preferences saved successfully");
    wp_die();
}

function aionassists_save_main_settings()
{      
    $welcomeHeader = sanitize_text_field($_POST["welcomeHeader"]);
    $welcomeMessageText = sanitize_text_field($_POST["welcomeMessageText"]);
    $chatbotHeader = sanitize_text_field($_POST["chatbotHeader"]);
    $faqHref = sanitize_text_field($_POST["faqHref"]);
    $firstMessage = sanitize_text_field($_POST["firstMessage"]);
    $firstMessageHref = sanitize_text_field($_POST["firstMessageHref"]);
    $selectedTheme = sanitize_text_field($_POST["selectedTheme"]);

    update_option("aionassists_welcomeHeader", $welcomeHeader);
    update_option("aionassists_welcomeMessageText", $welcomeMessageText);
    update_option("aionassists_chatbotHeader", $chatbotHeader);
    update_option("aionassists_faqHref", $faqHref);
    update_option("aionassists_firstMessage", $firstMessage);
    update_option("aionassists_firstMessageHref", $firstMessageHref);
    update_option("aionassists_selectedTheme", $selectedTheme);

    update_option("aionassists_last_action_status", "success");
    wp_send_json_success("Main settings saved successfully");
    wp_die();
}

function save_company_details()
{
    global $wpdb;

    $token = sanitize_text_field($_POST["token"]);
    $license_option = sanitize_text_field($_POST["license_option"]);
    $license_id = sanitize_text_field($_POST["license_id"]);
    $license_key = sanitize_text_field($_POST["license_key"]);
    $company_name = sanitize_text_field($_POST["company_name"]);
    $openai_key = sanitize_text_field($_POST["openai_key"]);
	$plan_name = sanitize_text_field($_POST["plan_name"]);

    update_option("aionassists_token", $token);
    update_option("aionassists_license_key", $license_key);
    update_option("aionassists_license_id", $license_id);
    update_option("aionassists_company_name", $company_name);
    update_option("aionassists_license_option", $license_option);
    update_option("aionassists_openai_key", $openai_key);
    update_option("aionassists_plan_name", $plan_name);

    update_option("aionassists_last_action_status", "success");
    wp_send_json_success("Company details saved successfully");
    wp_die();
}

function aionassists_save_settings()
{
    $settings = [
        "retrieve_an_order_status" => sanitize_text_field(
            $_POST["retrieve_an_order_status"]
        ),
        "retrieve_billing_address" => sanitize_text_field(
            $_POST["retrieve_billing_address"]
        ),
        "retrieve_shipping_address" => sanitize_text_field(
            $_POST["retrieve_shipping_address"]
        ),
        "retrieve_an_order_create_date" => sanitize_text_field(
            $_POST["retrieve_an_order_create_date"]
        ),
        "retrieve_an_order_items" => sanitize_text_field(
            $_POST["retrieve_an_order_items"]
        ),
        "retrieve_order_refunds" => sanitize_text_field(
            $_POST["retrieve_order_refunds"]
        ),
        "list_all_payment_gateways" => sanitize_text_field(
            $_POST["list_all_payment_gateways"]
        ),
        "list_all_shipping_zones" => sanitize_text_field(
            $_POST["list_all_shipping_zones"]
        ),
        "update_an_order_note" => sanitize_text_field(
            $_POST["update_an_order_note"]
        ),
        "update_shipping_address" => sanitize_text_field(
            $_POST["update_shipping_address"]
        ),
        "describe_product_marketing_style" => sanitize_text_field(
            $_POST["describe_product_marketing_style"]
        ),
        "retrieve_product_stock" => sanitize_text_field(
            $_POST["retrieve_product_stock"]
        ),
    ];

    foreach ($settings as $key => $value) {
        update_option("aionassists_" . $key, $value);
    }

    update_option("aionassists_last_action_status", "success");
    wp_send_json_success("Settings saved successfully");
    wp_die();
}
function aionassists_add_admin_menu()
{
    add_menu_page(
        "Aion Assists",
        "Aion Assists",
        "manage_options",
        "aionassists_dashboard",
        "aionassists_dashboard_page",
        "dashicons-format-status",
        "7"
    );
    add_submenu_page(
        "aionassists_dashboard",
        __("Dashboard", "aion-assists"),
        __("Dashboard", "aion-assists"),
        "manage_options",
        "aionassists_dashboard",
        "aionassists_dashboard_page"
    );
    add_submenu_page(
        "aionassists_dashboard",
        __("Settings", "aion-assists"),
        __("Settings", "aion-assists"),
        "manage_options",
        "aionassists_settings",
        "aionassists_settings_page"
    );
    add_submenu_page(
        "aionassists_dashboard",
        __("Preferences", "aion-assists"),
        __("Preferences", "aion-assists"),
        "manage_options",
        "aionassists_preferences",
        "aionassists_preferences_page"
    );
    add_submenu_page(
        "aionassists_dashboard",
        __("Customize", "aion-assists"),
        __("Customize", "aion-assists"),
        "manage_options",
        "aionassists_customize",
        "aionassists_customize_page"
    );
    add_submenu_page(
        "aionassists_dashboard",
        __("Setup Wizard", "aion-assists"),
        __("Setup Wizard", "aion-assists"),
        "manage_options",
        "aionassists_setup_wizard",
        "aionassists_setup_wizard_page"
    );
}

add_action("admin_menu", "aionassists_add_admin_menu");

function aionassists_dashboard_page()
{
    ?>
    <h1><?php echo esc_html__("Dashboard", "aion-assists"); ?></h1>
    <div class="dashboard-container">
        <div class="metrics-row">
            <div class="metric-box">
                <div class="icon-container">
                    <img src="<?php echo esc_url(plugin_dir_url(__FILE__) . "media/message-chart.svg"); ?>" alt="Sessions" class="metric-icon">
                </div>
                <div class="text-container">
                    <span class="metric-number" id="sessions-metric">-</span>
                    <span class="metric-label"><?php echo esc_html__(
                        "Sessions",
                        "aion-assists"
                    ); ?></span>
                </div>
            </div>
            <div class="metric-box">
				<div class="icon-container">
                    <img src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'media/message-sum.svg'); ?>" alt="Conversions" class="metric-icon">
				</div>
				<div class="text-container">
					<span class="metric-number" id="conversions-metric">-</span>
					<span class="metric-label"><?php echo esc_html__(
         "Conversions",
         "aion-assists"
     ); ?></span>
				</div>
			</div>
            <div class="metric-box">
                <div class="icon-container">
                    <img src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'media/sucesss-message.svg'); ?>" alt="Leads" class="metric-icon">
                </div>
                <div class="text-container">
                    <span class="metric-number" id="successful-metric">-</span>
                    <span class="metric-label"><?php echo esc_html__(
                        "Successful Operations",
                        "aion-assists"
                    ); ?></span>
                </div>
            </div>
        </div>
        <div class="metric-box full-width">
			<div class="left-column">
				
			</div>
			<div class="right-column">
				
			</div>
		</div>
    </div>
    <?php
}
function aionassists_settings_page()
{
    $plan_name = get_option("aionassists_plan_name", "free");
    $is_advanced = $plan_name === "advanced";
    $is_enterprise = $plan_name === "enterprise";
    $is_free = $plan_name === "free" || $plan_name === "";

    $settings = [
        "retrieve_an_order_status" => [
            "option" => get_option("aionassists_retrieve_an_order_status", ""),
            "label" => __("Allow to view order status.", "aion-assists"),
            "description" => __("A tool where the customer can view the order status by entering the order number.", "aion-assists"),
        ],
        "retrieve_billing_address" => [
            "option" => get_option("aionassists_retrieve_billing_address", ""),
            "label" => __("Allow to view the billing address.", "aion-assists"),
            "description" => __("A tool where the customer can view their billing address by entering their order number and email address.", "aion-assists"),
        ],
        "retrieve_shipping_address" => [
            "option" => get_option("aionassists_retrieve_shipping_address", ""),
            "label" => __("Allow to view the shipping address.", "aion-assists"),
            "description" => __("A tool where the customer can view their shipping address by entering their order number and email address.", "aion-assists"),
        ],
        "retrieve_an_order_create_date" => [
            "option" => get_option("aionassists_retrieve_an_order_create_date", ""),
            "label" => __("Allow to view the creation date of the order.", "aion-assists"),
            "description" => __("A tool where the customer can view the creation date of the order by entering the order number.", "aion-assists"),
        ],
        "retrieve_an_order_items" => [
            "option" => get_option("aionassists_retrieve_an_order_items", ""),
            "label" => __("Allow to view order items.", "aion-assists"),
            "description" => __("A tool where the customer can enter their order number and email address to view the items of the order.", "aion-assists"),
        ],
        "retrieve_order_refunds" => [
            "option" => get_option("aionassists_retrieve_order_refunds", ""),
            "label" => __("Allow to view an order return status.", "aion-assists"),
            "description" => __("A tool where the customer can enter the order number and e-mail address to view the return status of the order.", "aion-assists"),
        ],
        "list_all_payment_gateways" => [
            "option" => get_option("aionassists_list_all_payment_gateways", ""),
            "label" => __("Allow to view which payment gateways are used.", "aion-assists"),
            "description" => __("A tool that can display which payment methods your store allows for customers.", "aion-assists"),
        ],
        "list_all_shipping_zones" => [
            "option" => get_option("aionassists_list_all_shipping_zones", ""),
            "label" => __("Allow to view which zones are shipping to.", "aion-assists"),
            "description" => __("A tool that allows a user to view information about which regions your store ships to.", "aion-assists"),
        ],
        "update_an_order_note" => [
            "option" => get_option("aionassists_update_an_order_note", ""),
            "label" => __("Allow adding and updating notes to the order.", "aion-assists"),
            "description" => __("A tool that allows the customer to add or update a note to their order by entering their order number and email address.", "aion-assists"),
        ],
        "update_shipping_address" => [
            "option" => get_option("aionassists_update_shipping_address", ""),
            "label" => __("Allow updating the shipping address.", "aion-assists"),
            "description" => __("A tool that allows the customer to update the delivery address by entering the order number and email address.", "aion-assists"),
        ],
        "describe_product_marketing_style" => [
            "option" => get_option("aionassists_describe_product_marketing_style", ""),
            "label" => __("Allow product description with sales talk.", "aion-assists"),
            "description" => __("A tool that provides customers with product descriptions in the event of a product inquiry. If there is an existing description for the product, it will be displayed. If not, a response will be crafted by GPT, taking the product title as a reference and responding in the tone of a marketer.", "aion-assists"),
        ],
        "retrieve_product_stock" => [
            "option" => get_option("aionassists_retrieve_product_stock", ""),
            "label" => __("Allow to view stock status.", "aion-assists"),
            "description" => __("A tool where the customer can find out the stock status of a product.", "aion-assists"),
        ],
    ];    

    echo '<div class="company-info-page" id="settings">';
    echo '<div id="snackbar"></div>';
    echo "<h1>" . esc_html__("Chatbot Settings", "aion-assists") . "</h1>";
    ?>
	<?php if ($is_free): ?>
					<div class="success-msg">
						<i class="fa fa-check"></i>
						<?php echo esc_html__(
          "To use these features, you need to purchase the plugin.",
          "aion-assists"
      ); ?></div>
						
        <?php elseif ($is_advanced): ?>
					<div class="success-msg">
						<i class="fa fa-check"></i>
						<?php echo esc_html__(
          "To use all features, you need to upgrade to the enterprise plan.",
          "aion-assists"
      ); ?> </div>
        <?php endif; ?>
<?php
echo "<p>" .
    esc_html__(
        "Select the operations you want the Customer Care service to perform.",
        "aion-assists"
    ) .
    "</p>";
echo '<form method="post" id="settingsForm">';
wp_nonce_field('aionassists_save_settings_nonce', 'aionassists_settings_nonce'); 
foreach ($settings as $setting => $details) {
    $disabled =
        $is_free ||
        (!$is_enterprise &&
            !in_array($setting, [
                "retrieve_an_order_status",
                "retrieve_billing_address",
                "retrieve_shipping_address",
                "retrieve_an_order_items",
                "retrieve_an_order_create_date",
                "retrieve_order_refunds",
                "list_all_payment_gateways",
                "list_all_shipping_zones",
            ]));
    echo '<div class="preferences-section">';

    printf(
        /* translators: %s: Feature label */
        '<label>%s:</label>',
        esc_html($details["label"])
    );
    
    printf(
        /* translators: %s: Feature description */
        '<p class="description">%s</p>',
        esc_html($details["description"])
    );
    
    echo '<label><input type="radio" name="' . esc_attr($setting) . '" value="yes" ' . ($details["option"] === "yes" ? "checked" : "") . ($disabled ? " disabled" : "") . '"> ' . esc_html__("Yes", "aion-assists") . "</label>";

    echo '<label><input type="radio" name="' . esc_attr($setting) . '" value="no" ' . ($details["option"] === "no" ? "checked" : "") . ($disabled ? " disabled" : "") . '"> ' . esc_html__("No", "aion-assists") . "</label>";
    
    echo "</div>";
}
echo '<button type="button" id="save-settings" class="complete-installation">' .
    esc_html__("Save Settings", "aion-assists") .
    "</button>";
echo "</form>";
echo "</div>";
}
function aionassists_preferences_page()
{
    $plan_name = get_option("aionassists_plan_name", "free");
    $is_advanced = $plan_name === "advanced";
    $is_enterprise = $plan_name === "enterprise";
    $is_free = $plan_name === "free" || $plan_name === "";
    ?>      
    <div class="company-info-page" id="preferences">
        <div id="snackbar"></div>
        <h1><?php echo esc_html__("Company Preferences", "aion-assists"); ?></h1>
        <?php if ($is_free): ?>
					<div class="success-msg">
						<i class="fa fa-check"></i>
						<?php echo esc_html__(
          "To use these features, you need to purchase the plugin.",
          "aion-assists"
      ); ?></div>
						
        <?php elseif ($is_advanced): ?>
					</style>
					<div class="success-msg">
						<i class="fa fa-check"></i>
						<?php echo esc_html__(
          "To use all features, you need to upgrade to the enterprise plan.",
          "aion-assists"
      ); ?> </div>
        <?php endif; ?>
        <form method="post" id="preferencesForm">
            <?php wp_nonce_field('aionassists_save_preferences_nonce', 'aionassists_preferences_nonce'); ?>

            <label for="phoneNumber"><?php echo esc_html__(
                "What is the customer service phone number?",
                "aion-assists"
            ); ?></label>
            <input type="text" id="phoneNumber" name="phoneNumber" placeholder="<?php echo esc_attr__(
                "Customer Service Phone Number",
                "aion-assists"
            ); ?>" value="<?php echo esc_attr(
    get_option("aionassists_phoneNumber", "")
); ?>" <?php echo $is_free ? "disabled" : ""; ?> required>
            
            <label for="emailAddress"><?php echo esc_html__(
                "What is the customer service email address?",
                "aion-assists"
            ); ?></label>
            <input type="text" id="emailAddress" name="emailAddress" placeholder="<?php echo esc_attr__(
                "Customer Service Email Address",
                "aion-assists"
            ); ?>" value="<?php echo esc_attr(
    get_option("aionassists_emailAddress", "")
); ?>" <?php echo $is_free ? "disabled" : ""; ?> required>
            
            <label for="freeShippingThreshold"><?php echo esc_html__(
                "What is the minimum purchase amount to qualify for free shipping?",
                "aion-assists"
            ); ?></label>
            <input type="text" id="freeShippingThreshold" name="freeShippingThreshold" placeholder="<?php echo esc_attr__(
                "Free Shipping Threshold",
                "aion-assists"
            ); ?>" value="<?php echo esc_attr(
    get_option("aionassists_freeShippingThreshold", "")
); ?>" <?php echo $is_free ? "disabled" : ""; ?> required>
            
            <label for="courierCompany"><?php echo esc_html__(
                "Which shipping company is used for delivery?",
                "aion-assists"
            ); ?></label>
            <input type="text" id="courierCompany" name="courierCompany" placeholder="<?php echo esc_attr__(
                "Shipping Company",
                "aion-assists"
            ); ?>" value="<?php echo esc_attr(
    get_option("aionassists_courierCompany", "")
); ?>" <?php echo $is_free || $is_advanced ? "disabled" : ""; ?> required>
            
            <label for="returnConditions"><?php echo esc_html__(
                "What are the return policies and how to make a return?",
                "aion-assists"
            ); ?></label>
            <input type="text" id="returnConditions" name="returnConditions" placeholder="<?php echo esc_attr__(
                "Return Policies",
                "aion-assists"
            ); ?>" value="<?php echo esc_attr(
    get_option("aionassists_returnConditions", "")
); ?>" <?php echo $is_free || $is_advanced ? "disabled" : ""; ?> required>
            
            <label for="warrantyTerms"><?php echo esc_html__(
                "What are the warranty terms?",
                "aion-assists"
            ); ?></label>
            <input type="text" id="warrantyTerms" name="warrantyTerms" placeholder="<?php echo esc_attr__(
                "Warranty Terms",
                "aion-assists"
            ); ?>" value="<?php echo esc_attr(
    get_option("aionassists_warrantyTerms", "")
); ?>" <?php echo $is_free || $is_advanced ? "disabled" : ""; ?> required>
            
            <label for="dispatchDuration"><?php echo esc_html__(
                "What is the shipping/delivery time for orders?",
                "aion-assists"
            ); ?></label>
            <input type="text" id="dispatchDuration" name="dispatchDuration" placeholder="<?php echo esc_attr__(
                "Shipping Time",
                "aion-assists"
            ); ?>" value="<?php echo esc_attr(
    get_option("aionassists_dispatchDuration", "")
); ?>" <?php echo $is_free || $is_advanced ? "disabled" : ""; ?> required>
            
            <label for="refundDuration"><?php echo esc_html__(
                "When will the refund be credited to bank account?",
                "aion-assists"
            ); ?></label>
            <input type="text" id="refundDuration" name="refundDuration" placeholder="<?php echo esc_attr__(
                "Refund Duration",
                "aion-assists"
            ); ?>" value="<?php echo esc_attr(
    get_option("aionassists_refundDuration", "")
); ?>" <?php echo $is_free || $is_advanced ? "disabled" : ""; ?> required>
            
            <button type="button" id="save-preferences" class="complete-installation" <?php echo $is_free
                ? "disabled"
                : ""; ?>><?php echo esc_html__(
    "Save Preferences",
    "aion-assists"
); ?></button>
        </form>
    </div>  
    <?php
}
function aionassists_customize_page()
{
    $selectedTheme = get_option("aionassists_selectedTheme", "theme1"); ?>
        <div class="company-info-page" id="mainSettings">
			<div id="snackbar"></div>
            <h1><?php echo esc_html__("Customize", "aion-assists"); ?></h1>
            <form method="post" id="mainSettingsForm">
            <?php wp_nonce_field('aionassists_save_main_settings_nonce', 'aionassists_main_settings_nonce'); ?>
			<?php settings_fields("aionassists_customize_options"); ?>
            <?php do_settings_sections("aionassists_customize_options"); ?>
                <br>
				
				<h3><?php echo esc_html__("Theme", "aion-assists"); ?></h3>
				<p class="form-description"><?php echo esc_html__(
        "Choose a theme for the chatbot interface.",
        "aion-assists"
    ); ?></p> 
				<div class="theme-options-container">
					<?php
     $themes = [
         "theme1" => "linear-gradient(135deg, #CE9FFC 10%, #938aed 100%)",
         "theme2" => "linear-gradient(135deg, #FF9D6C 10%, #dd6d95 100%)",
         "theme3" => "linear-gradient( 135deg, #3C8CE7 50%, #00EAFF 130%)",
         "theme4" =>
             "linear-gradient(-225deg, #7085B6 0%, #87A7D9 50%, #a2bec5 100%)",
         "theme5" =>
             "linear-gradient( 109.6deg,  rgba(61,131,97,1) 11.2%, rgba(28,103,88,1) 91.1% )",
         "theme6" =>
             "radial-gradient(circle at 18.7% 37.8%, rgb(239 239 239) 0%, rgb(219 219 219) 90%)",
     ];
     foreach ($themes as $theme => $gradient) { ?>
                <div class="theme-option">
                <input type="radio" id="<?php echo esc_attr($theme); ?>" name="aionassists_theme" value="<?php echo esc_attr($theme); ?>" <?php checked($selectedTheme, $theme); ?>>
					<label for="<?php echo esc_attr($theme); ?>">
                        <div class="square" style="background-image: <?php echo esc_attr($gradient); ?>"></div>
					</label>
				</div>
                <?php }
     ?>    
			</div>		
                <label for="welcomeHeader" class="form-label"><strong><?php echo esc_html__(
                    "Welcome Title",
                    "aion-assists"
                ); ?></strong></label>
                <p class="form-description"><?php echo esc_html__(
                    "Title before the chat is started.",
                    "aion-assists"
                ); ?></p>
				<input type="text" id="welcomeHeader" name="welcomeHeader" placeholder="<?php echo esc_attr__(
        "Welcome Title",
        "aion-assists"
    ); ?>" maxlength="35" value="<?php echo esc_attr(
    get_option("aionassists_welcomeHeader", "")
); ?>">

                <label for="welcomeMessageText" class="form-label"><strong><?php echo esc_html__(
                    "Welcome Message Text",
                    "aion-assists"
                ); ?></strong></label>
                <p class="form-description"><?php echo esc_html__(
                    "Information letter in the form before the chat is initiated.",
                    "aion-assists"
                ); ?></p>
                <input type="text" id="welcomeMessageText" name="welcomeMessageText" placeholder="<?php echo esc_attr__(
                    "Welcome Message Text",
                    "aion-assists"
                ); ?>"  maxlength="250" value="<?php echo esc_attr(
    get_option("aionassists_welcomeMessageText", "")
); ?>">

                <label for="chatbotHeader" class="form-label"><strong><?php echo esc_html__(
                    "Chatbot Title",
                    "aion-assists"
                ); ?></strong></label>
                <p class="form-description"><?php echo esc_html__(
                    "Title when chat is started.",
                    "aion-assists"
                ); ?></p>
                <input type="text" id="chatbotHeader" name="chatbotHeader" placeholder="<?php echo esc_attr__(
                    "Chatbot Title",
                    "aion-assists"
                ); ?>"  maxlength="25" value="<?php echo esc_attr(
    get_option("aionassists_chatbotHeader", "")
); ?>">
				
				<label for="faqHref" class="form-label"><strong><?php echo esc_html__(
        "FAQ Link",
        "aion-assists"
    ); ?></strong></label>
				<p class="form-description"><?php echo esc_html__(
        "Add a link to the FAQ section.",
        "aion-assists"
    ); ?></p>
				<input type="text" id="faqHref" name="faqHref" placeholder="<?php echo esc_attr__(
        "FAQ Link",
        "aion-assists"
    ); ?>" maxlength="255" value="<?php echo esc_attr(
    get_option("aionassists_faqHref", "#")
); ?>">

                <label for="firstMessage" class="form-label"><strong><?php echo esc_html__(
                    "First Message or Marketing",
                    "aion-assists"
                ); ?></strong></label>
                <p class="form-description"><?php echo esc_html__(
                    "Add a first message when the chat is started.",
                    "aion-assists"
                ); ?></p>
                <input type="text" id="firstMessage" name="firstMessage" placeholder="<?php echo esc_attr__(
                    "First Message",
                    "aion-assists"
                ); ?>" maxlength="150" value="<?php echo esc_attr(
    get_option("aionassists_firstMessage", "")
); ?>">
                
                <label for="firstMessageHref" class="form-label"><strong><?php echo esc_html__(
                    "Href/Link for First Message",
                    "aion-assists"
                ); ?></strong></label>
                <input type="text" id="firstMessageHref" name="firstMessageHref" placeholder="HREF" value="<?php echo esc_attr(
                    get_option("aionassists_firstMessageHref", "#")
                ); ?>">

                <button type="button" id="save-main-settings" class="complete-installation"><?php echo esc_html__(
                    "Save Customize",
                    "aion-assists"
                ); ?></button>
            </form>

        </div>
        
        <?php
}
function aionassists_setup_wizard_page()
{
    $license_id = get_option("aionassists_license_id", "");
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $license_key = sanitize_text_field($_POST["license_key"]);
        $website_url = get_site_url();
        $endpoint = "/wc-auth/v1/authorize";
        $params = [
            "app_name" => "Aion Assists - Customer Service",
            "scope" => "read_write",
            "user_id" => urlencode($license_id), // Lisans id olarak kullanılacak
            "return_url" => urlencode(
                admin_url(
                    "admin.php?page=aionassists_settings&tab=setup_wizard"
                )
            ),
            "callback_url" => urlencode(
                "https://aionassistsapp.azurewebsites.net/auth/callback-endpoint"
            ),
        ];
        $query_string = http_build_query($params);
        $auth_url = $website_url . $endpoint . "?" . $query_string;

        echo "<script>window.location.href = '{$auth_url}';</script>";
        exit();
    }
    ?> 
	
            <div class="company-info-page">
                <h1><?php echo esc_html__(
                    "Welcome to the Setup Wizard",
                    "aionassists"
                ); ?></h1>
				<?php if (!empty($license_id)): ?>
					<style>
					.success-msg {
						color: #270;
						background-color: #DFF2BF;
						margin: 10px 0;
						padding: 10px;
						border-radius: 3px;
					}
					</style>
					<div class="success-msg">
						<i class="fa fa-check"></i>
						<?php echo esc_html__(
          "Your plugin has been successfully activated.",
          "aionassists"
      ); ?>
					</div>
            	<?php endif; ?>
				<p><?php echo esc_html__(
        'If you have chosen one of our "Advanced" or "Enterprise" packages, please select the "Premium" option.',
        "aionassists"
    ); ?></p>

                <p><?php echo esc_html__(
                    "If you want to experience Aion Assists for free, you must enter your OpenAI API key. Your license key is generated automatically.",
                    "aionassists"
                ); ?></p>
                <form method="post" id="setupWizard">
					
					<div class="license-type-selection">
						<h4 class="license-type-title"><?php echo esc_html__(
          "Your Plan:",
          "aionassists"
      ); ?></h4>
						<label class="license-option">
							<input type="radio" id="licenseTypeFree" name="license_option" value="free">
							Free
						</label>
						<label class="license-option premium">
							<input type="radio" id="licenseTypePremium" name="license_option" value="premium" checked>
							Premium
						</label>
					</div>
					
					<?php
     $license_key_option = get_option("aionassists_license_key", "");
     $company_name = get_option("aionassists_company_name", "");
     ?> 
					<label for="license_key"><?php echo esc_html__(
         "License Key:",
         "aionassists"
     ); ?></label>
                    <input type="text" id="license_key" name="license_key" placeholder="<?php echo esc_attr__(
                        "License Key",
                        "aionassists"
                    ); ?>" value="<?php echo esc_attr(
    $license_key_option
); ?>" required>
                    <div class="free-trial-checkbox">
						<input type="checkbox" id="free_trial_started" name="free_trial_started">
						<label for="free_trial_started"><?php echo esc_html__("Check if you started the free trial from the \"Plans and Pricing\" page.", "aionassists"); ?></label>
					    </div>
					    <p><a href="<?php echo aion_assists_fs()->get_trial_url(); ?>"><?php echo esc_html__("Click here to start the free trial.", "aionassists"); ?></a></p>

                    <label for="openai_key"><?php echo esc_html__(
                        "OpenAI API Key:",
                        "aionassists"
                    ); ?></label>
                    <input type="text" id="openai_key" name="openai_key" placeholder="<?php echo esc_attr__(
                        "OpenAI API Key",
                        "aionassists"
                    ); ?>" required>

                    <label for="company_name"><?php echo esc_html__(
                        "Company Name:",
                        "aionassists"
                    ); ?></label>
                    <input type="text" id="company_name" name="company_name" placeholder="<?php echo esc_attr__(
                        "Company Name",
                        "aionassists"
                    ); ?>" value="<?php echo esc_attr(
    $company_name
); ?>" required>

                    <label for="website_url"><?php echo esc_html__(
                        "Website URL:",
                        "aionassists"
                    ); ?></label>
                    <input type="text" id="website_url" name="website_url" placeholder="<?php echo esc_attr__(
                        "Website URL",
                        "aionassists"
                    ); ?>" value="<?php echo esc_attr(
    get_site_url()
); ?>" required>

                    <button type="submit" id="complete-installation" class="complete-installation company-info-page">
    <?php echo esc_html__("Complete Installation", "aionassists"); ?>
    <div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
</button>

				</form>

            </div>
		<?php
}

function aionassists_admin_scripts() {
    wp_enqueue_style(
        'aionassists-wizard-style',
        plugin_dir_url(__FILE__) . 'css/style.css',
        array(),
        '1.0.0'
    );
}
add_action('admin_enqueue_scripts', 'aionassists_admin_scripts');