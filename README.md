## Lagged_Loader

 * Zend Framework
 * best get _master_, 0.1.0 is an outdated import from Google Code
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
 
### Usage

    <?php
    define('LAGGED_APPLICATION_DIR', '/absolute/path/to/your/app'); // aka 'root'
    require_once '/absolute/path/to/Lagged/Loader.php';
    spl_autoload_register(array('Lagged_Loader', 'loadClass'));
