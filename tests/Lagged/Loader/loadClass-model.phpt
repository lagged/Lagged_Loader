--TEST--
Test autoload: model class
--FILE--
<?php
require_once __DIR__ . '/../../../src/Lagged/Loader.php';

$appDir = '/foobar'; // doesn't have to exist, we just check format

$loader = new Lagged_Loader($appDir);
var_dump($loader->getClassPath('Model_Foo'));
?>
--EXPECT--
string(26) "/foobar/app/models/Foo.php"

