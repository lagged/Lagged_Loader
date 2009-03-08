--TEST--
Test autoload: model class in a module
--FILE--
<?php
$rootDir = dirname(dirname(__FILE__));
require_once $rootDir . '/library/Lagged/Loader.php';

$appDir = '/foobar'; // doesn't have to exist, we just check format

$loader = new Lagged_Loader($appDir);
var_dump($loader->getClassPath('Bar_Model_Foo'));
?>
--EXPECT--
string(38) "/foobar/modules/Bar/app/models/Foo.php"

