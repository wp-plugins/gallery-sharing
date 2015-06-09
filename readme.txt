=== Gallery Sharing ===
Contributors: edwardbock
Donate link: http://palasthotel.de/
Tags: gallery, sharing
Requires at least: 4.0
Tested up to: 4.2.2
Stable tag: 1.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl

You can share your galleries between wordpress installations.

== Description ==

Adds a new tool to post editor that allows you to share you galleries.

== Installation ==

1. Upload `gallery-sharing-wordpress.zip` to the `/wp-content/plugins/` directory
1. Extract the Plugin to a `gallery-sharing` Folder
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Goto Settings->Gallery Sharing
1. Enter Domain
1. Enter htaccess credentials if needed else leave empty
1. Use tinymce button on post editor to find galleries in all instances and in own wordpress

== Frequently Asked Questions ==

= How does it work? = 
We place a shortcode in post content and register an ajax endpoint. On pageload the shortcode in post content loads via curl the gallery from the registered endpoint and renders it to content.


== Screenshots ==


== Changelog ==

= 1.0 =
* First release

== Upgrade Notice ==


== Arbitrary section ==


