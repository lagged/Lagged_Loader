--TEST--
Test autoload: a class in a module's library folder
--FILE--
<?php
require_once __DIR__ . '/../../../src/Lagged/LibraryLoader.php';

$appDir = '/foo'; // doesn't have to exist, we just check format
$module = 'bar';  // /foo/app/modules/bar

$loader = new Lagged_LibraryLoader($module, $appDir);
var_dump($loader->load('My_Super_Library'));
?>
--EXPECTF--
Warning: include(/foo/app/modules/bar/library/My/Super/Library.php): failed to open stream: No such file or directory in %s/LibraryLoader.php on line %d

Warning: include(): Failed opening '/foo/app/modules/bar/library/My/Super/Library.php' for inclusion (include_path='%s') in %s/LibraryLoader.php on line %d
bool(false)
