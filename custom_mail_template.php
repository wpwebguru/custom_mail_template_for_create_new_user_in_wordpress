<?php
/**
 * Plugin Name: Custom Mail Template
 * Description: Custom Mail Template for create a new user
 * Version: 1.1.1
 * Plugin URI: https://github.com/wpwebguru
 * Author: Sunny
 * Author URI: https://github.com/wpwebguru
 */

    // WP Change Default From Email
	function kbsd_change_my_from_address( $original_email_address ) {
		return get_option('kbsd_from_email'); // booking@thecastle.kitchen
	}
	add_filter( 'wp_mail_from', 'kbsd_change_my_from_address' );

	function kbsd_change_my_sender_name( $original_email_from ) {
		return get_option('kbsd_from_name'); // The Castle Kitchen
	}
	add_filter( 'wp_mail_from_name', 'kbsd_change_my_sender_name' );


    // password transfer
    add_action('user_register', 'kbsd_user_profile_update_action');
    add_action('personal_options_update', 'kbsd_user_profile_update_action');
    add_action('edit_user_profile_update', 'kbsd_user_profile_update_action');
    function kbsd_user_profile_update_action($user_id) {
        if ( !current_user_can( 'edit_user', $user_id ) )
                return false; 
        update_usermeta($user_id, 'kbsd_pass', $_POST['pass1']);
    }


    /**
     * Custom register email
     */
    add_filter( 'wp_new_user_notification_email', 'kbsd_custom_wp_new_user_notification_email', 10, 3 );
    function kbsd_custom_wp_new_user_notification_email( $wp_new_user_notification_email, $user, $blogname ) {

        $user_login = stripslashes( $user->user_login );
        $user_email = stripslashes( $user->user_email );
        $user_pass = stripslashes( get_user_meta($user->ID, 'kbsd_pass', true));
        $from_name = get_option('kbsd_from_name');
        $before_msg = get_option('kbsd_custom_msg_before');
        $after_msg = get_option('kbsd_custom_msg_after');
        $header = get_option('kbsd_header_name');
        $login_url	= get_option('kbsd_login_url');

        $message .= __( '<h2>'.$before_msg.'</h2>' );
        $message .= __( '<h3>Here are your login-data:</h3>' );
        $message .= __('<b>Username: </b> ') .$user_login .'<br>';
        $message .= __('<b>Email: </b> '). $user_email .'<br>';
        $message .= __('<b>Password: </b> '). $user_pass .'<br>';
        $message .= __('<a href=" '.$login_url.' " target="_blank">Login here</a>');
        $message .= __( '<h4>'.$after_msg.'</h4>' );

        $wp_new_user_notification_email['subject'] = $header;
        $wp_new_user_notification_email['headers'] = array('Content-Type: text/html; charset=UTF-8');
        $wp_new_user_notification_email['message'] = $message;

        return $wp_new_user_notification_email;
    }


  // Register main menu page.
	 
	add_action( 'admin_menu', 'kbsd_register_menu_func' );
	function kbsd_register_menu_func() {
		global $submenu;
		
		add_submenu_page( 'options-general.php', 'Mail Template', 'Custom Mail Template', 'manage_options', 'custom-mail-template', 'kbsd_custom_mail_template');
		
	}
    function kbsd_custom_mail_template(){ ?>
        
<div class="wrap" id="kbsd_mail_template">
    <h1>Custom Mail Template</h1>
    <form action="options.php" method="post">
        <?php wp_nonce_field('update-options'); ?>

        <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><label for="kbsd_from_name">FROM Name</label></th>
                <td><input name="kbsd_from_name" type="text" id="kbsd_from_name" class="regular-text" value="<? echo get_option('kbsd_from_name'); ?>"></td>
            </tr>
            <tr>
                <th scope="row"><label for="kbsd_from_email">FROM Email Address</label></th>
                <td><input name="kbsd_from_email" type="text" id="kbsd_from_email" class="regular-text" value="<? echo get_option('kbsd_from_email'); ?>"></td>
            </tr>

            <tr>
                <th scope="row"><label for="kbsd_header_name">Header Name</label></th>
                <td><input name="kbsd_header_name" type="text" id="kbsd_header_name" class="regular-text" value="<? echo get_option('kbsd_header_name'); ?>"></td>
            </tr>

            <tr>
                <th scope="row"><label for="kbsd_login_url">Login Url</label></th>
                <td><input name="kbsd_login_url" type="text" id="kbsd_login_url" class="regular-text" value="<? echo get_option('kbsd_login_url'); ?>"></td>
            </tr>

            <tr>
                <th scope="row"><label for="kbsd_custom_msg_before">Message Before Login info</label></th>
                <td>
                    <textarea name="kbsd_custom_msg_before" id="kbsd_custom_msg_before" cols="50" rows="5"><? echo get_option('kbsd_custom_msg_before'); ?></textarea>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="kbsd_custom_msg_after">Message After Login info</label></th>
                <td>
                    <textarea name="kbsd_custom_msg_after" id="kbsd_custom_msg_after" cols="50" rows="5"><? echo get_option('kbsd_custom_msg_after'); ?></textarea>
                </td>
            </tr>

            <tr>
                <th></th>
                <td>
                    <input type="hidden" name="action" value="update" />
                    <input type="hidden" name="page_options" value="kbsd_from_name, kbsd_from_email, kbsd_header_name, kbsd_login_url, kbsd_custom_msg_before, kbsd_custom_msg_after" />
                    <input type="submit" class="button button-primary" id="submit" name="submit" value="Save Changes" />
                </td>
            </tr>
        </tbody>
        </table>
    </form>
</div>


<?php
    }

    // WordPress Plugin Add Settings Link
	add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'kbsd_add_plugin_page_settings_link');
	function kbsd_add_plugin_page_settings_link( $links ) {
		$links[] = '<a href="' .
			admin_url( 'options-general.php?page=custom-mail-template' ) .
			'">' . __('Settings') . '</a>';
		return $links;
	}
