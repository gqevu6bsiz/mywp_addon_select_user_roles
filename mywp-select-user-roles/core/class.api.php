<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpSelectUserRolesApi' ) ) :

final class MywpSelectUserRolesApi {

  public static function plugin_info() {

    $plugin_info = array(
      'admin_url' => admin_url( 'admin.php?page=mywp_add_on_select_user_roles' ),
      'document_url' => 'https://mywpcustomize.com/add_ons/add-on-select-user-roles/',
      'website_url' => 'https://mywpcustomize.com/',
      'github' => 'https://github.com/gqevu6bsiz/mywp_addon_select_user_roles',
      'github_raw' => 'https://raw.githubusercontent.com/gqevu6bsiz/mywp_addon_select_user_roles/',
      'github_tags' => 'https://api.github.com/repos/gqevu6bsiz/mywp_addon_select_user_roles/tags',
    );

    $plugin_info = apply_filters( 'mywp_select_user_roles_plugin_info' , $plugin_info );

    return $plugin_info;

  }

  public static function is_manager() {

    return MywpApi::is_manager();

  }

  public static function get_all_user_roles() {

    return MywpApi::get_all_user_roles();

  }

  public static function get_count_user_roles() {

    $count_user_roles = count_users();

    return $count_user_roles['avail_roles'];

  }

  public static function available_controllers() {

    $available_controllers = array(
      'admin_comments',
      'admin_dashboard',
      'admin_general',
      'admin_nav_menu',
      'admin_post_edit',
      'admin_posts',
      'admin_sidebar',
      'admin_site_editor',
      'admin_terms',
      'admin_toolbar',
      'admin_uploads',
      'admin_user_edit',
      'admin_users',
      'frontend_toolbar',
      'login_user',
    );

    $available_controllers = apply_filters( 'mywp_select_user_roles_available_controllers' , $available_controllers );

    return $available_controllers;

  }

  public static function is_do_controller_to_controller_id( $controller_id ) {

    if( empty( $controller_id ) ) {

      return false;

    }

    $controller_id = strip_tags( $controller_id );

    $available_controllers = self::available_controllers();

    $is_do_controller = false;

    if( in_array( $controller_id , $available_controllers , true ) ) {

      $is_do_controller = true;

    }

    $is_do_controller = apply_filters( 'mywp_select_user_roles_is_do_controller_to_controller_id' , $is_do_controller , $controller_id );

    return $is_do_controller;

  }

}

endif;
