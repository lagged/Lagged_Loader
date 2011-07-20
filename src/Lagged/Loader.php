<?php
/**
 * Copyright (c) 2008-2009, Till Klampaeckel
 * 
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * 
 *  * Redistributions of source code must retain the above copyright notice, this
 *    list of conditions and the following disclaimer.
 *  * Redistributions in binary form must reproduce the above copyright notice, this
 *    list of conditions and the following disclaimer in the documentation and/or
 *    other materials provided with the distribution.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * PHP Version 5
 *
 * @category Core
 * @package  Lagged_Loader
 * @author   Till Klampaeckel <till@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  SVN: $Id$
 * @link     http://code.google.com/p/lagged/
 */
 
/**
 * One autoloader to rule them all. (Or something.)
 *
 * We are assuming the following layout:
 * app/controllers/
 * app/models/
 * app/forms/
 * app/modules/Foo/app/controllers
 * app/modules/Foo/app/models
 * app/modules/Foo/app/forms
 * library/Zend/
 * library/X/
 *
 *
 * @category Core
 * @package  Lagged_Loader
 * @author   Till Klampaeckel <till@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  Release: @package_version@
 * @link     http://code.google.com/p/lagged/
 */
class Lagged_Loader
{
    /**
     * The application's directory.
     *
     * @see self::__construct()
     * @see self::setApplicationPath()
     */
    static $appDir;

    /**
     * Directory variables used by other protected methods.
     *
     * @see self::setDefaultPaths()
     */
    protected $controllerDir;
    protected $formsDir;
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
     * @var string $namespace For custom library code.
     * @see self::setNamespace()
     * @see self::loadClass()
     */
    protected $namespace = null;
    
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
     *     static $loader;
     *     $loader = new Lagged_Loader(dirname(__FILE__));
     *     $loader->setNamespace('Foobar'); // support library/Foobar/*
     *     $loader->loadClass($className);
     * }
     * </code>
     */
    public function __construct($appDir = null)
    {
        if (!empty($appDir)) {
            $this->setDefaultPaths($appDir);
        }
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
     * A singleton style approach. No idea if this does what I want/think but it
     * sure works and it's still faster!
     *
     * This method imitates Zend_Loader::loadClass() and is meant to mass-replace
     * it in the framework code.
     *
     * @param string $className The name of the class.
     * @param mixed  $dirs      We don't use this.
     *
     * @return void
     * @see    Zend_Loader::loadClass()
     */
    static function load($className, $dirs = null)
    {
        if (self::$instance === null) {
            $path = null;
            if (!empty(self::$appDir)) {
                $path = self::$appDir;
            } else {
                $path = LAGGED_APPLICATION_DIR;
            }
            $cls = self::$instance = new Lagged_Loader($path);
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
        }
        if ($this->currentModule != '') {
            $moduleLength = strlen($this->currentModule);
            $moduleLength++; // for '_'
            if (substr($className, $moduleLength, 6) == 'Model_') {
                return $this->loadModel($className);
            }
        }

        /**
         * @desc Load forms, classes need to be prefixed with 'Form_', or
         *       'Module_Form_'.
         */
        if (substr($className, 0, 5) == 'Form_') {
            return $this->loadForm($className);
        }
        if ($this->currentModule != '') {
            $moduleLength = strlen($this->currentModule);
            $moduleLength++; // for '_'
            if (substr($className, $moduleLength, 5) == 'Form_') {
                return $this->loadForm($className);
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
        if (substr($className, 0, 7) == 'Lagged_') { // FIXME: test $this->namespace first
            return $this->loadLibrary($className);
        }
        if ($this->namespace !== null) {
            if (substr($className, 0, (strlen($this->namespace)+1)) == "{$this->namespace}_") {
                return $this->loadLibrary($className);
            }
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
     * Load a form class.
     *
     * @param string $className The class name of the form, e.g. Form_GuestBook
     *                          or Social_Form_GuestBook if within a module.
     * @return mixed
     * @uses   self::loadFormOrModel()
     */
    protected function loadForm($className)
    {
        return $this->loadFormOrModel($className, 'form');
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
        return $this->loadFormOrModel($className, 'model');
    }

    /**
     * Load a form or a model.
     *
     * @param string $className The classname to load.
     * @param string $loadType  Watcha wanna load: 'model' or 'form'.
     *
     * @return mixed
     * @see    self::loadModel()
     * @uses   self::getPath()
     * @uses   self::$formsDir
     * @uses   self::$modelsDir
     * @uses   self::$currentModule
     * @uses   self::$include
     */
    protected function loadFormOrModel($className, $loadType = 'model')
    {
        $path = '';
        $size = 0;

        switch ($loadType) {
        case 'form':
            $path .= $this->getPath($this->formsDir);
            $size += 5;
            break;
        case 'model':
            $path .= $this->getPath($this->modelsDir);
            $size += 6;
            break;
        }


        $file = str_replace('_', '/', $className) . '.php';
        if ($this->currentModule != '') {
            $file = substr($file, (strlen($this->currentModule)+1));
        }
        $file = substr($file, $size);
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
     * Set application path. 
     *
     * @param string $appDir Path of your application, absolute.
     *
     * @return void
     */
    static public function setApplicationPath($appDir)
    {
        self::$appDir = $appDir;
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
        self::$appDir = $appDir;

        $this->controllerDir = self::$appDir . '/modules/__MODULE__/app/controllers';
        $this->formsDir      = self::$appDir . '/modules/__MODULE__/app/forms';
        $this->modelsDir     = self::$appDir . '/modules/__MODULE__/app/models';
        $this->libraryDir    = self::$appDir . '/library';
    }

    /**
     * In case you want your own custom namespace in library, set it here.
     * This obviously shouldn't contain spaces, etc.. The Zend_ and Lagged_
     * namespaces are currently supported by default.
     *
     * @param string $namespace Class namespace, e.g. 'Foo'.
     *
     * @return void
     */
    public function setNamespace($namespace = null)
    {
        if ($namespace === null) {
            return;
        }
        $namespace = trim($namespace);
        if (substr($namespace, -1, 1) == '_') {
            $namespace = substr($namespace, 0, -1);
        }
        if ($namespace == '') {
            return;
        }
        $this->namespace = $namespace;
    }
}
