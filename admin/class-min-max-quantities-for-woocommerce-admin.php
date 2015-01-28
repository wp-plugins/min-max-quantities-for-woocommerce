<?php

/**
 * @class       MBJ_Min_Max_Quantities_For_WooCommerce_Admin
 * @version	1.0.0
 * @package	min-max-quantities-for-woocommerce
 * @category	Class
 * @author      johnny-manziel <jmkaila@gmail.com>
 */
class MBJ_Min_Max_Quantities_For_WooCommerce_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     *
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        $this->settings = array(
            array('name' => __('Min Max Value', 'min-max-quantities-for-woocommerce'), 'type' => 'title', 'desc' => '', 'id' => 'minmax_quantity_options'),
            array(
                'name' => __('Minimum order value', 'min-max-quantities-for-woocommerce'),
                'desc_tip' => __('The minimum value allowed of items in an order.', 'min-max-quantities-for-woocommerce'),
                'id' => 'min_max_quantities_for_woocommerce_minimum_order_value',
                'type' => 'text'
            ),
            array(
                'name' => __('Maximum order value', 'min-max-quantities-for-woocommerce'),
                'desc_tip' => __('The maximum value allowed of items in an order.', 'min-max-quantities-for-woocommerce'),
                'id' => 'min_max_quantities_for_woocommerce_maximum_order_value',
                'type' => 'text'
            ),
            array(
                'name' => __('Minimum order quantity', 'min-max-quantities-for-woocommerce'),
                'desc_tip' => __('The minimum quantity allowed of items in an order.', 'min-max-quantities-for-woocommerce'),
                'id' => 'min_max_quantities_for_woocommerce_minimum_order_quantity',
                'type' => 'text'
            ),
            array(
                'name' => __('Maximum order quantity', 'min-max-quantities-for-woocommerce'),
                'desc_tip' => __('The maximum quantity allowed of items in an order.', 'min-max-quantities-for-woocommerce'),
                'id' => 'min_max_quantities_for_woocommerce_maximum_order_quantity',
                'type' => 'text'
            ),
            array('type' => 'sectionend', 'id' => 'minmax_quantity_options'),
        );


        add_action('woocommerce_settings_general_options_after', array(&$this, 'woocommerce_settings_general_options_after_own'));
        add_action('woocommerce_update_options_general', array(&$this, 'woocommerce_update_options_general_own'));


        add_action('woocommerce_variation_options', array(&$this, 'woocommerce_variation_options_own'), 10, 2);
        add_action('woocommerce_product_after_variable_attributes', array(&$this, 'woocommerce_product_after_variable_attributes_own'), 10, 2);


        add_action('woocommerce_product_options_general_product_data', array(&$this, 'woocommerce_product_options_general_product_data_own'));
        add_action('woocommerce_process_product_meta', array(&$this, 'woocommerce_process_product_meta_own'));


        add_action('created_term', array($this, 'category_fields_save'), 10, 3);
        add_action('edit_term', array($this, 'category_fields_save'), 10, 3);
        add_action('product_cat_edit_form_fields', array($this, 'edit_category_fields'), 10, 2);
        add_action('product_cat_add_form_fields', array($this, 'add_category_fields'));
        add_filter('manage_edit-product_cat_columns', array($this, 'product_cat_columns'));
        add_filter('manage_product_cat_custom_column', array($this, 'product_cat_column'), 10, 3);
    }

    /**
     * @since    1.0.0
     * @access public
     * @return void
     */
    function woocommerce_settings_general_options_after_own() {
        woocommerce_admin_fields($this->settings);
    }

    /**
     * @since    1.0.0
     * @access public
     * @return void
     */
    function woocommerce_update_options_general_own() {
        woocommerce_update_options($this->settings);
    }

    /**
     * @since    1.0.0
     * @access public
     * @return void
     */
    function woocommerce_product_options_general_product_data_own() {
        global $woocommerce;

        echo '<div class="options_group">';

        woocommerce_wp_text_input(array('id' => 'minimum_allowed_quantity', 'label' => __('Minimum quantity', 'min-max-quantities-for-woocommerce'), 'description' => __('Enter a quantity to prevent the user buying this product if they have less than the allowed quantity in their cart', 'min-max-quantities-for-woocommerce'), 'desc_tip' => true));

        woocommerce_wp_text_input(array('id' => 'maximum_allowed_quantity', 'label' => __('Maximum quantity', 'min-max-quantities-for-woocommerce'), 'description' => __('Enter a quantity to prevent the user buying this product if they have more than the allowed quantity in their cart', 'min-max-quantities-for-woocommerce'), 'desc_tip' => true));

        woocommerce_wp_text_input(array('id' => 'group_of_quantity', 'label' => __('Group of...', 'min-max-quantities-for-woocommerce'), 'description' => __('Enter a quantity to only allow this product to be purchased in groups of X', 'min-max-quantities-for-woocommerce'), 'desc_tip' => true));

        woocommerce_wp_checkbox(array('id' => 'minmax_do_not_count', 'label' => __('Order rules: Do not count', 'min-max-quantities-for-woocommerce'), 'description' => __('Don\'t count this product against your minimum order quantity OR value rules.', 'min-max-quantities-for-woocommerce')));

        woocommerce_wp_checkbox(array('id' => 'minmax_cart_exclude', 'label' => __('Order rules: Exclude', 'min-max-quantities-for-woocommerce'), 'description' => __('Exclude this product from minimum order quantity OR value rules. If this is the only item in the cart, rules will not apply.', 'min-max-quantities-for-woocommerce')));

        woocommerce_wp_checkbox(array('id' => 'minmax_category_group_of_exclude', 'label' => __('Category rules: Exclude', 'min-max-quantities-for-woocommerce'), 'description' => __('Exclude this product from category group of quantity rules. This product will not be counted towards category groups.', 'min-max-quantities-for-woocommerce')));

        echo '</div>';

        $js = "
    		jQuery('.checkbox.min_max_rules').live( 'change', function() {

    			if ( jQuery(this).is( ':checked' ) ) {

    				jQuery(this).closest('.woocommerce_variation').find( 'tr.min_max_rules' ).show();

    			} else {

    				jQuery(this).closest('.woocommerce_variation').find( 'tr.min_max_rules' ).hide();

    			}

    		}).change();
    	";

        if (function_exists('wc_enqueue_js')) {
            wc_enqueue_js($js);
        } else {
            $woocommerce->add_inline_js($js);
        }
    }

    /**
     * @since    1.0.0
     * @access public
     * @return void
     */
    function woocommerce_process_product_meta_own($post_id) {

        if (isset($_POST['minimum_allowed_quantity']))
            update_post_meta($post_id, 'minimum_allowed_quantity', esc_attr($_POST['minimum_allowed_quantity']));

        if (isset($_POST['maximum_allowed_quantity']))
            update_post_meta($post_id, 'maximum_allowed_quantity', esc_attr($_POST['maximum_allowed_quantity']));

        if (isset($_POST['group_of_quantity']))
            update_post_meta($post_id, 'group_of_quantity', esc_attr($_POST['group_of_quantity']));

        update_post_meta($post_id, 'minmax_do_not_count', empty($_POST['minmax_do_not_count']) ? 'no' : 'yes' );

        update_post_meta($post_id, 'minmax_cart_exclude', empty($_POST['minmax_cart_exclude']) ? 'no' : 'yes' );

        update_post_meta($post_id, 'minmax_category_group_of_exclude', empty($_POST['minmax_category_group_of_exclude']) ? 'no' : 'yes' );

        if (isset($_POST['variable_post_id'])) {

            $variable_post_id = $_POST['variable_post_id'];
            $min_max_rules = $_POST['min_max_rules'];
            $minimum_allowed_quantity = $_POST['variation_minimum_allowed_quantity'];
            $maximum_allowed_quantity = $_POST['variation_maximum_allowed_quantity'];
            $group_of_quantity = $_POST['variation_group_of_quantity'];
            $minmax_do_not_count = $_POST['variation_minmax_do_not_count'];
            $minmax_cart_exclude = $_POST['variation_minmax_cart_exclude'];
            $minmax_category_group_of_exclude = $_POST['variation_minmax_category_group_of_exclude'];

            $max_loop = max(array_keys($_POST['variable_post_id']));

            for ($i = 0; $i <= $max_loop; $i++) {

                if (!isset($variable_post_id[$i]))
                    continue;

                $variation_id = absint($variable_post_id[$i]);

                if (isset($min_max_rules[$i])) {
                    update_post_meta($variation_id, 'min_max_rules', 'yes');
                    update_post_meta($variation_id, 'minimum_allowed_quantity', $minimum_allowed_quantity[$i]);
                    update_post_meta($variation_id, 'maximum_allowed_quantity', $maximum_allowed_quantity[$i]);
                    update_post_meta($variation_id, 'group_of_quantity', $group_of_quantity[$i]);

                    if (isset($minmax_do_not_count[$i]))
                        update_post_meta($variation_id, 'minmax_do_not_count', 'yes');
                    else
                        update_post_meta($variation_id, 'minmax_do_not_count', 'no');

                    if (isset($minmax_cart_exclude[$i]))
                        update_post_meta($variation_id, 'minmax_cart_exclude', 'yes');
                    else
                        update_post_meta($variation_id, 'minmax_cart_exclude', 'no');

                    if (isset($minmax_category_group_of_exclude[$i]))
                        update_post_meta($variation_id, 'minmax_category_group_of_exclude', 'yes');
                    else
                        update_post_meta($variation_id, 'minmax_category_group_of_exclude', 'no');
                } else {
                    update_post_meta($variation_id, 'min_max_rules', 'no');
                }
            }
        }
    }

    /**
     * @since    1.0.0
     * @access public
     * @return void
     */
    function woocommerce_variation_options_own($loop, $variation_data) {
        ?>
        <label><input type="checkbox" class="checkbox min_max_rules" name="min_max_rules[<?php echo $loop; ?>]" <?php if (isset($variation_data['min_max_rules'][0])) checked($variation_data['min_max_rules'][0], 'yes') ?> /> <?php _e('Min/Max Rules', 'woocommerce'); ?> <a class="tips" data-tip="<?php _e('Enable this option to override min/max settings at variation level', 'min-max-quantities-for-woocommerce'); ?>" href="#">[?]</a></label>
        <?php
    }

    /**
     * @since    1.0.0
     * @access public
     * @return void
     */
    function woocommerce_product_after_variable_attributes_own($loop, $variation_data) {
        ?>
        <tr class="min_max_rules" style="display:none">
            <td>
                <label><?php _e('Minimum quantity', 'min-max-quantities-for-woocommerce'); ?></label>
                <input type="number" size="5" name="variation_minimum_allowed_quantity[<?php echo $loop; ?>]" value="<?php if (isset($variation_data['minimum_allowed_quantity'][0])) echo $variation_data['minimum_allowed_quantity'][0]; ?>" />
            </td>
            <td>
                <label><?php _e('Maximum quantity', 'min-max-quantities-for-woocommerce'); ?> <input type="text" size="5" name="variation_maximum_allowed_quantity[<?php echo $loop; ?>]" value="<?php if (isset($variation_data['maximum_allowed_quantity'][0])) echo $variation_data['maximum_allowed_quantity'][0]; ?>" />
            </td>
        </tr>
        <tr class="min_max_rules" style="display:none">
            <td>
                <label><?php _e('Group of...', 'min-max-quantities-for-woocommerce'); ?></label>
                <input type="number" size="5" name="variation_group_of_quantity[<?php echo $loop; ?>]" value="<?php if (isset($variation_data['group_of_quantity'][0])) echo $variation_data['group_of_quantity'][0]; ?>" />
            </td>
            <td>

                <label><input type="checkbox" class="checkbox" name="variation_minmax_do_not_count[<?php echo $loop; ?>]" <?php if (isset($variation_data['minmax_do_not_count'][0])) checked($variation_data['minmax_do_not_count'][0], 'yes') ?> /> <?php _e('Order rules: Do not count', 'min-max-quantities-for-woocommerce'); ?></label>

                <label><input type="checkbox" class="checkbox" name="variation_minmax_cart_exclude[<?php echo $loop; ?>]" <?php if (isset($variation_data['minmax_cart_exclude'][0])) checked($variation_data['minmax_cart_exclude'][0], 'yes') ?> /> <?php _e('Order rules: Exclude', 'min-max-quantities-for-woocommerce'); ?></label>

                <label><input type="checkbox" class="checkbox" name="variation_minmax_category_group_of_exclude[<?php echo $loop; ?>]" <?php if (isset($variation_data['minmax_category_group_of_exclude'][0])) checked($variation_data['minmax_category_group_of_exclude'][0], 'yes') ?> /> <?php _e('Category group-of rules: Exclude', 'min-max-quantities-for-woocommerce'); ?></label>

            </td>
        </tr>
        <?php
    }

    /**
     * @since    1.0.0
     * @access public
     * @return void
     */
    function add_category_fields() {
        global $woocommerce;
        ?>
        <div class="form-field">
            <label><?php _e('Group of...', 'min-max-quantities-for-woocommerce'); ?></label>
            <input type="number" size="5" name="group_of_quantity" />
            <p class="description"><?php _e('Enter a quantity to only allow products in this category to be purchased in groups of X', 'min-max-quantities-for-woocommerce'); ?></p>
        </div>
        <?php
    }

    /**
     * @since    1.0.0
     * @access public
     * @return void
     */
    function edit_category_fields($term, $taxonomy) {
        global $woocommerce;

        $display_type = get_woocommerce_term_meta($term->term_id, 'group_of_quantity', true);
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label><?php _e('Group of...', 'min-max-quantities-for-woocommerce'); ?></label></th>
            <td>
                <input type="number" size="5" name="group_of_quantity" value="<?php echo $display_type; ?>" />
                <p class="description"><?php _e('Enter a quantity to only allow products in this category to be purchased in groups of X', 'min-max-quantities-for-woocommerce'); ?></p>
            </td>
        </tr>
        <?php
    }

    /**
     * @since    1.0.0
     * @access public
     * @return void
     */
    function category_fields_save($term_id, $tt_id, $taxonomy) {
        if (isset($_POST['group_of_quantity']))
            update_woocommerce_term_meta($term_id, 'group_of_quantity', esc_attr($_POST['group_of_quantity']));
    }

    /**
     * @since    1.0.0
     * @access public
     * @param mixed $columns
     * @return void
     */
    function product_cat_columns($columns) {
        $columns['groupof'] = __('Purchasable in...', 'min-max-quantities-for-woocommerce');

        return $columns;
    }

    /**
     * @since    1.0.0
     * @return void
     */
    function product_cat_column($columns, $column, $id) {
        global $woocommerce;

        if ($column == 'groupof') {
            if ($groupof = get_woocommerce_term_meta($id, 'group_of_quantity', true))
                $columns .= __('Groups of', 'min-max-quantities-for-woocommerce') . ' ' . absint($groupof);
            else
                $columns .= '&ndash;';
        }

        return $columns;
    }

}

