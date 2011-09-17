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
    protected $module;
    
    public function __construct($module)
    {
        $this->module = $module;
    }

    public function load($className)
    {   
        $moduleLibraryPath = APPLICATION_PATH . '/modules/'.strtolower($this->module).'/library/';
        $file = str_replace('_', '/', $className) . '.php';
        return include $moduleLibraryPath . $file;
    }
}
