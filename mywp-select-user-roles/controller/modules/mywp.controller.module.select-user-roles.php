<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleSelectUserRoles' ) ) :

final class MywpControllerModuleSelectUserRoles extends MywpControllerAbstractModule {

  static protected $id = 'select_user_roles';

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['select_user_roles'] = array();

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['select_user_roles'] = array();

    return $default_data;

  }

  protected static function after_init() {

    add_filter( 'mywp_controller_is_do' , array( __CLASS__ , 'mywp_controller_is_do' ) , 9 , 2 );

  }

  public static function mywp_controller_is_do( $is_do_controller , $controller_id ) {

    $called_text = sprintf( '(object) %s' , __CLASS__ , __FUNCTION__ );

    if( empty( $controller_id ) ) {

      MywpHelper::error_not_found_message( 'nothing $controller_id' , $called_text );

      return $is_do_controller;

    }

    if( ! MywpSelectUserRolesApi::is_do_controller_to_controller_id( $controller_id ) ) {

      return $is_do_controller;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['select_user_roles'] ) ) {

      return $is_do_controller;

    }

    $mywp_user = new MywpUser();

    $current_user_role = $mywp_user->get_user_role();

    if( in_array( $current_user_role , $setting_data['select_user_roles'] , true ) ) {

      return true;

    } else {

      return false;

    }

  }

}

MywpControllerModuleSelectUserRoles::init();

endif;
