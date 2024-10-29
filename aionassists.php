<?php

/*
 * Plugin Name:       Aion Assists - Customer Service
 * Plugin URI:        https://aionisys.com/aion-assists
 * Description:       Aion Assists is a GPT-powered customer service plugin for your WooCommerce store.
 * Version:           1.0.1
 * Author:            aionisys
 * Author URI:        https://aionisys.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       aion-assists
 * Domain Path:       /languages
 */
if ( !defined( "ABSPATH" ) ) {
    die( "ACCESS DENIED FROM CODE" );
}
if ( !function_exists( 'aion_assists_fs' ) ) {
    function aion_assists_fs() {
        global $aion_assists_fs;
        if ( !isset( $aion_assists_fs ) ) {
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $aion_assists_fs = fs_dynamic_init( array(
                'id'             => '14579',
                'slug'           => 'aion-assists',
                'premium_slug'   => 'aion-assists-p',
                'type'           => 'plugin',
                'public_key'     => 'pk_aa6e0507a68b8f65ac92bf6d36db4',
                'is_premium'     => false,
                'has_addons'     => false,
                'has_paid_plans' => true,
                'trial'          => array(
                    'days'               => 10,
                    'is_require_payment' => false,
                ),
                'menu'           => array(
                    'slug'       => 'aionassists_dashboard',
                    'first-path' => 'admin.php?page=aionassists_setup_wizard',
                    'support'    => false,
                ),
                'is_live'        => true,
            ) );
        }
        return $aion_assists_fs;
    }

    aion_assists_fs();
    do_action( 'aion_assists_fs_loaded' );
}
function aionassists_load_textdomain() {
    load_plugin_textdomain( "aion-assists", false, basename( dirname( __FILE__ ) ) . "/languages" );
}

add_action( "plugins_loaded", "aionassists_load_textdomain" );
function aionassists_enqueue_google_fonts() {
    wp_enqueue_style( "aionassists-google-fonts-roboto", "https://fonts.googleapis.com/css?family=Roboto&display=swap" );
}

