=== Integration to NettiX for WooCommerce ===
Contributors: orwokki, tmigi
Tags: woocommerce, integration, nettix, nettiauto, nettivene
Stable tag: 2.0.0
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Plugin for downloading sales ads from various NettiX services and adding them as external products to WooCommerce

== Description ==

With this plugin you can download your sales ads from various NettiX services like Nettiauto, Nettivene, Nettimoto, Nettikaravaani, and Nettikone to be added as external products to WooCommerce.

Plugin creates automatically product categories for each service so created external products can be listed by NettiX service.
Plugin also adds product tags for sales location and vehicle make so those can also be used when listing products to website.

To use this plugin you need to be customer of NettiX and get needed API credentials from them to list ads YOU have added to NettiX services.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/wc-integration-to-nettix` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Navigate to WooCommerce -> Settings -> Integration -> Integration to NettiX
1. Add Client ID, Client Secret, and User IDs given by NettiX to their correct fields.
1. Tick checkboxes of those services where you have ads in NettiX
1. Click Save Changes button
1. Wait for plugin to download ads from NettiX and convert them to external products in WooCommerce

Plugin is scheduled to fetch new ads and update existing ads once per hour. Initial download may take time, especially if ads have many images. So be patient.

== Screenshots ==

1. Settings page for plugin under WooCommerce integrations
1. Example list of downloaded external products

== Changelog ==

= 2.0.0 =
* Totally redesigned ads fetching and importing
* Some of the data from NettiX ads are added to custom fields in WooCommerce products
* Some of the fields from NettiX ads can be used as badge in the WooCommerce product card
* Ads are now removed when they are not seen in results set queried from NettiX API
* When plugin removes ad the images associated to it are also removed from media library

= 1.3.2 =
* Tested to work with WordPress 6.6 and WooCommerce 9.1
* Dropped support for PHP 7.4. All future releases support only PHP 8.x versions.

= 1.3.1 =
* Tested to work with WordPress 6.5 and WooCommerce 8.7
* Added new Requires Plugins header with value woocommerce

= 1.3.0 =
* Ability to add location (town/city) to short description
* Labels to fields in product short description
* Minor bug fixes

= 1.2.0 =
* Location (town/city) is added as tag to product
* Population of short description can be managed from integration settings
* Maker name is added as product category and attached to product
* In Nettikaravaani interface, vehicle type is added as tag to product
* In Nettivene interface, boat type and boat sub-type are added as tag to product
* Tested to work with WooCommerce 8.6

= 1.1.2 =
* Fix to bug in connection testing. Bug resulted 403 forbidden error in some cases.

= 1.1.1 =
* To fix wp.org deployment package to contain all files

= 1.1.0 =
* Added Nettikaravaani and Nettikone as possible sources for ads
* Tested to work with WooCommerce 8.5

= 1.0.7 =
* Tested to work with WordPress 6.4 and WooCommerce 8.3

= 1.0.6 =
* Tested to work with WooCommerce 8.2
* More logging added to fetching ads and ad images

= 1.0.5 =
* Tested to work with WordPress 6.3 and WooCommerce 8.0

= 1.0.4 =
* Tested to work with WordPress 6.2 and WooCommerce 7.5

= 1.0.3 =
* Implemented test connection feature to also test fetching ads

= 1.0.2 =
* Added ability to test correctness of credentials to NettiX API
* Plugin tested to work with WooCommerce 7.4
* Fixed how plugin activation fails in case WooCommerce is not installed/activated

= 1.0.1 =
* Plugin tested to work with WordPress 6.1 and WooCommerce 7.1

= 1.0 =
* Initial production version

== Upgrade notice ==

= 2.0.0 =
* Totally redesigned ads fetching and importing
* Some of the data from NettiX ads are added to custom fields in WooCommerce products
* Some of the fields from NettiX ads can be used as badge in the WooCommerce product card

= 1.2.0 =
* Location (town/city) is added as tag to product
* Population of short description can be managed from integration settings
* Maker name is added as product category and attached to product
* In Nettikaravaani interface, vehicle type is added as tag to product
* In Nettivene interface, boat type and boat sub-type are added as tag to product

= 1.1.0 =
* Added Nettikaravaani and Nettikone as possible sources for ads

= 1.0.2 =
* Added ability to test correctness of credentials to NettiX API
* Plugin tested to work with WooCommerce 7.4
* Fixed how plugin activation fails in case WooCommerce is not installed/activated

= 1.0 =
Initial version

= 1.1.0 =
* Added Nettikaravaani and Nettikone as possible sources for ads