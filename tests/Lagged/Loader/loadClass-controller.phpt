--TEST--
Test autoload: controller class
--FILE--
<?php
require_once __DIR__ . '/../../../src/Lagged/Loader.php';

$appDir = '/foobar'; // doesn't have to exist, we just check format

$loader = new Lagged_Loader($appDir);
var_dump($loader->getClassPath('FooController'));
?>
--EXPECT--
string(57) "/foobar/app/modules/default/controllers/FooController.php"

