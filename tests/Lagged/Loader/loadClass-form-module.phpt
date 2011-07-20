--TEST--
Test autoload: form class in a module
--FILE--
<?php
require_once __DIR__ . '/../../../src/Lagged/Loader.php';

$appDir = '/foobar'; // doesn't have to exist, we just check format

$loader = new Lagged_Loader($appDir);
var_dump($loader->getClassPath('Bar_Model_Foo'));
?>
--EXPECT--
string(38) "/foobar/app/modules/Bar/models/Foo.php"

