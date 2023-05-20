<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleSelectUserRolesUpdater' ) ) :

final class MywpControllerModuleSelectUserRolesUpdater extends MywpControllerAbstractModule {

  static protected $id = 'select_user_roles_updater';

  static private $schedule_hook = 'mywp_select_user_roles_version_check';

  protected static function after_init() {

    add_filter( 'mywp_controller_pre_get_model_' . self::$id , array( __CLASS__ , 'mywp_controller_pre_get_model' ) );

    add_filter( 'site_transient_update_plugins' , array( __CLASS__ , 'site_transient_update_plugins' ) );

  }

  public static function mywp_controller_pre_get_model( $pre_model ) {

    $pre_model = true;

    return $pre_model;

  }

  public static function site_transient_update_plugins( $site_transient ) {

    if( empty( $site_transient ) or ! isset( $site_transient->response ) ) {

      return $site_transient;

    }

    $is_latest = self::is_latest();

    if( is_wp_error( $is_latest ) ) {

      return $site_transient;

    }

    if( $is_latest ) {

      return $site_transient;

    }

    $latest = self::get_latest();

    if( is_wp_error( $latest ) ) {

      return $site_transient;

    }

    $plugin_info = MywpSelectUserRolesApi::plugin_info();

    $update_plugin = array(
      'id' => MYWP_SELECT_USER_ROLES_PLUGIN_BASENAME,
      'slug' => MYWP_SELECT_USER_ROLES_PLUGIN_DIRNAME,
      'plugin' => MYWP_SELECT_USER_ROLES_PLUGIN_BASENAME,
      'new_version' => $latest,
      'url' => $plugin_info['github'],
      'package' => $plugin_info['github_raw'] . $latest . '/' . MYWP_SELECT_USER_ROLES_PLUGIN_DIRNAME . '.zip',
      'icons' => array(),
      'banners' => array(),
      'banners_rtl' => array(),
      'requires' => false,
      'tested' => false,
      'compatibility' => false,
    );

    $site_transient->response[ MYWP_SELECT_USER_ROLES_PLUGIN_BASENAME ] = (object) $update_plugin;

    return $site_transient;

  }

  public static function get_remote() {

    $transient_key = 'mywp_select_user_roles_updater_remote';

    $transient = get_site_transient( $transient_key );

    if( ! empty( $transient ) ) {

      return $transient;

    }

    $plugin_info = MywpSelectUserRolesApi::plugin_info();

    $remote_args = array();

    $error = new WP_Error();

    $remote_result = wp_remote_get( $plugin_info['github_tags'] , $remote_args );

    if( empty( $remote_result ) ) {

      $error->add( 'not_results' , __( 'Connection lost or the server is busy. Please try again later.' , 'mywp-select-user-roles' ) );

      return $error;

    }

    if( is_wp_error( $remote_result ) ) {

      $error->add( 'invalid_remote' , $remote_result->get_error_message() );

      return $error;

    }

    $remote_code = wp_remote_retrieve_response_code( $remote_result );
    $remote_body = wp_remote_retrieve_body( $remote_result );

    set_site_transient( $transient_key , $remote_body , DAY_IN_SECONDS );

    if( $remote_code !== 200 ) {

      if( ! empty( $remote_body ) ) {

        $maybe_json = json_decode( $remote_body );

        if( ! empty( $maybe_json ) && ! empty( $maybe_json->message ) ) {

          $error->add( 'invalid_connection' , sprintf( '[%d] %s' , $remote_code , $maybe_json->message ) );

        } else {

          $error->add( 'invalid_json' , sprintf( '[%d] %s' , $remote_code , __( 'An error has occurred. Please reload the page and try again.' , 'mywp-select-user-roles' ) ) );

        }

      } else {

        $error->add( 'invalid_connection' , sprintf( '[%d] %s' , $remote_code , __( 'Connection lost or the server is busy. Please try again later.' , 'mywp-select-user-roles' ) ) );

      }

      return $error;

    }

    if( empty( $remote_body ) ) {

      $error->add( 'invalid_remote_body' , __( 'An error has occurred. Please reload the page and try again.' , 'mywp-select-user-roles' ) );

      return $error;

    }

    return $remote_body;

  }

  public static function get_latest() {

    $transient_key = 'mywp_select_user_roles_updater';

    $transient = get_site_transient( $transient_key );

    if( ! empty( $transient['latest'] ) ) {

      return $transient['latest'];

    }

    $remote = self::get_remote();

    if( empty( $remote ) or is_wp_error( $remote ) ) {

      return $remote;

    }

    $error = new WP_Error();

    $maybe_remote_json = json_decode( $remote );

    if( ! is_array( $maybe_remote_json ) or empty( $maybe_remote_json[0] ) ) {

      $error->add( 'invalid_remote_json' , __( 'Invalid remote Json data. Please try again.' , 'mywp-select-user-roles' ) );

      return $error;

    }

    $remote_json = $maybe_remote_json[0];

    if( ! is_object( $remote_json ) or ! isset( $remote_json->name ) or ! isset( $remote_json->zipball_url ) or ! isset( $remote_json->tarball_url ) ) {

      $error->add( 'invalid_json' , __( 'Invalid results. Sorry maybe update format changed.' , 'mywp-select-user-roles' ) );

      return $error;

    }

    $latest = $remote_json->name;

    $transient = array( 'latest' => $latest );

    set_site_transient( $transient_key , $transient , DAY_IN_SECONDS );

    if( ! function_exists( 'wp_clean_plugins_cache' ) ) {

      require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    }

    wp_clean_plugins_cache();

    return $latest;

  }

  public static function is_latest() {

    $latest = self::get_latest();

    $error = new WP_Error();

    if( is_wp_error( $latest ) ) {

      return $latest;

    }

    $latest_compare = version_compare( $latest , MYWP_SELECT_USER_ROLES_VERSION , '<=' );

    return $latest_compare;

  }

  public static function mywp_wp_loaded() {

    if( is_multisite() ) {

      if( ! is_main_site() ) {

        return false;

      }

    }

    self::schedule_hook();

    add_action( self::$schedule_hook , array( __CLASS__ , 'version_check' ) );

  }

  public static function schedule_hook() {

    if( wp_next_scheduled( self::$schedule_hook ) ) {

      return false;

    }

    $next_scheduled_date = time() + DAY_IN_SECONDS;

    wp_schedule_single_event( $next_scheduled_date , self::$schedule_hook );

  }

  public static function version_check() {

    self::get_latest();

  }

}

MywpControllerModuleSelectUserRolesUpdater::init();

endif;
