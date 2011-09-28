<?php
include __DIR__ . '/setup.php';

define('LAGGED_APPLICATION_DIR', '~/Documents/workspaces/imagineeasy_v5/trunk/');

function __autoload($className) {
    Lagged_Loader::load($className);
}

$start = microtime(true);
$data  = array();
foreach ($libs as $lib) {
    Lagged_Loader::load($lib);
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
