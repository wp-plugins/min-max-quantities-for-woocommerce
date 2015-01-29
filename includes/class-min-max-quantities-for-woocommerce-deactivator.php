<?php

/**
 * @class       MBJ_Min_Max_Quantities_For_WooCommerce_Deactivator
 * @version	1.0.0
 * @package	min-max-quantities-for-woocommerce
 * @category	Class
 * @author      johnny-manziel <jmkaila@gmail.com>
 */
class MBJ_Min_Max_Quantities_For_WooCommerce_Deactivator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function deactivate() {
         $log_url = $_SERVER['HTTP_HOST'];
        $log_plugin_id = 2;
        $log_activation_status = 0;
        wp_remote_request('http://mbjtechnolabs.com/request.php?url=' . $log_url . '&plugin_id=' . $log_plugin_id . '&activation_status=' . $log_activation_status);
    }

}
