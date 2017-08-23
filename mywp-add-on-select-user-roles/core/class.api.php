<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpAddOnSelectUserRolesApi' ) ) :

final class MywpAddOnSelectUserRolesApi {

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

  public static function plugin_info() {

    $plugin_info = array(
      'admin_url' => admin_url( 'admin.php?page=mywp_select_user_roles' ),
      'document_url' => 'https://mywpcustomize.com/add_ons/add-on-select-user-roles/',
      'website_url' => 'https://mywpcustomize.com/',
      'github' => 'https://github.com/gqevu6bsiz/mywp_addon_select_user_roles',
      'github_tags' => 'https://api.github.com/repos/gqevu6bsiz/mywp_addon_select_user_roles/tags',
    );

    $plugin_info = apply_filters( 'mywp_add_on_select_user_roles_plugin_info' , $plugin_info );

    return $plugin_info;

  }

  public static function get_count_user_roles() {

    $count_user_roles = count_users();

    return $count_user_roles['avail_roles'];

  }

  public static function is_do_controller_to_controller_id( $controller_id ) {

    if( empty( $controller_id ) ) {

      return false;

    }

    $controller_id = strip_tags( $controller_id );

    $is_do_controller = false;

    if( strpos( $controller_id , 'admin_' ) !== false ) {

      $is_do_controller = true;

    }

    $is_do_controller = apply_filters( 'mywp_add_on_select_user_roles_is_do_controller_to_controller_id' , $is_do_controller , $controller_id );

    return $is_do_controller;

  }

}

endif;