add_action( "wp_enqueue_scripts", "aionassists_enqueue_google_fonts" );
if ( is_admin() ) {
    require_once plugin_dir_path( __FILE__ ) . "admin/admin-functions.php";
    function aionassists_admin_enqueue_scripts(  $hook_suffix  ) {
        $is_dashboard_page = "toplevel_page_aionassists_dashboard" === $hook_suffix;
        wp_enqueue_script(
            "aionassists-admin",
            plugin_dir_url( __FILE__ ) . "admin/js/script.js",
            ["jquery"],
            null,
            true
        );
        wp_localize_script( "aionassists-admin", "aionAssistsSettings", [
            "apiToken"                => get_option( "aionassists_token", "" ),
            "licenseOption"           => get_option( "aionassists_license_option", "free" ),
            "licenseId"               => get_option( "aionassists_license_id", "" ),
            "licenseKey"              => get_option( "aionassists_license_key", "" ),
            "openaiKey"               => get_option( "aionassists_openai_key", "" ),
            "companyName"             => get_option( "aionassists_company_name", "" ),
            "siteLanguage"            => get_locale(),
            "sessionDetailsHeader"    => __( "Session Details: ", "aion-assists" ),
            "sessionIdHeader"         => __( "Session ID: ", "aion-assists" ),
            "customerHeader"          => __( "Customer: ", "aion-assists" ),
            "sessionStartedHeader"    => __( "Session Started: ", "aion-assists" ),
            "showSummaryText"         => __( "Show Summary", "aion-assists" ),
            "summaryIconUrl"          => plugin_dir_url( __FILE__ ) . "admin/media/stars.svg",
            "sessionSummaryHeader"    => __( "Session Summary: ", "aion-assists" ),
            "needUpgradeToEnterprise" => __( "Your current plan does not support message saving and viewing. To access this feature, you need to upgrade to the 'Enterprise' plan.", "aion-assists" ),
            "checkoutLink"            => __( "Click here to view plans.", "aion-assists" ),
            "planName"                => get_option( "aionassists_plan_name", "" ),
            "sessionCount"            => get_option( "aionassists_session_count", 0 ),
            "onDashboardPage"         => $is_dashboard_page,
        ] );
        wp_enqueue_style( "google-material-icons", "https://fonts.googleapis.com/icon?family=Material+Icons" );
    }

    add_action( "admin_enqueue_scripts", "aionassists_admin_enqueue_scripts" );
}
if ( !is_admin() ) {
    add_action( "wp_enqueue_scripts", "aionassists_enqueue_scripts" );
    function aionassists_enqueue_scripts() {
        wp_enqueue_script(
            "aionassists-chatbot",
            plugin_dir_url( __FILE__ ) . "script.js",
            ["jquery"],
            null,
            true
        );
        $token = get_option( "aionassists_token", "" );
        $firstMessage = get_option( "aionassists_firstMessage", "..." );
        $firstMessageHref = get_option( "aionassists_firstMessageHref", "#" );
        $selectedTheme = get_option( "aionassists_selectedTheme", "theme1" );
        $planName = get_option( "aionassists_plan_name", "free" );
        wp_localize_script( "aionassists-chatbot", "aionAssists", [
            "apiToken"         => $token,
            "firstMessage"     => $firstMessage,
            "firstMessageHref" => $firstMessageHref,
            "selectedTheme"    => $selectedTheme,
            "planName"         => $planName,
            "iconUrl"          => plugin_dir_url( __FILE__ ) . "assets/aionisys.png",
            "ajaxurl"          => admin_url( "admin-ajax.php" ),
        ] );
        wp_enqueue_style( "google-material-icons", "https://fonts.googleapis.com/icon?family=Material+Icons" );
        wp_enqueue_style( "aionassists-admin", plugin_dir_url( __FILE__ ) . "admin.css" );
    }

    function aionassists_enqueue_chatbot_style() {
        wp_enqueue_style(
            "aionassists-chatbot",
            plugin_dir_url( __FILE__ ) . "style.css",
            [],
            "1.0.0",
            "all"
        );
    }

    add_action( "wp_enqueue_scripts", "aionassists_enqueue_chatbot_style", 20 );
}
add_action( "wp_ajax_increase_session_count", "aionassists_increase_session_count" );
add_action( "wp_ajax_nopriv_increase_session_count", "aionassists_increase_session_count" );
function aionassists_increase_session_count() {
    if ( get_option( "aionassists_plan_name" ) == "free" ) {
        $current_count = get_option( "aionassists_session_count", 0 );
        update_option( "aionassists_session_count", ++$current_count );
    }
    echo esc_html( get_option( "aionassists_session_count" ) );
    wp_die();
}

