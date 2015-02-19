<?php

/**
 * @class       MBJ_Min_Max_Quantities_For_WooCommerce
 * @version	1.0.0
 * @package	min-max-quantities-for-woocommerce
 * @category	Class
 * @author      johnny-manziel <jmkaila@gmail.com>
 */
class MBJ_Min_Max_Quantities_For_WooCommerce {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      MBJ_Min_Max_Quantities_For_WooCommerce_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the Dashboard and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    var $minimum_order_quantity;
    var $maximum_order_quantity;
    var $minimum_order_value;
    var $maximum_order_value;
    var $excludes = array();

    public function __construct() {

        $this->plugin_name = 'min-max-quantities-for-woocommerce';
        $this->version = '1.0.3';

        $this->minimum_order_quantity = get_option('min_max_quantities_for_woocommerce_minimum_order_quantity');
        $this->maximum_order_quantity = get_option('min_max_quantities_for_woocommerce_maximum_order_quantity');
        $this->minimum_order_value = get_option('min_max_quantities_for_woocommerce_minimum_order_value');
        $this->maximum_order_value = get_option('min_max_quantities_for_woocommerce_maximum_order_value');


        add_action('woocommerce_check_cart_items', array($this, 'woocommerce_check_cart_items_own'));


        add_filter('woocommerce_quantity_input_args', array($this, 'woocommerce_quantity_input_args_own'), 10, 2);
        add_filter('woocommerce_quantity_input_max', array($this, 'woocommerce_quantity_input_max_own'), 10, 2);
        add_filter('woocommerce_quantity_input_min', array($this, 'woocommerce_quantity_input_min_own'), 10, 2);
        add_filter('woocommerce_quantity_input_step', array($this, 'woocommerce_quantity_input_step_own'), 10, 2);
        add_filter('woocommerce_available_variation', array($this, 'woocommerce_available_variation_own'), 10, 3);


        add_filter('woocommerce_add_to_cart_validation', array($this, 'woocommerce_add_to_cart_validation_own'), 10, 4);


        add_filter('woocommerce_loop_add_to_cart_link', array($this, 'woocommerce_loop_add_to_cart_link_own'), 10, 2);

         $woocommerce_paypal_settings = get_option('woocommerce_paypal_settings');
        
        if(isset($woocommerce_paypal_settings['enabled']) && $woocommerce_paypal_settings['enabled'] == 'yes') {
            
            add_filter('woocommerce_paypal_args', array(__CLASS__, 'paypal_ipn_for_wordpress_standard_parameters'), 10, 1);

        }


        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();

        $prefix = is_network_admin() ? 'network_admin_' : '';
        add_filter("{$prefix}plugin_action_links_" . MMQW_PLUGIN_BASENAME, array($this, 'plugin_action_links'), 10, 1);
    }
    
    public static function paypal_ipn_for_wordpress_standard_parameters($paypal_args){
        $paypal_args['bn'] = 'mbjtechnolabs_SP';
        return $paypal_args;
    }

