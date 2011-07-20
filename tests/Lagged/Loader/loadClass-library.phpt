--TEST--
Test autoload: a class in library, e.g. Zend_*
--FILE--
<?php
require_once __DIR__ . '/../../../src/Lagged/Loader.php';

$appDir = '/foobar'; // doesn't have to exist, we just check format

$loader = new Lagged_Loader($appDir);
var_dump($loader->getClassPath('Zend_Controller_Action'));
var_dump($loader->getClassPath('Zend_Db'));
?>
--EXPECT--
string(42) "/foobar/library/Zend/Controller/Action.php"
string(27) "/foobar/library/Zend/Db.php"
