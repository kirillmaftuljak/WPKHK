<?php
/**
 * Plugin Name: Very First Plugin
 * Plugin URI: http://maftuljakkirill.ikt.khk.ee/
 * Description: This is very first plugin I ever created.
 * Version: 1.0
 * Author: Kirill M
 * Author URI: http://maftuljakkirill.ikt.khk.ee/
**/

function dh_modify_read_more_link() {
 return '<a class="more-link" href="' . get_permalink() . '">Click to Read!</a>';
}
add_filter( 'the_content_more_link', 'dh_modify_read_more_link' );