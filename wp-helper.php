<?php
/**
 * Plugin Name:Helper plugging for WP-Rocket
 * Plugin URI: https://github.com/dexterneutron
 * Description: Helper for avoiding missing permission notices.
 * Version: 1.0
 * Author: Felix Lugo
 * Author URI: https://github.com/dexterneutron
 */

add_action('admin_notices','remove_notices',1);
function remove_notices(){
	remove_action('admin_notices', 'rocket_warning_htaccess_permissions');
	/*Since the notice_advanced_cache_permissions function was called from a class, removing the action could be a bit tricky.
	one approach is to first find the callback key (or unique ID) and then use that reference to remove the action. 
	*/
	do_hard_unregister_object_callback( 'admin_notices', 10, 'notice_advanced_cache_permissions');
}
 function do_hard_unregister_object_callback( $event_name, $priority, $method_name ) {
	$callback_function = get_object_callback_unique_id_from_registry( $event_name, $priority, $method_name );
	if ( ! $callback_function ) {
		return false;
	}
	remove_filter( $event_name, $callback_function, $priority );
}
function get_object_callback_unique_id_from_registry( $event_name, $priority, $method_name ) {
	global $wp_filter;
	if ( ! isset( $wp_filter[ $event_name ][ $priority ] ) ) {
		return false;
	}
	foreach( $wp_filter[ $event_name ][ $priority ] as $callback_function => $registration ) {
		if ( strpos( $callback_function, $method_name, 32) !== false) {
			return $callback_function;
		}
	}
	return false;
}
