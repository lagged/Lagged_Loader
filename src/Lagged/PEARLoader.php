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
 * @author   Till Klampaeckel <till@lagged.biz>
 * @license  http://www.easybib.com/company/terms Terms of Service
 * @version  SVN: $Id$
 * @link     http://www.easybib.com
 */

/**
 * PEARloader
 *
 * Autoload for PEAR Packages.
 *
 * @category Lagged
 * @package  Autoloader
 * @author   Till Klampaeckel <till@lagged.biz>
 * @license  http://www.easybib.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.easybib.com
 */
class Lagged_PEARLoader
{
    /**
     * @var string $appDir Application directory (root)
     */
    protected static $pearDir = '@php_dir@';

    /**
     * __construct
     */
    private function __construct()
    {
    }

    /**
     * Autoloader!
     *
     * @param string $className
     *
     * @return boolean
     */
    public static function load($className)
    {
        $file = str_replace('_', '/', $className) . '.php';
        return include self::$pearDir . '/' . $file;
    }
}
