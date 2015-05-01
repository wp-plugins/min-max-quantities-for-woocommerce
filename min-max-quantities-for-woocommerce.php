<?php

/**
 *
 * @wordpress-plugin
 * Plugin Name:       Min Max Quantities For WooCommerce
 * Plugin URI:        http://webs-spider.com/
 * Description:       This plugin extend functionality of woocommerce to define minimum , maximum allowed quantities for products, variations and orders.
 * Version:           1.2.0
 * Author:            johnwickjigo
 * Author URI:        http://www.mbjtechnolabs.com
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       min-max-quantities-for-woocommerce
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * define plugin basename
 */
if (!defined('MMQW_PLUGIN_BASENAME')) {
    define('MMQW_PLUGIN_BASENAME', plugin_basename(__FILE__));
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-min-max-quantities-for-woocommerce-activator.php
 */
function activate_plugin_name() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-min-max-quantities-for-woocommerce-activator.php';
    MBJ_Min_Max_Quantities_For_WooCommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-min-max-quantities-for-woocommerce-deactivator.php
 */
function deactivate_plugin_name() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-min-max-quantities-for-woocommerce-deactivator.php';
    MBJ_Min_Max_Quantities_For_WooCommerce_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_plugin_name');
register_deactivation_hook(__FILE__, 'deactivate_plugin_name');

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-min-max-quantities-for-woocommerce.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_plugin_name() {

    $plugin = new MBJ_Min_Max_Quantities_For_WooCommerce();
    $plugin->run();
}

add_action('plugins_loaded', 'woocommerce_min_max_quantities_init', 0);

function woocommerce_min_max_quantities_init() {
    run_plugin_name();
}
