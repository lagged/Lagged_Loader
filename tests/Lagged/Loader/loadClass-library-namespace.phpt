--TEST--
Test autoload: a class in library within the ez_ namespace
--FILE--
<?php
require_once __DIR__ . '/../../../src/Lagged/Loader.php';

$appDir = '/foobar'; // doesn't have to exist, we just check format

$loader = new Lagged_Loader($appDir);
$loader->setNamespace('ez');
var_dump($loader->getClassPath('ez_Session'));
var_dump($loader->getClassPath('ez_Core_Acl'));
?>
--EXPECT--
string(30) "/foobar/library/ez/Session.php"
string(31) "/foobar/library/ez/Core/Acl.php"
