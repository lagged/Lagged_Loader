--TEST--
Test autoload: form class
--FILE--
<?php
require_once __DIR__ . '/../../../src/Lagged/Loader.php';

$appDir = '/foobar'; // doesn't have to exist, we just check format

$loader = new Lagged_Loader($appDir);
var_dump($loader->getClassPath('Form_Foo'));
?>
--EXPECT--
string(41) "/foobar/app/modules/default/forms/Foo.php"
