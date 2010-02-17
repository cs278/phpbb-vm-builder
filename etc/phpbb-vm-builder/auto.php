<?php

$lang = 'en';

$dbms = 'firebird';

$user = array(
	'name'		=> 'phpBB',
	'password'	=> 'phpbbR0ckZ!',
	'email'		=> 'null@cs278.org',
);

$base_url = 'http://localhost/';
$base_url .= 'install/index.php?mode=install&language=' . $lang;

$data = array(
	'submit'	=> true,
	'language'	=> $lang,
);

$data['dbms'] = $dbms;
$data['table_prefix'] = 'phpbb_';

switch ($dbms)
{
	case 'mysql':
	case 'mysqli':
		$data = array_merge($data, array(
			'dbhost'		=> 'localhost',
			'dbport'		=> '',
			'dbname'		=> 'phpbb',
			'dbuser'		=> 'root',
			'dbpasswd'		=> 'phpbb',
		));
	break;

	case 'firebird':
		$data = array_merge($data, array(
			'dbhost'	=> '',
			'dbport'	=> '',
			'dbname'	=> '/var/tmp/phpbb.db',
			'dbuser'	=> 'SYSDBA',
			'dbpasswd'	=> 'phpbb',
		));
	break;

	case 'sqlite':
		$data = array_merge($data, array(
			'dbhost'	=> '/var/tmp/phpbb.sqlite',
			'dbport'		=> '',
			'dbname'		=> '',
			'dbuser'		=> '',
			'dbpasswd'		=> '',
		));
	break;
}

$data = array_merge($data, array(
	'default_lang'	=> $lang,
	'admin_name'	=> $user['name'],
	'admin_pass1'	=> $user['password'],
	'admin_pass2'	=> $user['password'],
	'board_email1'	=> $user['email'],
	'board_email2'	=> $user['email'],
));

$data = array_merge($data, array(
	'email_enable'		=> false,
	'smtp_delivery'		=> false,
	'smtp_host'			=> '',
	'smtp_auth'			=> '',
	'smtp_user'			=> '',
	'smtp_pass'			=> '',
	'cookie_secure'		=> false,
	'force_server_vars'	=> false,
	'server_protocol'	=> 'http://',
	'server_name'		=> 'localhost',
	'server_port'		=> 10080,
	'script_path'		=> '/',
));

/**
15		img_imagick	hidden					
16		ftp_path	hidden					
17		ftp_user	hidden					
18		ftp_pass	hidden	
**/

foreach (array('config_file', 'create_table', 'final') as $sub)
{
	$handle = curl_init();
	curl_setopt_array($handle, array(
		CURLOPT_POST			=> true,
		CURLOPT_POSTFIELDS		=> $data,
		CURLOPT_URL				=> $base_url . '&sub=' . $sub,
		CURLOPT_RETURNTRANSFER	=> true,
	));
	$result = curl_exec($handle);
	curl_close($handle);
}
