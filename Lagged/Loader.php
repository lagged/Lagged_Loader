<?php
/**
 * One autoloader to rule them all. (Or something.)
 *
 * PHP Version 5
 *
 * @author Till Klampaeckel <klampaeckel@lagged.de>
 */
 
/**
 * We are assuming the following layout:
 * app/controllers/
 * app/models/
 * app/modules/Foo/app/controllers
 * app/modules/Foo/app/models
 * library/Zend/
 * library/X/
 */
class Lagged_Loader
{
    /**
     * Directory variables.
     * @see self::__construct()
     */
    protected $appDir;
    protected $controllerDir;
    protected $libraryDir;
    protected $modelsDir;

    /**
     * @var string $currentModule The module we are autoloading for.
     */
    protected $currentModule = '';
    
    /**
     * @var string $defaultModule The name of the default module for the paths,
     *                            sometimes e.g. app/default/controller or sometimes
     *                            app/controller.
     */
    protected $defaultModule = '';
    
    /**
     * @var bool $include Set to false for unit-testing the code.
     * @see self::getClassPath()
     */
    protected $include = true;

    static $instance = null;
    
    /**
     * Lagged_Loader::__construct()
     *
     * @param string $appDir The root of the application.
     * @param string $module The current module, when 'empty', default is assumed.
     *
     * @return Lagged_Loader
     * @uses   self::setDefaultPaths()
     *
     * <code>
     * function __autoload($className)
     * {
     *     static $loader = new Lagged_Loader(dirname(__FILE__));
     *     $loader->loadClass($className);
     * }
     */
    public function __construct($appDir)
    {
        $this->setDefaultPaths($appDir);
    }
    
    /**
     * Attempts to find the current module we are in.
     *
     * @param string $className The name of the class.
     *
     * @return void
     * @uses   self::currentModule
     */
    protected function detectModule($className)
    {
        if (substr($className, 0, 6) == 'Model_') {
            return;
        }
        if (strstr($className, '_Model_')) {
            $moduleEnd = strpos($className, '_', 0);
            if ($moduleEnd > 0) {
                if (substr($className, ($moduleEnd+1), 6) == 'Model_') {
                    $this->currentModule = substr($className, 0, ($moduleEnd));
                    return;
                }
            }
        }
        if (substr($className, -10) == 'Controller'
            && substr($className, -11) != '_Controller') {
            $moduleEnd = strpos($className, '_');
            if ($moduleEnd > 0) {
                $this->currentModule = substr($className, 0, ($moduleEnd));
                return;
            }
        }
        return;
    }
    
    /**
     * This is a public method for unit-testing the autoloader.
     *
     * @param  string $className The name of the class to load.
     *
     * @return string
     * @uses   self::loadClass()
     */
    public function getClassPath($className)
    {
        $this->include = false;

        $path = $this->loadClass($className);

        $this->reset();

        return $path;
    }

    /**
     * Generate the paht to use for include.
     *
     * @param string $path The path, most likely a placeholder.
     *
     * @return string
     */
    protected function getPath($path)
    {
        if ($this->currentModule != '') {
            $path = str_replace('__MODULE__', $this->currentModule, $path);
        } else {
            $path = str_replace('modules/__MODULE__/', '', $path);
        }
        return $path;
    }

    /**
     * This mocks {@link Zend_Loader::isReadable()}. Shouldn't be used in
     * development, but obviously faster in production.
     *
     * @param mixed $file A filename, path.
     *
     * @return true
     */
    public static function isReadable($file)
    {
        return true;
    }

    /**
     * Get a singleton.
     *
     */
    static function load($className, $dirs = null)
    {
        if (self::$instance === null) {
            $cls = self::$instance = new Lagged_Loader(LAGGED_APPLICATION_DIR);
        } else {
            $cls = self::$instance;
        }
        $cls->loadClass($className, $dirs);
    }

    /**
     * Load a class. :-)
     *
     * @param string $className The classname from __autoload(), e.g. Model_FooBar,
     *                          or FooController, Zend_Db, Bar_FooController.
     * @param mixed  $dirs      This mocks {@link Zend_Loader::loadClass()}.
     *
     * @return mixed
     */
    public function loadClass($className, $dirs = null)
    {
        /**
         * @desc Auto-detect the current module which we are autoloading "from".
         */
        $this->detectModule($className);
        
        /**
         * @desc Load models, classes need to be prefixed with 'Model_', or
         *       'Module_Model_'.
         */
        if (substr($className, 0, 6) == 'Model_') {
            return $this->loadModel($className);
            return;
        }
        if ($this->currentModule != '') {
            $moduleLength = strlen($this->currentModule);
            $moduleLength++; // for '_'
            if (substr($className, $moduleLength, 6) == 'Model_') {
                return $this->loadModel($className);
            }
        }
        
        /**
         * @desc Load controllers, e.g. 'FooController', or 'Module_FooController'.
         *       Unfortunately controllers break the one naming convention in the
         *       Zend Framework.
         */
        if (substr($className, -10) == 'Controller'
            && substr($className, -11) != '_Controller') { // avoid Foo_Bar_Controller
            return $this->loadController($className);
        }
        
        if (substr($className, 0, 5) == 'Zend_') {
            return $this->loadLibrary($className);
        }
        if (substr($className, 0, 7) == 'Lagged_') {
            return $this->loadLibrary($className);
        }
    }

    /**
     * Load a controller
     *
     * @param string $className E.g., FooController, or Module_FooController.
     *
     * @return mixed
     */
    protected function loadController($className)
    {
        $path = $this->getPath($this->controllerDir) . '/';
        if ($this->currentModule == '') {
            $path .= $className . '.php';
        } else {
            $path .= str_replace($this->currentModule . '_', '', $className);
            $path .= '.php';
        }
        if ($this->include === true) {
            return include $path;
        }
        return $path;
    }

    /**
     * Load a library, either include it, or return the path.
     *
     * @param string $className E.g., Zend_Db, or Lagged_Foobar.
     *
     * @return mixed
     */
    protected function loadLibrary($className)
    {
        $path = $this->libraryDir . '/' . str_replace('_', '/', $className) . '.php';
        if ($this->include === true) {
            return include $path;
        }
        return $path;
    }

    /**
     * Load a model.
     *
     * @param string $className
     *
     * @return mixed
     */
    protected function loadModel($className)
    {
        $path = $this->getPath($this->modelsDir);
        $file = str_replace('_', '/', $className) . '.php';
        if ($this->currentModule != '') {
            $file = substr($file, (strlen($this->currentModule)+1));
        }
        $file = substr($file, 6);
        $path .= '/' . $file;
        if ($this->include === true) {
            return include $path;
        }
        return $path;
    }

    /**
     * Reset class for unit testing.
     *
     * @return void
     * @uses   self::$currentModule
     */
    public function reset()
    {
        $this->currentModule = null;
    }

    /**
     * Set the default paths.
     *
     * @param string $appDir Application root.
     *
     * @return void
     * @see    self::__construct()
     * @see    self::loadClass()
     */
    protected function setDefaultPaths($appDir)
    {
        $this->appDir = $appDir;

        $this->controllerDir = $this->appDir . '/modules/__MODULE__/app/controllers';
        $this->modelsDir     = $this->appDir . '/modules/__MODULE__/app/models';
        $this->libraryDir    = $this->appDir . '/library';
    }
}
?>
