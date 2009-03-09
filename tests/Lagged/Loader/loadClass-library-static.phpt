--TEST--
Test autoload: a class in library Zend_*, static
--FILE--
<?php
require_once '../../../Lagged/Loader.php';

set_include_path('.');
ini_set('display_errors', 1);

define('LAGGED_APPLICATION_DIR', '/foobar'); // doesn't have to exist, we just check format

Lagged_Loader::load('Zend_Controller_Action');
Lagged_Loader::load('Zend_Db');
?>
--EXPECT--
Warning: include(/foobar/library/Zend/Controller/Action.php): failed to open stream: No such file or directory in /usr/home/till/public_html/lagged/lagged/trunk/Lagged/Loader.php on line 313

Warning: include(): Failed opening '/foobar/library/Zend/Controller/Action.php' for inclusion (include_path='.') in /usr/home/till/public_html/lagged/lagged/trunk/Lagged/Loader.php on line 313

Warning: include(/foobar/library/Zend/Db.php): failed to open stream: No such file or directory in /usr/home/till/public_html/lagged/lagged/trunk/Lagged/Loader.php on line 313

Warning: include(): Failed opening '/foobar/library/Zend/Db.php' for inclusion (include_path='.') in /usr/home/till/public_html/lagged/lagged/trunk/Lagged/Loader.php on line 313

