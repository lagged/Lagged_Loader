--TEST--
Test autoload: form class
--FILE--
<?php
require_once __DIR__ . '/../../../src/Lagged/Loader.php';

$appDir = '/foobar'; // doesn't have to exist, we just check format

$loader = new Lagged_Loader($appDir);
var_dump($loader->getClassPath('Service_Foo'));
?>
--EXPECT--
string(44) "/foobar/app/modules/default/services/Foo.php"
