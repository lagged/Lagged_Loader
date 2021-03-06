<?php
/**
 * EasyBib Copyright 2008-2011
 * Modifying, copying, of code contained herein that is not specifically
 * authorized by Imagine Easy Solutions LLC ("Company") is strictly prohibited.
 * Violators will be prosecuted.
 *
 * This restriction applies to proprietary code developed by EasyBib. Code from
 * third-parties or open source projects may be subject to other licensing
 * restrictions by their respective owners.
 *
 * Additional terms can be found at http://www.easybib.com/company/terms
 *
 * PHP Version 5
 *
 * @category Lagged
 * @package  Autoloader
 * @author   Michael Scholl <michael@sch0ll.de>
 * @author   Till Klampaeckel <till@lagged.biz>
 * @license  http://www.easybib.com/company/terms Terms of Service
 * @version  SVN: $Id$
 * @link     http://www.easybib.com
 */

/**
 * Library Autoloader
 *
 * @category Lagged
 * @package  Autoloader
 * @author   Michael Scholl <michael@sch0ll.de>
 * @author   Till Klampaeckel <till@lagged.biz>
 * @license  http://www.easybib.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.easybib.com
 */

class Lagged_LibraryLoader
{
    /**
     * @var string $rootDir Root application directory
     */
    protected $rootDir;

    /**
     * @var string $appDirName Application foldr name
     */
    protected $appDirName;

    /**
     * @var string $module Name of the module which includes the library folder.
     */
    protected $module;

    /**
     * __construct
     *
     * @param string $module
     * @param mixed  $rootDir    If 'null', LAGGED_APPLICATION_DIR is used.
     * @param string $appDirName app|application folder name
     *
     * @return $this
     */
    public function __construct($module, $rootDir = null, $appDirName = 'app')
    {
        $this->module = strtolower($module);

        if ($rootDir === null) {
            $this->rootDir = LAGGED_APPLICATION_DIR;
        } else {
            $this->rootDir = $rootDir;
        }

        $this->appDirName = $appDirName;
    }

    /**
     * Autoloader!
     *
     * @param string $className
     *
     * @return boolean
     */
    public function load($className)
    {
        static $moduleLibraryPath;
        if ($moduleLibraryPath === null) {
            $moduleLibraryPath = $this->rootDir . '/' . $this->appDirName
                . '/modules/' . $this->module . '/library/';
        }

        $file = str_replace('_', '/', $className) . '.php';
        return include $moduleLibraryPath . $file;
    }
}
