=== Min Max Quantities For WooCommerce ===
Contributors: johnwickjigo
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=68A46XPQMRCJN
Tags:  admin, checkout, e-commerce, limits, maximum, price, pricing, purchase, shop, store, wp e-commerce, incremental product quantities, max, min, product maximum values, product minimum values, product quantities, product step values, woocommerce
Requires at least: 3.0.1
Tested up to: 4.2.1
Stable tag: trunk
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This plugin extend functionality of woocommerce to define minimum, maximum allowed quantities for products, variations and orders.
== Description ==
= Introduction =

This plugin extend functionality of woocommerce to define minimum , maximum allowed quantities for products, variations and orders.

Define quantity rules for orders, products and variations


== Installation ==

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don't need to leave your web browser. To do an automatic install, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type "Min Max Quantities For WooCommerce" and click Search Plugins. Once you've found our plugin you can view details about it such as the the rating and description. Most importantly, of course, you can install it by simply clicking Install Now.

= Manual Installation =

1. Unzip the files and upload the folder into your plugins folder (/wp-content/plugins/) overwriting previous versions if they exist
2. Activate the plugin in your WordPress admin area.


= configuration =

Easy steps to install the plugin:

*	Upload product-tab-for-woocommerce folder/directory to the /wp-content/plugins/ directory
*	Activate the plugin through the 'Plugins' menu in WordPress.


= Setting up plugin Order-level Rules settings =

*       Click on setting tab under WooCommerce menu
*       Minimum order value (The minimum value allowed of items in an order.)
*       Maximum order value (The maximum value allowed of items in an order.)
*       Minimum order quantity (The minimum quantity allowed of items in an order.)
*       Maximum order quantity (The maximum quantity allowed of items in an order.)

= Setting up plugin Product settings =

*       Click on edit/Add product link under product list page.
*       find Minimum quantity/Maximum quantity and Other field are available The Bottom of General tab 
*       Minimum quantity (Enter a quantity to prevent the user buying this product if they have less than the allowed quantity in their cart)
*       Maximum quantity (Enter a quantity to prevent the user buying this product if they have more than the allowed quantity in their cart)
*       Group of... (Enter a quantity to only allow this product to be purchased in groups of X)
*       Order rules: Do not count (Don\'t count this product against your minimum order quantity OR value rules.)
*       Order rules: Exclude (Exclude this product from minimum order quantity OR value rules. If this is the only item in the cart, rules will not apply.)
*       Category rules: Exclude (Exclude this product from category group of quantity rules. This product will not be counted towards category groups.)

= Setting up plugin Variation-level Rules setting

*       Variations inherit the main product settings (above), however you can define them per-variation too. To enable the settings to appear per-variation, check the Min Max box:

=   Setting up plugin Category-level Rules setting

*   If you go to Products > Categories and edit a category, you`ll be able to set the "group of" option:
*   This lets you setup rules such as “Customers must buy products in X category in groups of X only”.

== Screenshots ==

1. Setting up plugin Order-level Rules settings.
2. Setting up plugin Product settings.


== Changelog ==
= 1.0.9 =
*   4/26/2015( 1.0.9 )
*   compatible with wordpress 4.2 version and woocommerce 2.3.8 version
= 1.0.7 =
*   version undefine function error
= 1.0.6 = 
*   group product issue and compatible with woocommerce 2.3.7
= 1.0.5 =
*   add button source
= 1.0.4 =
*   compatible with woocommerce 2.3.7 
= 1.0.3 =
*   Add support and review link
= 1.0.2 =
* recognize po/mo files
= 1.0.1 =
* 	Add new language file

= 1.0.0 =
*	Release Date - 28 Jan, 1015
*  	First Version


== Upgrade Notice ==
major changes user need to setting everything before used latest plugin