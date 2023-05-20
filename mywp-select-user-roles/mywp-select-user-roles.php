<?php
/*
Plugin Name: My WP Add-on Select User Roles
Plugin URI: https://mywpcustomize.com/
Description: My WP Add-on Select User Roles is apply customize for only selected user roles on My WP Customize.
Version: 1.6.0
Author: gqevu6bsiz
Author URI: http://gqevu6bsiz.chicappa.jp/
Text Domain: mywp-select-user-roles
Domain Path: /languages/
My WP Test working: 1.21
*/

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpSelectUserRoles' ) ) :

final class MywpSelectUserRoles {

  public static function init() {

    self::define_constants();
    self::include_core();

    add_action( 'mywp_start' , array( __CLASS__ , 'mywp_start' ) );

  }

  private static function define_constants() {

    define( 'MYWP_SELECT_USER_ROLES_NAME' , 'My WP Add-on Select User Roles' );
    define( 'MYWP_SELECT_USER_ROLES_VERSION' , '1.6.0' );
    define( 'MYWP_SELECT_USER_ROLES_PLUGIN_FILE' , __FILE__ );
    define( 'MYWP_SELECT_USER_ROLES_PLUGIN_BASENAME' , plugin_basename( MYWP_SELECT_USER_ROLES_PLUGIN_FILE ) );
    define( 'MYWP_SELECT_USER_ROLES_PLUGIN_DIRNAME' , dirname( MYWP_SELECT_USER_ROLES_PLUGIN_BASENAME ) );
    define( 'MYWP_SELECT_USER_ROLES_PLUGIN_PATH' , plugin_dir_path( MYWP_SELECT_USER_ROLES_PLUGIN_FILE ) );
    define( 'MYWP_SELECT_USER_ROLES_PLUGIN_URL' , plugin_dir_url( MYWP_SELECT_USER_ROLES_PLUGIN_FILE ) );

  }

  private static function include_core() {

    $dir = MYWP_SELECT_USER_ROLES_PLUGIN_PATH . 'core/';

    require_once( $dir . 'class.api.php' );

  }

  public static function mywp_start() {

    add_action( 'mywp_plugins_loaded', array( __CLASS__ , 'mywp_plugins_loaded' ) );

    add_action( 'init' , array( __CLASS__ , 'wp_init' ) );

  }

  public static function mywp_plugins_loaded() {

    add_filter( 'mywp_controller_plugins_loaded_include_modules' , array( __CLASS__ , 'mywp_controller_plugins_loaded_include_modules' ) );

    add_filter( 'mywp_setting_plugins_loaded_include_modules' , array( __CLASS__ , 'mywp_setting_plugins_loaded_include_modules' ) );

  }

  public static function wp_init() {

    load_plugin_textdomain( 'mywp-select-user-roles' , false , MYWP_SELECT_USER_ROLES_PLUGIN_DIRNAME . '/languages' );

  }

  public static function mywp_controller_plugins_loaded_include_modules( $includes ) {

    $dir = MYWP_SELECT_USER_ROLES_PLUGIN_PATH . 'controller/modules/';

    $includes['select_user_roles_main_general'] = $dir . 'mywp.controller.module.main.general.php';
    $includes['select_user_roles_setting']      = $dir . 'mywp.controller.module.select-user-roles.php';
    $includes['select_user_roles_updater']      = $dir . 'mywp.controller.module.updater.php';

    return $includes;

  }

  public static function mywp_setting_plugins_loaded_include_modules( $includes ) {

    $dir = MYWP_SELECT_USER_ROLES_PLUGIN_PATH . 'setting/modules/';

    $includes['select_user_roles_setting'] = $dir . 'mywp.setting.select-user-roles.php';

    return $includes;

  }

}

MywpSelectUserRoles::init();

endif;
