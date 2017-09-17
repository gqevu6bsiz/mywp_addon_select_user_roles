<?php
/*
Plugin Name: My WP Add-on Select User Roles
Plugin URI: https://mywpcustomize.com/
Description: Add-on the apply customize for selected user roles on My WP.
Version: 1.1.3
Author: gqevu6bsiz
Author URI: http://gqevu6bsiz.chicappa.jp/
Requires at least: 4.6
Tested up to: 4.8
Text Domain: mywp-add-on-select-user-roles
Domain Path: /languages/
*/

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpAddOnSelectUserRoles' ) ) :

final class MywpAddOnSelectUserRoles {

  private static $instance;

  private function __construct() {}

  public static function get_instance() {

    if ( !isset( self::$instance ) ) {

      self::$instance = new self();

    }

    return self::$instance;

  }

  private function __clone() {}

  private function __wakeup() {}

  public static function init() {

    self::define_constants();
    self::include_core();

    add_action( 'mywp_start' , array( __CLASS__ , 'mywp_start' ) );

  }

  private static function define_constants() {

    define( 'MYWP_ADD_ON_SELECT_USER_ROLES_NAME' , 'My WP Add-on Select User Roles' );
    define( 'MYWP_ADD_ON_SELECT_USER_ROLES_VERSION' , '1.1.3' );
    define( 'MYWP_ADD_ON_SELECT_USER_ROLES_PLUGIN_FILE' , __FILE__ );
    define( 'MYWP_ADD_ON_SELECT_USER_ROLES_PLUGIN_BASENAME' , plugin_basename( MYWP_ADD_ON_SELECT_USER_ROLES_PLUGIN_FILE ) );
    define( 'MYWP_ADD_ON_SELECT_USER_ROLES_PLUGIN_DIRNAME' , dirname( MYWP_ADD_ON_SELECT_USER_ROLES_PLUGIN_BASENAME ) );
    define( 'MYWP_ADD_ON_SELECT_USER_ROLES_PLUGIN_PATH' , plugin_dir_path( MYWP_ADD_ON_SELECT_USER_ROLES_PLUGIN_FILE ) );
    define( 'MYWP_ADD_ON_SELECT_USER_ROLES_PLUGIN_URL' , plugin_dir_url( MYWP_ADD_ON_SELECT_USER_ROLES_PLUGIN_FILE ) );

  }

  private static function include_core() {

    $dir = MYWP_ADD_ON_SELECT_USER_ROLES_PLUGIN_PATH . 'core/';

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

    load_plugin_textdomain( 'mywp-add-on-select-user-roles' , false , MYWP_ADD_ON_SELECT_USER_ROLES_PLUGIN_DIRNAME . '/languages' );

  }

  public static function mywp_controller_plugins_loaded_include_modules( $includes ) {

    $dir = MYWP_ADD_ON_SELECT_USER_ROLES_PLUGIN_PATH . 'controller/modules/';

    $includes['add_on_select_user_roles_main_general'] = $dir . 'mywp.controller.module.main.general.php';
    $includes['add_on_select_user_roles_setting']      = $dir . 'mywp.controller.module.select-user-roles.php';
    $includes['add_on_select_user_roles_updater']      = $dir . 'mywp.controller.module.updater.php';

    return $includes;

  }

  public static function mywp_setting_plugins_loaded_include_modules( $includes ) {

    $dir = MYWP_ADD_ON_SELECT_USER_ROLES_PLUGIN_PATH . 'setting/modules/';

    $includes['add_on_select_user_roles_setting'] = $dir . 'mywp.setting.select-user-roles.php';

    return $includes;

  }

}

MywpAddOnSelectUserRoles::init();

endif;
