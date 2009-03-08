<?php
// ZendFramework 1.7.6
$include_path  = '/usr/local/share/pear:';
$include_path .= '/usr/home/till/public_html/zf-lib/:';
$include_path .= '.';

set_include_path($include_path);

include 'Zend/Loader.php';

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

$start = microtime(true);
$data  = array();
foreach ($libs as $lib) {
    Zend_Loader::loadClass($lib);
    $data[] = array($lib, round(((microtime(true))-$start), 5));
}
$end = microtime(true);

require_once 'Console/Table.php';

$tbl = new Console_Table();
$tbl->setHeaders(
    array('Lib', 'Time')
);
$tbl->addData($data);
echo $tbl->getTable();

echo "\n\nTotal: " . round(($end-$start), 5) . " s\n";

function gettime()
{
    $a = explode (' ',microtime());
    return(double) $a[0] + $a[1];
}
?>
