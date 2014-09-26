<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| EMAIL CONFIG
| -------------------------------------------------------------------
*/

$config['useragent'] = '';
$config['protocol'] = 'smtp';
$config['mailpath'] = '/usr/sbin/sendmail';
$config['smtp_host'] = '127.0.0.1';
$config['smtp_user'] = '';
$config['smtp_pass'] = '';
$config['smtp_port'] = 25;
$config['smtp_timeout'] = 5;
$config['wordwrap'] = true;
$config['wrapchars'] = 76;
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['validate'] = false;
$config['priority'] = 3;
$config['crlf'] = "\r\n";
$config['newline'] = "\r\n";
$config['bcc_batch_mode'] = false;
$config['bcc_batch_size'] = 200;

$config['from_name'] = '';
$config['from_email'] = '';


/* End of file email.php */
/* Location: ./application/config/email.php */
