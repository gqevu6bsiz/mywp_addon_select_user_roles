<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenSelectUserRoles' ) ) :

final class MywpSettingScreenSelectUserRoles extends MywpAbstractSettingModule {

  static protected $id = 'select_user_roles';

  static protected $priority = 50;

  static private $menu = 'add_on_select_user_roles';

  public static function mywp_setting_menus( $setting_menus ) {

    $setting_menus[ self::$menu ] = array(
      'menu_title' => __( 'User Roles' ),
      'multiple_screens' => false,
    );

    return $setting_menus;

  }

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'User Roles' ),
      'menu' => self::$menu,
      'controller' => 'select_user_roles',
      'document_url' => self::get_document_url( 'add_ons/add-on-select-user-roles/' ),
    );

    return $setting_screens;

  }

  public static function mywp_ajax_manager() {

    add_action( 'wp_ajax_' . MywpSetting::get_ajax_action_name( self::$id , 'check_latest' ) , array( __CLASS__ , 'check_latest' ) );

  }

  public static function check_latest() {

    $action_name = MywpSetting::get_ajax_action_name( self::$id , 'check_latest' );

    if( empty( $_POST[ $action_name ] ) ) {

      return false;

    }

    check_ajax_referer( $action_name , $action_name );

    if( ! MywpSelectUserRolesApi::is_manager() ) {

      return false;

    }

    delete_site_transient( 'mywp_select_user_roles_updater' );
    delete_site_transient( 'mywp_select_user_roles_updater_remote' );

    $is_latest = MywpControllerModuleSelectUserRolesUpdater::is_latest();

    if( is_wp_error( $is_latest ) ) {

      wp_send_json_error( array( 'error' => $is_latest->get_error_message() ) );

    }

    if( ! $is_latest ) {

      wp_send_json_success( array( 'is_latest' => 0 ) );

    } else {

      wp_send_json_success( array( 'is_latest' => 1 , 'message' => sprintf( '<p>%s</p>' , '<span class="dashicons dashicons-yes"></span> ' . __( 'Using a latest version.' , 'mywp-select-user-roles' ) ) ) );

    }

  }

  public static function mywp_current_setting_screen_content() {

    $setting_data = self::get_setting_data();

    $all_user_roles = MywpSelectUserRolesApi::get_all_user_roles();

    $count_user_roles = MywpSelectUserRolesApi::get_count_user_roles();

    ?>
    <p><?php printf( __( '%1$s allows the management for only your selected user roles to be customized.' , 'mywp-select-user-roles' ) , MYWP_SELECT_USER_ROLES_NAME ); ?></p>
    <p><?php _e( 'Select the user roles to customize below.' , 'mywp-select-user-roles' ); ?></p>

    <h3><?php _e( 'User Roles' ); ?></h3>
    <ul>

      <?php foreach( $all_user_roles as $user_role => $user_role_data ) : ?>

        <?php $checked = false; ?>

        <?php if( in_array( $user_role , $setting_data['select_user_roles'] , true ) ) : ?>

          <?php $checked = true; ?>

        <?php endif; ?>

        <li>
          <label>
            <input type="checkbox" name="mywp[data][select_user_roles][]" value="<?php echo esc_attr( $user_role ); ?>" <?php checked( $checked , true ); ?> />
            <?php echo esc_html( $user_role_data['label'] ); ?>
            <code>
              <?php if( isset( $count_user_roles[ $user_role ] ) ) : ?>
                <?php printf( __( '%1$s <span class="count">(%2$s)</span>' ) , esc_attr( $user_role ) , number_format_i18n( $count_user_roles[ $user_role ] ) ); ?>
              <?php else :?>
                <?php echo esc_attr( $user_role ); ?>
              <?php endif; ?>
            </code>
          </label>
        </li>

      <?php endforeach; ?>

    </ul>

    <p>&nbsp;</p>
    <?php

  }

  public static function mywp_current_setting_screen_after_footer() {

    $is_latest = MywpControllerModuleSelectUserRolesUpdater::is_latest();

    $have_latest = false;

    if( ! is_wp_error( $is_latest ) && ! $is_latest ) {

      $have_latest = MywpControllerModuleSelectUserRolesUpdater::get_latest();

    }

    $plugin_info = MywpSelectUserRolesApi::plugin_info();

    $class_have_latest = '';

    if( $have_latest ) {

      $class_have_latest = 'have-latest';

    }

    ?>
    <p>&nbsp;</p>
    <h3><?php _e( 'Plugin info' , 'my-wp' ); ?></h3>
    <table class="form-table <?php echo esc_attr( $class_have_latest ); ?>" id="version-check-table">
      <tbody>
        <tr>
          <th><?php printf( __( 'Version %s' ) , '' ); ?></th>
          <td>
            <code><?php echo esc_html( MYWP_SELECT_USER_ROLES_VERSION ); ?></code>
            <a href="<?php echo esc_url( $plugin_info['github'] ); ?>" target="_blank" class="button button-primary link-latest"><?php printf( __( 'Get Version %s' ) , esc_attr( $have_latest ) ); ?></a>
            <p class="already-latest"><span class="dashicons dashicons-yes"></span> <?php _e( 'Using a latest version.' , 'mywp-select-user-roles' ); ?></p>
            <br />
          </td>
        </tr>
        <tr>
          <th><?php _e( 'Check latest' , 'mywp-select-user-roles' ); ?></th>
          <td>
            <button type="button" id="check-latest-version" class="button button-secondary check-latest"><span class="dashicons dashicons-update"></span> <?php _e( 'Check latest version' , 'mywp-select-user-roles' ); ?></button>
            <span class="spinner"></span>
            <div id="check-latest-result"></div>
          </td>
        </tr>
        <tr>
          <th><?php _e( 'Documents' , 'my-wp' ); ?></th>
          <td>
            <a href="<?php echo esc_url( $plugin_info['document_url'] ); ?>" class="button button-secondary" target="_blank"><span class="dashicons dashicons-book"></span> <?php _e( 'Documents' , 'my-wp' ); ?>
          </td>
        </tr>
      </tbody>
    </table>

    <p>&nbsp;</p>
    <?php

  }

  public static function mywp_current_admin_print_footer_scripts() {

?>
<style>
#version-check-table .spinner {
  visibility: hidden;
}
#version-check-table.checking .spinner {
  visibility: visible;
}
#version-check-table .link-latest {
  margin-left: 12px;
  display: none;
}
#version-check-table .already-latest {
  display: inline-block;
}
#version-check-table .check-latest {
}
#version-check-table.have-latest .link-latest {
  display: inline-block;
}
#version-check-table.have-latest .already-latest {
  display: none;
}
</style>
<script>
jQuery(document).ready(function($){

  $('#check-latest-version').on('click', function() {

    var $version_check_table = $(this).parent().parent().parent().parent();

    $version_check_table.addClass('checking');

    PostData = {
      action: '<?php echo esc_js( MywpSetting::get_ajax_action_name( self::$id , 'check_latest' ) ); ?>',
      <?php echo esc_js( MywpSetting::get_ajax_action_name( self::$id , 'check_latest' ) ); ?>: '<?php echo esc_js( wp_create_nonce( MywpSetting::get_ajax_action_name( self::$id , 'check_latest' ) ) ); ?>'
    };

    $.ajax({
      type: 'post',
      url: ajaxurl,
      data: PostData
    }).done( function( xhr ) {

      if( typeof xhr !== 'object' || xhr.success === undefined ) {

        $version_check_table.removeClass('checking');

        alert( mywp_admin_setting.unknown_error_reload_page );

        return false;

      }

      if( ! xhr.success ) {

        $version_check_table.removeClass('checking');

        alert( xhr.data.error );

        return false;

      }

      if( xhr.data.is_latest ) {

        $('#check-latest-result').html( xhr.data.message );

        $version_check_table.removeClass('checking');

        return false;

      }

      location.reload();

      return true;

    }).fail( function( xhr ) {

      $version_check_table.removeClass('checking');

      alert( mywp_admin_setting.unknown_error_reload_page );

      return false;

    });

  });

});
</script>
<?php

  }

  public static function mywp_current_setting_post_data_format_update( $formatted_data ) {

    $mywp_model = self::get_model();

    if( empty( $mywp_model ) ) {

      return $formatted_data;

    }

    $new_formatted_data = $mywp_model->get_initial_data();

    $new_formatted_data['advance'] = $formatted_data['advance'];

    if( ! empty( $formatted_data['select_user_roles'] ) ) {

      foreach( $formatted_data['select_user_roles'] as $user_role ) {

        if( empty( $user_role ) ) {

          continue;

        }

        $new_formatted_data['select_user_roles'][] = strip_tags( $user_role );

      }

    }

    return $new_formatted_data;

  }

}

MywpSettingScreenSelectUserRoles::init();

endif;
