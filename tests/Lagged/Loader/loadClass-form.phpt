--TEST--
Test autoload: form class
--FILE--
<?php
require_once '../../../Lagged/Loader.php';

$appDir = '/foobar'; // doesn't have to exist, we just check format

$loader = new Lagged_Loader($appDir);
var_dump($loader->getClassPath('Form_Foo'));
?>
--EXPECT--
string(25) "/foobar/app/forms/Foo.php"
