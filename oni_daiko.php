<?php
/*
Plugin Name: Oni Daiko
Plugin URI: http://plugins.webnist.net/
Description: Shows a list of the latest posts from each blogs on a multisaite blog.
Version: 0.5.5
Author: Webnist
Author URI: http://www.webnist.net
*/
if(preg_match('/mu-plugins/' , dirname(__FILE__))) {
	require_once(dirname(__FILE__).'/oni-daiko/oni_daiko_contents.php');
	require_once(dirname(__FILE__).'/oni-daiko/oni_daiko_settings.php');
	require_once(dirname(__FILE__).'/oni-daiko/oni_daiko_setting_load.php');
	require_once(dirname(__FILE__).'/oni-daiko/oni_daiko_setting_save.php');
	if($blog_id == 1) {
		require_once(dirname(__FILE__).'/oni-daiko/oni_daiko_widgets.php');
	}
	$domain_name  = 'oni_daiko';
	$locale_name  = get_locale();
	$mofile_name  = dirname(__FILE__);
	$mofile_name .= "/oni-daiko/lang/$domain_name-$locale_name.mo";
	load_textdomain("$domain_name", $mofile_name);
} else {
	require_once(dirname(__FILE__).'/oni_daiko_contents.php');
	require_once(dirname(__FILE__).'/oni_daiko_settings.php');
	require_once(dirname(__FILE__).'/oni_daiko_setting_load.php');
	require_once(dirname(__FILE__).'/oni_daiko_setting_save.php');
	if($blog_id == 1) {
		require_once(dirname(__FILE__).'/oni_daiko_widgets.php');
	}
	$domain_name  = 'oni_daiko';
	$locale_name  = get_locale();
	$mofile_name  = dirname(__FILE__);
	$mofile_name .= "/lang/$domain_name-$locale_name.mo";
	load_textdomain("$domain_name", $mofile_name);
}
function get_oni_daiko_plugin_uri(){
	if(preg_match('/mu-plugins/' , dirname(__FILE__))) {
		$oni_daiko_plugin_uri = get_option('siteurl').'/wp-content/mu-plugins/oni-daiko/';
	} else {
		$oni_daiko_plugin_uri = get_option('siteurl').'/wp-content/plugins/oni-daiko/';
	}
	return $oni_daiko_plugin_uri;
}
function oni_daiko_plugin_uri(){
	if(preg_match('/mu-plugins/' , dirname(__FILE__))) {
		$oni_daiko_plugin_uri = get_option('siteurl').'/wp-content/mu-plugins/oni-daiko/';
	} else {
		$oni_daiko_plugin_uri = get_option('siteurl').'/wp-content/plugins/oni-daiko/';
	}
	echo $oni_daiko_plugin_uri;
}
function get_oni_daiko_plugin_directory(){
	if(preg_match('/mu-plugins/' , dirname(__FILE__))) {
		$oni_daiko_plugin_directory = ABSPATH.'/wp-content/mu-plugins/oni-daiko/';
	} else {
		$oni_daiko_plugin_directory = ABSPATH.'/wp-content/plugins/oni-daiko/';
	}
	return $oni_daiko_plugin_directory;
}
function oni_daiko_plugin_directory(){
	if(preg_match('/mu-plugins/' , dirname(__FILE__))) {
		$oni_daiko_plugin_directory = ABSPATH.'/wp-content/mu-plugins/oni-daiko/';
	} else {
		$oni_daiko_plugin_directory = ABSPATH.'/wp-content/plugins/oni-daiko/';
	}
	echo $oni_daiko_plugin_directory;
}
function get_oni_daiko_plugin_setting_uri(){
	$request_page_uri = $_SERVER['REQUEST_URI'];
	if(preg_match('/oni_daiko.php/' , $request_page_uri)) {
		return TRUE;
	} else {
		return FALSE;
	}
}
function oni_daiko_plugin_setting_header(){
	if(get_oni_daiko_plugin_setting_uri()) {
		echo '<link rel="stylesheet" href="'.get_oni_daiko_plugin_uri().'admin/style.css" type="text/css" media="screen" />'."\n";
	}
}
add_action('admin_head', 'oni_daiko_plugin_setting_header', 99);
function include_oni_daiko_css() {
	echo '<link rel="stylesheet" href="'.get_oni_daiko_plugin_uri().'css/oni_daiko.css" type="text/css" media="screen" />'."\n";
}
add_action('wp_head','include_oni_daiko_css');
function oni_daiko_menu() {
	if(preg_match('/mu-plugins/' , dirname(__FILE__))) {
		add_submenu_page('wpmu-admin.php', __('Oni Daiko', 'oni_daiko'), __('Oni Daiko', 'oni_daiko'), 'manage_options', 'oni_daiko.php', 'oni_daiko_setting_menu');
	} else {
		add_menu_page(__('Oni Daiko', 'oni_daiko'), __('Oni Daiko', 'oni_daiko'), 10, 'oni_daiko.php', 'oni_daiko_setting_menu', get_oni_daiko_plugin_uri().'admin/images/admin_side.gif');
	}
}
add_action('admin_menu', 'oni_daiko_menu');
function oni_daiko_short_contents($get_contents, $characters) {
	global $shortcode_tags;
	if (empty($characters)) {
		$get_characters = 100;
	} else {
		$get_characters	= $characters;
	}
	$get_content = strip_tags($get_contents);
	if ( !empty($shortcode_tags) && is_array($shortcode_tags) ) {
		$tagnames = array_keys($shortcode_tags);
		$tagregexp = join( '|', array_map('preg_quote', $tagnames) );
		$get_content = preg_replace('/\[(' . $tagregexp . ')\\b.*?\\/?\\]/s', '', $get_content);
		$get_content = preg_replace('/\[\/(' . $tagregexp . ')\\b.*?\\/?\\]/s', '', $get_content);
	}
	if (strlen($get_content) == mb_strlen($get_content, 'UTF-8')) {
		$content_short = substr($get_content, 0, $get_characters);
	} else {
		$content_short = mb_substr($get_content, 0, $get_characters + 1, 'UTF-8');
	}
	$output = $content_short."\n";
	return $output;
}
function oni_daiko_template_tag( $case = 'page' ) {
	$output = get_oni_daiko_post('title=' . oni_daiko_title() . '&limit=' . oni_daiko_limit() . '&contents=' .oni_daiko_contents(). '&characters=' . oni_daiko_text_limit() . '&case=' . $case);
	return $output;
}
?>
