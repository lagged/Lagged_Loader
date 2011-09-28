## Lagged_Loader and co.

 * Zend Framework
 * best get _master_ or `0.2.0`, `0.1.0` is an outdated import from Google Code
 * general application structure
   * root/app
   * root/app/modules/default/controllers
   * root/app/modules/default/forms
   * root/app/modules/default/models
   * root/app/modules/foo/controllers
   * root/app/modules/foo/forms
   * root/app/modules/foo/models
   * root/library

### Supports
 
 * controllers
 * forms
 * models
 * _global_ library folder

### Installation

    pear channel-discover easybib.github.com/pear
    pear install easybib/Lagged_Loader-alpha

That's all!

### Usage

Basic usage to load `Zend_`, `Lagged_`, forms, models and controllers:

    <?php
    define('LAGGED_APPLICATION_DIR', '/absolute/path/to/your/app'); // aka 'root'
    require_once '/absolute/path/to/Lagged/Loader.php';
    spl_autoload_register(array('Lagged_Loader', 'loadClass'));

#### Custom namespaces for (global) libraries

In case you have your own libraries in `library/MyNamespace/`:

    <?php
    require_once '/absolute/path/to/Lagged/Loader.php';
    $loader = new Lagged_Loader('/absolute/path/to/your/app');
    $loader->setNamespace('MyNamespace');
    spl_autoload_register(array($loader, 'load'));

#### Library Loading?!

It's neat to be able to distribute a `library/` folder inside a module as well. This allows for
distributing dependencies where they are needed.

Usage is simple:

    <?php
    // if you haven't already:
    //define('LAGGED_APPLICATION_DIR', '...');
    $libraryLoader = new Lagged_LibraryLoader('my-module');
    spl_autoload_register(array($libraryLoader, 'load'));

#### PEAR

**This will only work if you installed this code using the PEAR channel.**

Load your PEAR libs, this part is work in progress:

    <?php
    require_once 'Lagged/PEARLoader.php';
    spl_autoload_register('Lagged_PEARLoader', 'loadClass'));
    
See `tests/` for more examples.
