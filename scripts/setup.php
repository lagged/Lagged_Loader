<?php
echo "TEST: $test" . PHP_EOL . PHP_EOL;

$include_path  = '';
$include_path .= $_SERVER['HOME'] . '/Documents/workspaces/imagineeasy_v5/trunk/library:';
$include_path .= '.';

set_include_path($include_path . ':' . get_include_path());

include '../src/Lagged/Loader.php';

$libs   = array();
$libs[] = 'Zend_Auth';
$libs[] = 'Zend_Config_Ini';
$libs[] = 'Zend_Controller_Action';
$libs[] = 'Zend_Controller_Action_HelperBroker';
$libs[] = 'Zend_Controller_Front';
$libs[] = 'Zend_Controller_Plugin_ErrorHandler';
$libs[] = 'Zend_Controller_Router_Route';
$libs[] = 'Zend_Db';
$libs[] = 'Zend_Db_Table_Abstract';
$libs[] = 'Zend_Http_Client';
$libs[] = 'Zend_Mail_Transport_Smtp';
$libs[] = 'Zend_Registry';
$libs[] = 'Zend_Session';
$libs[] = 'Zend_Session_Namespace';
$libs[] = 'Zend_View';
$libs[] = 'Zend_XmlRpc_Client';
$libs[] = 'Zend_XmlRpc_Server';
