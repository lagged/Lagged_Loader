--TEST--
Test autoload: controller class
--FILE--
<?php
require_once '../../../Lagged/Loader.php';

$appDir = '/foobar'; // doesn't have to exist, we just check format

$loader = new Lagged_Loader($appDir);
var_dump($loader->getClassPath('FooController'));
?>
--EXPECT--
string(41) "/foobar/app/controllers/FooController.php"

