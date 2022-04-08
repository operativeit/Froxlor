<?php

/**
 * This file is part of the Froxlor project.
 * Copyright (c) 2003-2009 the SysCP Team (see authors).
 * Copyright (c) 2010 the Froxlor Team (see authors).
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code. You can also view the
 * COPYING file online at http://files.froxlor.org/misc/COPYING.txt
 *
 * @copyright  (c) the authors
 * @author     Florian Lippert <flo@syscp.org> (2003-2009)
 * @author     Froxlor team <team@froxlor.org> (2010-)
 * @license    GPLv2 http://files.froxlor.org/misc/COPYING.txt
 * @package    Settings
 *
 */
return array(
	'groups' => array(
		'accounts' => array(
			'title' => $lng['admin']['accountsettings'],
			'icon' => 'fa-solid fa-users-gear',
			'fields' => array(
				'session_sessiontimeout' => array(
					'label' => $lng['serversettings']['session_timeout'],
					'settinggroup' => 'session',
					'varname' => 'sessiontimeout',
					'type' => 'number',
					'min' => 60,
					'default' => 600,
					'save_method' => 'storeSettingField'
				),
				'session_allow_multiple_login' => array(
					'label' => $lng['serversettings']['session_allow_multiple_login'],
					'settinggroup' => 'session',
					'varname' => 'allow_multiple_login',
					'type' => 'checkbox',
					'default' => false,
					'save_method' => 'storeSettingField'
				),
				'login_domain_login' => array(
					'label' => $lng['serversettings']['login_domain_login'],
					'settinggroup' => 'login',
					'varname' => 'domain_login',
					'type' => 'checkbox',
					'default' => false,
					'save_method' => 'storeSettingField'
				),
				'login_maxloginattempts' => array(
					'label' => $lng['serversettings']['maxloginattempts'],
					'settinggroup' => 'login',
					'varname' => 'maxloginattempts',
					'type' => 'number',
					'min' => 1,
					'default' => 3,
					'save_method' => 'storeSettingField'
				),
				'login_deactivatetime' => array(
					'label' => $lng['serversettings']['deactivatetime'],
					'settinggroup' => 'login',
					'varname' => 'deactivatetime',
					'type' => 'number',
					'min' => 0,
					'default' => 900,
					'save_method' => 'storeSettingField'
				),
				'2fa_enabled' => array(
					'label' => $lng['2fa']['2fa_enabled'],
					'settinggroup' => '2fa',
					'varname' => 'enabled',
					'type' => 'checkbox',
					'default' => true,
					'save_method' => 'storeSettingField'
				),
				'panel_password_min_length' => array(
					'label' => $lng['serversettings']['panel_password_min_length'],
					'settinggroup' => 'panel',
					'varname' => 'password_min_length',
					'type' => 'number',
					'min' => 0,
					'default' => 0,
					'save_method' => 'storeSettingField'
				),
				'panel_password_alpha_lower' => array(
					'label' => $lng['serversettings']['panel_password_alpha_lower'],
					'settinggroup' => 'panel',
					'varname' => 'password_alpha_lower',
					'type' => 'checkbox',
					'default' => true,
					'save_method' => 'storeSettingField'
				),
				'panel_password_alpha_upper' => array(
					'label' => $lng['serversettings']['panel_password_alpha_upper'],
					'settinggroup' => 'panel',
					'varname' => 'password_alpha_upper',
					'type' => 'checkbox',
					'default' => true,
					'save_method' => 'storeSettingField'
				),
				'panel_password_numeric' => array(
					'label' => $lng['serversettings']['panel_password_numeric'],
					'settinggroup' => 'panel',
					'varname' => 'password_numeric',
					'type' => 'checkbox',
					'default' => false,
					'save_method' => 'storeSettingField'
				),
				'panel_password_special_char_required' => array(
					'label' => $lng['serversettings']['panel_password_special_char_required'],
					'settinggroup' => 'panel',
					'varname' => 'password_special_char_required',
					'type' => 'checkbox',
					'default' => false,
					'save_method' => 'storeSettingField'
				),
				'panel_password_special_char' => array(
					'label' => $lng['serversettings']['panel_password_special_char'],
					'settinggroup' => 'panel',
					'varname' => 'password_special_char',
					'type' => 'text',
					'default' => '!?<>§$%+#=@',
					'save_method' => 'storeSettingField'
				),
				'panel_password_regex' => array(
					'label' => $lng['serversettings']['panel_password_regex'],
					'settinggroup' => 'panel',
					'varname' => 'password_regex',
					'type' => 'text',
					'default' => '',
					'save_method' => 'storeSettingField',
					'advanced_mode' => true
				),
				'customer_accountprefix' => array(
					'label' => $lng['serversettings']['accountprefix'],
					'settinggroup' => 'customer',
					'varname' => 'accountprefix',
					'type' => 'text',
					'default' => '',
					'plausibility_check_method' => array(
						'\\Froxlor\\Validate\\Check',
						'checkUsername'
					),
					'save_method' => 'storeSettingField'
				),
				'customer_mysqlprefix' => array(
					'label' => $lng['serversettings']['mysqlprefix'],
					'settinggroup' => 'customer',
					'varname' => 'mysqlprefix',
					'type' => 'text',
					'default' => '',
					'plausibility_check_method' => array(
						'\\Froxlor\\Validate\\Check',
						'checkUsername'
					),
					'save_method' => 'storeSettingField'
				),
				'customer_ftpprefix' => array(
					'label' => $lng['serversettings']['ftpprefix'],
					'settinggroup' => 'customer',
					'varname' => 'ftpprefix',
					'type' => 'text',
					'default' => '',
					'save_method' => 'storeSettingField'
				),
				'customer_ftpatdomain' => array(
					'label' => $lng['serversettings']['ftpdomain'],
					'settinggroup' => 'customer',
					'varname' => 'ftpatdomain',
					'type' => 'checkbox',
					'default' => false,
					'save_method' => 'storeSettingField'
				),
				'panel_allow_preset' => array(
					'label' => $lng['serversettings']['allow_password_reset'],
					'settinggroup' => 'panel',
					'varname' => 'allow_preset',
					'type' => 'checkbox',
					'default' => false,
					'save_method' => 'storeSettingField',
					'dependency' => array(
						'fieldname' => 'panel_allow_preset_admin',
						'fielddata' => array(
							'settinggroup' => 'panel',
							'varname' => 'allow_preset_admin'
						),
						'onlyif' => 0
					)
				),
				'panel_allow_preset_admin' => array(
					'label' => $lng['serversettings']['allow_password_reset_admin'],
					'settinggroup' => 'panel',
					'varname' => 'allow_preset_admin',
					'type' => 'checkbox',
					'default' => false,
					'save_method' => 'storeSettingField',
					'dependency' => array(
						'fieldname' => 'panel_allow_preset',
						'fielddata' => array(
							'settinggroup' => 'panel',
							'varname' => 'allow_preset'
						),
						'onlyif' => 1
					)
				),
				'system_backupenabled' => array(
					'label' => $lng['serversettings']['backupenabled'],
					'settinggroup' => 'system',
					'varname' => 'backupenabled',
					'type' => 'checkbox',
					'default' => false,
					'cronmodule' => 'froxlor/backup',
					'save_method' => 'storeSettingField'
				),
				'system_createstdsubdom_default' => array(
					'label' => $lng['serversettings']['createstdsubdom_default'],
					'settinggroup' => 'system',
					'varname' => 'createstdsubdom_default',
					'type' => 'select',
					'default' => '1',
					'select_var' => array(
						'0' => $lng['panel']['no'],
						'1' => $lng['panel']['yes']
					),
					'save_method' => 'storeSettingField'
				),
			)
		)
	)
);
