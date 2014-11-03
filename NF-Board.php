<?php
/**
 * Plugin Name: NF BORAD
 * Plugin URI: http://www.nfboard.co.kr
 * Description: NF BOARD 는 워드프레스 기반의 플러그인 형태로 작동하는 누구나 친숙하게 편리하게 이용이 가능한 한국형 회원관리 & 게시판 통합보드 입니다.
 * Version: 1.0.1
 * Author: NFLINT
 * Author URI: http://www.nfboard.co.kr
 */

require_once(plugin_dir_path(__FILE__)."env-define.php");
require_once(plugin_dir_path(__FILE__)."env-include.php");
require_once(plugin_dir_path(__FILE__)."env-function.php");

add_action('init', 'NFB_INIT', 1);
add_action('wp_enqueue_scripts', 'NFB_UserHead');
add_action('admin_enqueue_scripts', 'NFB_AdminHead');
add_action('admin_menu', 'NFB_AdminMenu');

add_action('the_posts', 'NFB_BoardShortcode');
add_action('the_posts', 'NFB_LatestShortcode');

add_shortcode('NFB_JOIN', 'NFB_JoinShortcode');
add_shortcode('NFB_LOGIN', 'NFB_LoginShortcode');
add_shortcode('NFB_ID_FIND', 'NFB_IDFindShortcode');
add_shortcode('NFB_PW_FIND', 'NFB_PWFindShortcode');
add_shortcode('NFB_LEAVE', 'NFB_LeaveShortcode');

require_once(plugin_dir_path(__FILE__)."env-ajax.php");

register_activation_hook(__FILE__, 'NFB_Activation_func');
register_deactivation_hook(__FILE__, 'NFB_Deactivation_func');
register_uninstall_hook(__FILE__, 'NFB_Plugin_delete');
?>