<?php
add_filter( 'plugin_row_meta', 'dc_action_links', 10, 2 );


/**
 * Show action links on the plugin screen
 */
function () {

	
	return (array) $links;
}