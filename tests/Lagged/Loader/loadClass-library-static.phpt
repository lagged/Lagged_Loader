--TEST--
Test autoload: a class in library Zend_*, static
--INI--
include_path=.
display_errors=1
--FILE--
<?php
require_once __DIR__ . '/../../../src/Lagged/Loader.php';

define('LAGGED_APPLICATION_DIR', '/foobar'); // doesn't have to exist, we just check format

Lagged_Loader::load('Zend_Controller_Action');
Lagged_Loader::load('Zend_Db');
Lagged_Loader::load('Zend_Form');
?>
--EXPECTF--
Warning: include(/foobar/library/Zend/Controller/Action.php): failed to open stream: No such file or directory in %s/Lagged/Loader.php on line %d

Warning: include(): Failed opening '/foobar/library/Zend/Controller/Action.php' for inclusion (include_path='.') in %s/Lagged/Loader.php on line %d

Warning: include(/foobar/library/Zend/Db.php): failed to open stream: No such file or directory in %s/Lagged/Loader.php on line %d

Warning: include(): Failed opening '/foobar/library/Zend/Db.php' for inclusion (include_path='.') in %s/Lagged/Loader.php on line %d

Warning: include(/foobar/library/Zend/Form.php): failed to open stream: No such file or directory in %s/Lagged/Loader.php on line %d

Warning: include(): Failed opening '/foobar/library/Zend/Form.php' for inclusion (include_path='.') in %s/Lagged/Loader.php on line %d

