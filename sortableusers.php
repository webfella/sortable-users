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

}

// Actions
register_activation_hook(__FILE__, array('WEBFELLA_SU', 'prepareDb'));
add_action('user_register', array('WEBFELLA_SU', 'prepareDb'));

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