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

  static private $menu = 'select_user_roles';

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
    );

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_content() {

    $setting_data = self::get_setting_data();

    $all_user_roles = MywpApi::get_all_user_roles();

    $count_user_roles = MywpAddonSelectUserRolesApi::get_count_user_roles();

    ?>
    <p><?php printf( __( '%1$s allows the management for different user role to be customized.' , 'mywp-add-on-user-roles' ) , MYWP_ADD_ON_SELECT_USER_ROLES_NAME ); ?></p>
    <p><?php _e( 'Select the user roles to customize below.' , 'mywp-add-on-user-roles' ); ?></p>

    <h3><?php _e( 'User Roles' ); ?></h3>
    <ul>

      <?php foreach( $all_user_roles as $user_role => $user_role_data ) : ?>

        <?php $checked = false; ?>
        <?php if( in_array( $user_role , $setting_data['select_user_roles'] ) ) : ?>
          <?php $checked = true; ?>
        <?php endif; ?>
        <li>
          <label>
            <input type="checkbox" name="mywp[data][select_user_roles][]" value="<?php echo esc_attr( $user_role ); ?>" <?php checked( $checked , true ); ?> />
            <?php echo $user_role_data['label']; ?>
            <?php if( isset( $count_user_roles[ $user_role ] ) ) : ?>
              <?php printf( __( '%1$s <span class="count">(%2$s)</span>' ), $user_role, number_format_i18n( $count_user_roles[ $user_role ] ) ); ?>
            <?php else :?>
              <?php echo $user_role; ?>
            <?php endif; ?>
          </label>
        </li>

      <?php endforeach; ?>

    </ul>

    <p>&nbsp;</p>
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
