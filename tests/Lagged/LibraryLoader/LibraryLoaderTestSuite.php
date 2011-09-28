<?php
require_once 'PHPUnit/Autoload.php';

class Lagged_LibraryLoaderTestSuite
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite(
            'Lagged_Loader Unit Tests'
        );

        $phpt = new PHPUnit_Extensions_PhptTestSuite(__DIR__);
        $suite->addTestSuite($phpt);
 
        return $suite;
    }
}