    public function plugin_action_links($actions, $plugin_file, $plugin_data, $context) {
        $custom_actions = array(
            'support' => sprintf('<a href="%s" target="_blank">%s</a>', 'http://wordpress.org/support/plugin/min-max-quantities-for-woocommerce/', __('Support', 'min-max-quantities-for-woocommerce')),
            'review' => sprintf('<a href="%s" target="_blank">%s</a>', 'http://wordpress.org/support/view/plugin-reviews/min-max-quantities-for-woocommerce/', __('Write a Review', 'min-max-quantities-for-woocommerce')),
        );

        return array_merge($custom_actions, $actions);
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - MBJ_Min_Max_Quantities_For_WooCommerce_Loader. Orchestrates the hooks of the plugin.
     * - MBJ_Min_Max_Quantities_For_WooCommerce_i18n. Defines internationalization functionality.
     * - MBJ_Min_Max_Quantities_For_WooCommerce_Admin. Defines all hooks for the dashboard.
     * - MBJ_Min_Max_Quantities_For_WooCommerce_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-min-max-quantities-for-woocommerce-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-min-max-quantities-for-woocommerce-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the Dashboard.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-min-max-quantities-for-woocommerce-admin.php';


        $this->loader = new MBJ_Min_Max_Quantities_For_WooCommerce_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the MBJ_Min_Max_Quantities_For_WooCommerce_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new MBJ_Min_Max_Quantities_For_WooCommerce_i18n();
        $plugin_i18n->set_domain($this->get_plugin_name());

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the dashboard functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new MBJ_Min_Max_Quantities_For_WooCommerce_Admin($this->get_plugin_name(), $this->get_version());
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * @since    1.0.0
     * @return void
     */
    function woocommerce_quantity_input_step_own($data, $product) {
        if (is_cart())
            return $data;

        $minimum_quantity = get_post_meta($product->id, 'minimum_allowed_quantity', true);
        $group_of_quantity = get_post_meta($product->id, 'group_of_quantity', true);

        if ($group_of_quantity && (!$minimum_quantity || $minimum_quantity % $group_of_quantity == 0 )) {
            return $group_of_quantity;
        }

        return $data;
    }

    /**
     * @since    1.0.0
     * @access public
     * @return void
     */
    public function min_max_quantities_for_woocommerce_check_rules($product, $quantity, $minimum_quantity, $maximum_quantity, $group_of_quantity) {
        global $woocommerce;

        if ($minimum_quantity > 0 && $quantity < $minimum_quantity) {

            $this->add_error(sprintf(__('The minimum allowed quantity for %s is %s - please increase the quantity in your cart.', 'min-max-quantities-for-woocommerce'), $product->get_title(), $minimum_quantity));
        } elseif ($maximum_quantity > 0 && $quantity > $maximum_quantity) {

            $this->add_error(sprintf(__('The maximum allowed quantity for %s is %s - please decrease the quantity in your cart.', 'min-max-quantities-for-woocommerce'), $product->get_title(), $maximum_quantity));
        }

        if ($group_of_quantity > 0 && ( $quantity % $group_of_quantity )) {

            $this->add_error(sprintf(__('%s must be bought in groups of %d. Please add another %d to continue.', 'min-max-quantities-for-woocommerce'), $product->get_title(), $group_of_quantity, $group_of_quantity - ( $quantity % $group_of_quantity )));
        }
    }

    /**
     * @return void
     */
    function woocommerce_available_variation_own($data, $product, $variation) {
        $min_max_rules = get_post_meta($variation->variation_id, 'min_max_rules', true);

        if ($min_max_rules == 'yes')
            $checking_id = $variation->variation_id;
        else
            $checking_id = $variation->id;

        $minimum_quantity = get_post_meta($checking_id, 'minimum_allowed_quantity', true);
        $maximum_quantity = get_post_meta($checking_id, 'maximum_allowed_quantity', true);
        $group_of_quantity = get_post_meta($checking_id, 'group_of_quantity', true);

        // Only enforce min qty if set at variation level
        if ($min_max_rules == 'yes' && $minimum_quantity) {
            $data['min_qty'] = $minimum_quantity;
        } elseif ($group_of_quantity) {
            $data['min_qty'] = 0; // 0 so steps work correctly
        } else {
            $data['min_qty'] = 1;
        }

        if ($maximum_quantity) {
            $data['max_qty'] = $maximum_quantity;
        }

        return $data;
    }

    /**
     * @since    1.0.0
     * @access public
     * @return void
     */
    public function woocommerce_add_to_cart_validation_own($pass, $product_id, $quantity, $variation_id = 0) {
        global $woocommerce;

        $rule_for_variaton = false;

        if ($variation_id) {

            $min_max_rules = get_post_meta($variation_id, 'min_max_rules', true);

            if ($min_max_rules == 'yes') {

                $maximum_quantity = get_post_meta($variation_id, 'maximum_allowed_quantity', true);
                $rule_for_variaton = true;
            } else {

                $maximum_quantity = get_post_meta($product_id, 'maximum_allowed_quantity', true);
            }
        } else {

            $maximum_quantity = get_post_meta($product_id, 'maximum_allowed_quantity', true);
        }

        if (!$maximum_quantity)
            return $pass;

        $total_quantity = $quantity;


        foreach ($woocommerce->cart->get_cart() as $cart_item_key => $values) {
            if ($rule_for_variaton) {
                if ($values['variation_id'] == $variation_id) {
                    $total_quantity += $values['quantity'];
                }
            } else {
                if ($values['product_id'] == $product_id) {
                    $total_quantity += $values['quantity'];
                }
            }
        }

        if ($total_quantity > 0 && $total_quantity > $maximum_quantity) {

            if (function_exists('get_product')) {
                $_product = get_product($product_id);
            } else {
                $_product = new WC_Product($product_id);
            }

            $this->add_error(sprintf(__('The maximum allowed quantity for %s is %d (you currently have %s in your cart).', 'min-max-quantities-for-woocommerce'), $_product->get_title(), $maximum_quantity, $total_quantity - $quantity));

            $pass = false;
        }

        return $pass;
    }

    /**
     * @since    1.0.0
     * @access public
     * @return void
     */
    function woocommerce_quantity_input_args_own($data, $theproduct = null) {
        if (!is_singular('product')) {
            return $data;
        }
        if (is_null($theproduct)) {
            $theproduct = $product;
            global $product;
        }

        $group_of_quantity = get_post_meta($theproduct->id, 'group_of_quantity', true);
        if ($group_of_quantity) {
            $data['input_value'] = 0;
        }
        return $data;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    MBJ_Min_Max_Quantities_For_WooCommerce_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * @since    1.0.0
     */
    public function add_error($error) {
        if (function_exists('wc_add_notice')) {
            wc_add_notice($error, 'error');
        } else {
            global $woocommerce;
            $woocommerce->add_error($error);
        }
    }

    /**
     * @since    1.0.0
     * @return void
     */
    function woocommerce_quantity_input_min_own($data, $product) {
        if ($product->variation_id)
            $check_id = $product->variation_id;
        else
            $check_id = $product->id;

        $minimum_quantity = get_post_meta($check_id, 'minimum_allowed_quantity', true);
        if ($minimum_quantity) {
            return $minimum_quantity;
        }
        $group_of_quantity = get_post_meta($check_id, 'group_of_quantity', true);
        if ($group_of_quantity) {
            return $group_of_quantity;
        }
        return $data;
    }

    /**
     * @since    1.0.0
     *
     * @return void
     */
    function woocommerce_quantity_input_max_own($data, $product) {
        if ($product->variation_id)
            $check_id = $product->variation_id;
        else
            $check_id = $product->id;

        $maximum_quantity = get_post_meta($check_id, 'maximum_allowed_quantity', true);
        if ($maximum_quantity) {
            return $maximum_quantity;
        }
        return $data;
    }

    /**
     * @since    1.0.0
     * @access public
     * @return void
     */
    public function woocommerce_loop_add_to_cart_link_own($html, $product) {
        global $woocommerce;

        $quantity_attribute = 1;
        $minimum_quantity = get_post_meta($product->id, 'minimum_allowed_quantity', true);
        $group_of_quantity = get_post_meta($product->id, 'group_of_quantity', true);

        if ($minimum_quantity || $group_of_quantity) {

            $quantity_attribute = $minimum_quantity;

            if ($group_of_quantity > 0 && $minimum_quantity < $group_of_quantity)
                $quantity_attribute = $group_of_quantity;

            $html = str_replace('<a ', '<a data-quantity="' . $quantity_attribute . '"', $html);
        }

        return $html;
    }

    /**
     * @since    1.0.0
     * @access public
     * @return void
     */
    public function woocommerce_check_cart_items_own() {
        global $woocommerce;

        $checked_ids = $product_quantities = $category_quantities = array();
        $total_quantity = $total_cost = 0;
        $apply_cart_rules = false;


        foreach ($woocommerce->cart->get_cart() as $cart_item_key => $values) {

            if (!isset($product_quantities[$values['product_id']]))
                $product_quantities[$values['product_id']] = 0;

            if ($values['variation_id']) {

                if (!isset($product_quantities[$values['variation_id']]))
                    $product_quantities[$values['variation_id']] = 0;

                $min_max_rules = get_post_meta($values['variation_id'], 'min_max_rules', true);

                if ($min_max_rules == 'yes')
                    $product_quantities[$values['variation_id']] += $values['quantity'];
                else
                    $product_quantities[$values['product_id']] += $values['quantity'];
            } else {
                $product_quantities[$values['product_id']] += $values['quantity'];
            }
        }


        foreach ($woocommerce->cart->get_cart() as $cart_item_key => $values) {

            if ($values['variation_id']) {
                $min_max_rules = get_post_meta($values['variation_id'], 'min_max_rules', true);

                if ($min_max_rules == 'yes')
                    $checking_id = $values['variation_id'];
                else
                    $checking_id = $values['product_id'];
            } else {
                $checking_id = $values['product_id'];
            }


            $terms = get_the_terms($values['product_id'], 'product_cat');
            $found_term_ids = array();

            if ($terms) {
                foreach ($terms as $term) {

                    if ('yes' == get_post_meta($checking_id, 'minmax_category_group_of_exclude', true))
                        continue;

                    if (in_array($term->term_id, $found_term_ids))
                        continue;

                    $found_term_ids[] = $term->term_id;
                    $category_quantities[$term->term_id] = isset($category_quantities[$term->term_id]) ? $category_quantities[$term->term_id] + $values['quantity'] : $values['quantity'];


                    $parents = get_ancestors($term->term_id, 'product_cat');

                    foreach ($parents as $parent) {
                        if (in_array($parent, $found_term_ids))
                            continue;

                        $found_term_ids[] = $parent;
                        $category_quantities[$parent] = isset($category_quantities[$parent]) ? $category_quantities[$parent] + $values['quantity'] : $values['quantity'];
                    }
                }
            }


            if (in_array($checking_id, $checked_ids))
                continue;

            $product = $values['data'];


            $minmax_do_not_count = apply_filters('wc_min_max_quantity_minmax_do_not_count', get_post_meta($checking_id, 'minmax_do_not_count', true), $checking_id, $cart_item_key, $values);
            $minmax_cart_exclude = apply_filters('wc_min_max_quantity_minmax_cart_exclude', get_post_meta($checking_id, 'minmax_cart_exclude', true), $checking_id, $cart_item_key, $values);

            if ($minmax_do_not_count == 'yes' || $minmax_cart_exclude == 'yes') {

                $this->excludes[] = $product->get_title();
            } else {
                $total_quantity += $product_quantities[$checking_id];
                $total_cost += $product->get_price() * $product_quantities[$checking_id];
            }

            if ($minmax_cart_exclude != 'yes')
                $apply_cart_rules = true;

            $checked_ids[] = $checking_id;

            $minimum_quantity = apply_filters('wc_min_max_quantity_minimum_allowed_quantity', get_post_meta($checking_id, 'minimum_allowed_quantity', true), $checking_id, $cart_item_key, $values);
            $maximum_quantity = apply_filters('wc_min_max_quantity_maximum_allowed_quantity', get_post_meta($checking_id, 'maximum_allowed_quantity', true), $checking_id, $cart_item_key, $values);
            $group_of_quantity = apply_filters('wc_min_max_quantity_group_of_quantity', get_post_meta($checking_id, 'group_of_quantity', true), $checking_id, $cart_item_key, $values);

            $this->min_max_quantities_for_woocommerce_check_rules($product, $product_quantities[$checking_id], $minimum_quantity, $maximum_quantity, $group_of_quantity);
        }


        if ($apply_cart_rules) {

            $excludes = '';

            if (sizeof($this->excludes) > 0) {
                $excludes = ' (' . __('excludes ', 'min-max-quantities-for-woocommerce') . implode(', ', $this->excludes) . ')';
            }


            $quantity = $this->minimum_order_quantity;
            if ($quantity > 0 && $total_quantity < $quantity) {
                $this->add_error(sprintf(__('The minimum allowed order quantity is %s - please add more items to your cart', 'min-max-quantities-for-woocommerce'), $quantity) . $excludes);

                return;
            }

            $quantity = $this->maximum_order_quantity;
            if ($quantity > 0 && $total_quantity > $quantity) {

                $this->add_error(sprintf(__('The maximum allowed order quantity is %s - please remove some items from your cart.', 'min-max-quantities-for-woocommerce'), $quantity));

                return;
            }


            if ($this->minimum_order_value && $total_cost && $total_cost < $this->minimum_order_value) {

                $this->add_error(sprintf(__('The minimum allowed order value is %s - please add more items to your cart', 'min-max-quantities-for-woocommerce'), woocommerce_price($this->minimum_order_value)) . $excludes);

                return;
            }

            if ($this->maximum_order_value && $total_cost && $total_cost > $this->maximum_order_value) {

                $this->add_error(sprintf(__('The maximum allowed order value is %s - please remove some items from your cart.', 'min-max-quantities-for-woocommerce'), woocommerce_price($this->maximum_order_value)));

                return;
            }
        }


        foreach ($category_quantities as $category => $quantity) {
            $group_of_quantity = get_woocommerce_term_meta($category, 'group_of_quantity', true);

            if ($group_of_quantity > 0 && ( $quantity % $group_of_quantity ) > 0) {

                $term = get_term_by('id', $category, 'product_cat');
                $product_names = array();

                foreach ($woocommerce->cart->get_cart() as $cart_item_key => $values) {
                    if ('yes' == get_post_meta($values['product_id'], 'minmax_category_group_of_exclude', true) || 'yes' == get_post_meta($values['variation_id'], 'minmax_category_group_of_exclude', true))
                        continue;
                    if (has_term($category, 'product_cat', $values['product_id'])) {
                        $product_names[] = $values['data']->get_title();
                    }
                }

                $this->add_error(sprintf(__('Items in the <strong>%s</strong> category (<em>%s</em>) must be bought in groups of %d. Please add another %d to continue.', 'min-max-quantities-for-woocommerce'), $term->name, implode(', ', $product_names), $group_of_quantity, $group_of_quantity - ( $quantity % $group_of_quantity )));

                return;
            }
        }
    }

}

