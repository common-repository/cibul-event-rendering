<?php 

/*
  Plugin Name: Cibul plugin
  Plugin URI: http://cibul.net
  Description: Render rich list items views from your event links in your blog articles
  Version: 1.04
  Author: Kari Olafsson
  Author URI: http://cibul.net
  License: GPL
*/


require_once('lib/CibulClientSDK/CibulClientSDK.class.php');
require_once('CibulPluginWordpressDbHandler.class.php');
require_once('CibulPluginView.class.php');
require_once('CibulPluginAdmin.class.php');
require_once('CibulPluginCache.class.php');
require_once('CibulPluginRenderer.class.php');
require_once('CibulWidgetMap.class.php');

// make admin menu and set options

$cibulAdmin = new CibulPluginAdmin(); // holds and manages plugin options

$options = $cibulAdmin->getOptions();


// make cache handler

$wpDbHandler = new CibulPluginWordpressDbHandler(); // handles wordpress db operations. The cache handler uses this.

$cacheHandler = new CibulPluginCache($options['cacheTableName'], $wpDbHandler); // handles renders stored in db


// make view handler

$viewHandler = new CibulPluginView($options['templates']); // handlers view rendering. shove in there templates to be used


// admin needs a reference to the cache handler

$cibulAdmin->setCache($cacheHandler);




// create the cache table on plugin activation
register_activation_hook(__FILE__, array($cacheHandler, 'createTable'));

// set plugin administration page

function admin_init()
{
  global $cibulAdmin;

  if (function_exists('add_options_page')) add_options_page('Cibul Admin', 'Cibul Admin', 9, basename(__FILE__), array($cibulAdmin, 'processRequest'));

  // register admin  scripts
  wp_register_script('cibul-admin', plugins_url('js/cibul-admin.js', __FILE__));
  wp_enqueue_script('cibul-admin');
}

add_action('admin_menu', 'admin_init');



// create and hook up the renderer if key is valid

if ($cibulAdmin->isApiKeyValid())
{
  // try to initialize a client

  try
  {
    $cibulClient = new CibulClientSDK($options['key']);

    // load renderer

    $renderer = new CibulPluginRenderer($cibulClient, $cacheHandler, $viewHandler, $options['lang']);  

    // hook up renderer to the_content

    add_filter('the_content', array($renderer, 'renderEvents'));
  }
  catch (Exception $e) {}
  
}
