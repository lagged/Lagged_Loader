--TEST--
Test autoload: form class in a module
--FILE--
<?php
require_once __DIR__ . '/../../../src/Lagged/Loader.php';

$appDir = '/foobar'; // doesn't have to exist, we just check format

$loader = new Lagged_Loader($appDir);
var_dump($loader->getClassPath('Bar_Service_Foo'));
?>
--EXPECT--
string(40) "/foobar/app/modules/bar/services/Foo.php"

