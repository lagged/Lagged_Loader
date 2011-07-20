--TEST--
Test autoload: controller class in a module
--FILE--
<?php
require_once __DIR__ . '/../../../src/Lagged/Loader.php';

$appDir = '/foobar'; // doesn't have to exist, we just check format

$loader = new Lagged_Loader($appDir);
var_dump($loader->getClassPath('Bar_FooController'));
?>
--EXPECT--
string(53) "/foobar/app/modules/bar/controllers/FooController.php"