function aionassists_chatbot_html() {
    $welcomeHeader = get_option( "aionassists_welcomeHeader", __( "Hello! How can we help you?", "aion-assists" ) );
    if ( empty( $welcomeHeader ) ) {
        $welcomeHeader = __( "Hello! How can we help you?", "aion-assists" );
    }
    $welcomeMessageText = get_option( "aionassists_welcomeMessageText", __( "Please enter your name and email address before contacting customer service.", "aion-assists" ) );
    if ( empty( $welcomeMessageText ) ) {
        $welcomeMessageText = __( "Please enter your name and email address before contacting customer service.", "aion-assists" );
    }
    $chatbotHeader = get_option( "aionassists_chatbotHeader", __( "Customer Service", "aion-assists" ) );
    if ( empty( $chatbotHeader ) ) {
        $chatbotHeader = __( "Customer Service", "aion-assists" );
    }
    $theme_colors = [
        "theme1" => "#b18df8",
        "theme2" => "#d66d71",
        "theme3" => "#3C8CE7",
        "theme4" => "#768dbf",
        "theme5" => "#2c745c",
        "theme6" => "#000916",
    ];
    $selected_theme = get_option( "aionassists_selectedTheme", "theme1" );
    $selected_color = ( isset( $theme_colors[$selected_theme] ) ? $theme_colors[$selected_theme] : $theme_colors["theme1"] );
    ?>

    <div id="chatbot-toggle">
    <button id="chatbot-open-button" style="background-color: <?php 
    echo ( $selected_theme == "theme6" ? "#000916" : esc_attr( $selected_color ) );
    ?>;">
        <img src="<?php 
    echo esc_url( plugin_dir_url( __FILE__ ) . "assets/chatbot-icon-1.svg" );
    ?>" class="chatbot-icon"/>
    </button>
</div>

    <div id="chatbot-box" class="chatbot-hidden">
        <div id="startChatFormContainer" class="chatbot-hidden">
            <div class="chatbot-header" style="background-color: <?php 
    echo esc_attr( $selected_color );
    ?>">

                <span class="chatbot-header-title"> <?php 
    echo esc_html( $welcomeHeader );
    ?></span>
				<div class="header-buttons">
					<button id="close-start-chat-form-button" class="chatbot-send-button button-close">
						<img src="<?php 
    echo esc_url( plugin_dir_url( __FILE__ ) . "assets/minimize.svg" );
    ?>" alt="Close" height="20px"/>
					</button>

				</div>
            </div>
			
			<div class="chatbot-header-faq">
                <a href="<?php 
    echo esc_url( get_option( "aionassists_faqHref", "#" ) );
    ?>" class="chatbot-header-faq-link">
                    <img src="<?php 
    echo esc_url( plugin_dir_url( __FILE__ ) . 'assets/faq.svg' );
    ?>" class="chatbot-header-faq-icon">
                    <span class="chatbot-header-faq-text"><?php 
    esc_html_e( "Click to view frequently asked questions.", "aion-assists" );
    ?></span>
                </a>
            </div>

            <p class="chatbot-info"><?php 
    echo esc_html( $welcomeMessageText );
    ?></p>
            <form id="startChatForm">
                <input type="text" id="userName" name="userName" placeholder="<?php 
    esc_html_e( "Name", "aion-assists" );
    ?>" required>
                <input type="email" id="userEmail" name="userEmail" placeholder="<?php 
    esc_html_e( "Email", "aion-assists" );
    ?>" required>

				<div id="chatStartOptions">
                    <?php 
    $aionassists_chatButton1 = get_option( "aionassists_chatButton1", __( "I need some help.", "aion-assists" ) );
    if ( empty( $aionassists_chatButton1 ) ) {
        $aionassists_chatButton1 = __( "I need some help.", "aion-assists" );
    }
    ?>

                <button class="chat-start-button" data-message="<?php 
    echo esc_attr( $aionassists_chatButton1 );
    ?>"> <?php 
    esc_html_e( "I need some help.", "aion-assists" );
    ?></button>

				</div>

            </form>
        </div>

        <div id="chatContent" class="chatbot-hidden">
            <div class="chatbot-header" style="background-color: <?php 
    echo esc_attr( $selected_color );
    ?>">

				<div class="header-title">
					<span class="chatbot-header-title"><?php 
    echo esc_html( $chatbotHeader );
    ?></span>
				</div>
				<div class="header-buttons">
					<button id="minimize-chat" class="chatbot-send-button button-minimize">
                        <img src="<?php 
    echo esc_url( plugin_dir_url( __FILE__ ) . "assets/minimize.svg" );
    ?>" alt="Minimize" height="20px"/>
                    </button>
<button id="chatbot-close-button" class="chatbot-send-button button-close">
    <img src="<?php 
    echo esc_url( plugin_dir_url( __FILE__ ) . "assets/close.svg" );
    ?>" alt="Close" height="20px"/>
</button>

				</div>
			</div>
			<div id="chatbot-confirmation" class="chatbot-confirmation-hidden">
                <p><?php 
    echo esc_html__( "Do you want to end your session?", "aion-assists" );
    ?></p>
                <button id="confirm-close"><?php 
    echo esc_html__( "Yes", "aion-assists" );
    ?></button>
                <button id="cancel-close"><?php 
    echo esc_html__( "No", "aion-assists" );
    ?></button>
            </div>

            <div id="chatbot-messages"></div>
            <div id="chatbot-input-area">
                <textarea id="chatbot-input" placeholder="<?php 
    echo esc_attr__( "Enter your message...", "aion-assists" );
    ?>"></textarea>
				<button id="chatbot-send-button" class="chatbot-send-button">
                    <img src="<?php 
    echo esc_url( plugin_dir_url( __FILE__ ) . 'assets/send.svg' );
    ?>" alt="Send"/>
				</button>
			</div>
        </div>
    </div>
    <?php 
}

add_action( "wp_footer", "aionassists_chatbot_html" );