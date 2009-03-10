--TEST--
Test autoload: a class in library Zend_*, static
--INI--
include_path=.
display_errors=1
--FILE--
<?php
require_once '../../../Lagged/Loader.php';

define('LAGGED_APPLICATION_DIR', '/foobar'); // doesn't have to exist, we just check format

Lagged_Loader::load('Zend_Controller_Action');
Lagged_Loader::load('Zend_Db');
?>
--EXPECT--
Warning: include(/foobar/library/Zend/Controller/Action.php): failed to open stream: No such file or directory in /usr/home/till/public_html/lagged/lagged/trunk/Lagged/Loader.php on line 321

Warning: include(): Failed opening '/foobar/library/Zend/Controller/Action.php' for inclusion (include_path='.') in /usr/home/till/public_html/lagged/lagged/trunk/Lagged/Loader.php on line 321

Warning: include(/foobar/library/Zend/Db.php): failed to open stream: No such file or directory in /usr/home/till/public_html/lagged/lagged/trunk/Lagged/Loader.php on line 321

Warning: include(): Failed opening '/foobar/library/Zend/Db.php' for inclusion (include_path='.') in /usr/home/till/public_html/lagged/lagged/trunk/Lagged/Loader.php on line 321

