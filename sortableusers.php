<?php
/*
Plugin Name: Sortable Users
Plugin URI: http://www.webfella.com.au/wordpress/sortable-users/
Description: Easily sort users by dragging and dropping them within the user admin section.
Version: 1.0
Author: Adrian Bruinhout
Author URI: http://www.webfella.com.au/
License: GPL2

Copyright 2013  Adrian Bruinhout  (email : adrian@webfella.com.au)

		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License, version 2, as 
		published by the Free Software Foundation.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program; if not, write to the Free Software
		Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

global $wp_version;
$exit_msg_ms  = 'Sorry, this plugin is not supported (and will not work) on WordPress MultiSite.';
$exit_msg_ver = 'Sorry, this plugin is not supported on pre-3.1 WordPress installs.';
if( is_multisite() ) { exit($exit_msg_ms); }
if (version_compare($wp_version,"3.1","<")) { exit($exit_msg_ver); }

class WEBFELLA_SU {

	// add custom db order fields to users when plugin activated
	function prepareDb() {
		$users = get_users();
		foreach ($users as $user) {
			if (empty($user->user_order)) {
				$userID = $user->ID;
				add_user_meta($userID, 'user_order', $userID, true);
			}
		}
	}

	// Load assets only on the admin users page
	function load_assets() {
		global $pagenow;
		if ($pagenow === 'users.php') {
			$relPath = plugins_url() . '/sortable-users';
			wp_enqueue_script('sortuserscriptslibs', $relPath . '/js/libs/jquery-ui.min.js', array('jquery'));
			wp_enqueue_script('sortuserscripts', $relPath . '/js/su.js', array('sortuserscriptslibs'));
			wp_register_style('sortuserstyles', $relPath . '/css/su.css');
			wp_enqueue_style('sortuserstyles');
			wp_localize_script( 'sortuserscripts', 'sortable_users_data', array( 'ajax_url' => admin_url( 'admin-ajax.php' )) );
		}
	}

	// Register the column - Order
	function userorder($columns) {
		$columns['user_order'] = __('Order', 'user_order');
		return $columns;
	}
	
	// Display the column content
	function userorder_column( $value, $column_name, $user_id ) {
		if ( 'user_order' != $column_name ) { return $value; }
		$user = get_userdata( $user_id );
		$order_button = '<span class="su_sort" data-su-user-id="'. $user_id .'">'. get_user_meta($user_id, 'user_order', true) .'</span>';
		return $order_button;
	}

	// Map column to sorting value
	function userorder_column_sortable($columns) {
		$custom = array(
			'user_order'    => 'user_order'
		);
		return wp_parse_args($custom, $columns);
	}

	// order users by column
	function userorder_column_orderby( $vars ) {
		if ( isset( $vars['orderby'] ) && 'user_order' == $vars['orderby'] ) {
			$vars = array_merge( $vars, array(
				'meta_key' => 'user_order',
				'orderby' => 'meta_value'
			) );
		}
		return $vars;
	}

	// update users with order via ajax
	function userorder_ajax() {
		global $wpdb;
		update_user_meta($_POST['user_ID'], 'user_order', $_POST['user_value']);
		die();
	}

	// insert sorting link to wp menu by default
	function edit_user_menu() {  
		global $menu;
		$menu[70][2] = $menu[70][2].'?orderby=user_order&order=desc';
	}

}

// Actions
register_activation_hook(__FILE__, array('WEBFELLA_SU', 'prepareDb'));
add_action('user_register', array('WEBFELLA_SU', 'prepareDb'));
add_action('admin_init', array('WEBFELLA_SU','load_assets'));
add_filter('manage_users_columns', array('WEBFELLA_SU','userorder'));
add_action('manage_users_custom_column',  array('WEBFELLA_SU','userorder_column'), 10, 3);
add_filter('manage_users_sortable_columns', array('WEBFELLA_SU','userorder_column_sortable'));
add_filter('request', array('WEBFELLA_SU','userorder_column_orderby'));
add_action('wp_ajax_my_action', array('WEBFELLA_SU','userorder_ajax'));
add_action('admin_menu', array('WEBFELLA_SU','edit_user_menu'));

// donate link on plugins page
add_filter('plugin_row_meta', 'registerdate_donate_link', 10, 2);
function registerdate_donate_link($links, $file) {
	if ($file == plugin_basename(__FILE__)) {
		$donate_link = '<a href="http://www.webfella.com.au/wordpress/donate/">Donate</a>';
		$links[] = $donate_link;
	}
	return $links;
}

?>