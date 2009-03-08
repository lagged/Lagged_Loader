--TEST--
Test autoload: controller class in a module
--FILE--
<?php
$rootDir = dirname(dirname(__FILE__));
require_once $rootDir . '/library/Lagged/Loader.php';

$appDir = '/foobar'; // doesn't have to exist, we just check format

$loader = new Lagged_Loader($appDir);
var_dump($loader->getClassPath('Bar_FooController'));
?>
--EXPECT--
string(53) "/foobar/modules/Bar/app/controllers/FooController.php"

