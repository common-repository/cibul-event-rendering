=== Plugin Name ===
Contributors: kaore
Donate link: 
Tags: events, cibul, public events
Requires at least: 3.2
Tested up to: 3.3.2
Stable tag: 1.04

Convert links to cibul events into html renders with image, title, description and map

== Description ==

The plugin picks up any links to cibul events in blog posts, fetches the event data from the cibul.net website to make a render of the event at the location of the link in the post.

Event renders follow a default and css which can be fully customized from the plugin's admin page. 

Rendered information include:

 * Title
 * Description
 * Date, Time and location
 * Thumbnail

A widget can be set on the sidebar to display on a map the locations of the events listed on the current page.


== Installation ==

1. Upload the plugin folder in the /wp-content/plugins directory
2. Activate the plugin
3. In the plugin admin menu, follow instructions to set a new API key
4. In your posts, place <a> tag links to the cibul events you want to render


== Frequently Asked Questions ==

= I have a question which is not listed here, where should I send it? =

Send an email to admin@cibul.net


== Changelog ==

= 1.0 =
* Couple of bug fixes, use of the CibulClientSDK class

= 1.0 =
* First released version of the plugin