#!/usr/bin/env php
<?php
ini_set('display_errors', 'Off');
ini_set('date.timezone', 'Europe/Berlin');

require_once 'PEAR/PackageFileManager2.php';
PEAR::setErrorHandling(PEAR_ERROR_DIE);

$api_version     = '0.5.0';
$api_state       = 'alpha';

$release_version = $api_version;
$release_state   = $api_state;
$release_notes   = " * support Service_Foo and Module_Service_Foo \n"
                 . " * silence warnings";

$description = "A better autoloader for the Zend Framework: \n"
    . "http://github.com/lagged/Lagged_loader \n";

$package = new PEAR_PackageFileManager2();

$package->setOptions(
    array(
        'filelistgenerator' => 'file',
        'simpleoutput'      => true,
        'baseinstalldir'    => '/',
        'packagedirectory'  => './',
        'dir_roles'         => array(
            'src'   => 'php',
            'tests' => 'test',
            'docs'  => 'doc',
        ),
        'exceptions'        => array(
            'README.md' => 'doc',
        ),
        'ignore'            => array(
            '.git*',
            'generate-package.xml.php',
            '*.tgz',
            '*scripts*',
        )
    )
);

$package->setPackage('Lagged_Loader');
$package->setSummary('A better autoloader for the Zend_Framework.');
$package->setDescription($description);
$package->setChannel('easybib.github.com/pear');
$package->setPackageType('php');
$package->setLicense(
    'BSD',
    'http://www.opensource.org/licenses/bsd-license.php'
);

$package->setNotes($release_notes);
$package->setReleaseVersion($release_version);
$package->setReleaseStability($release_state);
$package->setAPIVersion($api_version);
$package->setAPIStability($api_state);

$package->addMaintainer(
    'developer',
    'mischosch',
    'Michael Scholl',
    'michael@sch0ll.de'
);

$package->addMaintainer(
    'lead',
    'till',
    'Till Klampaeckel',
    'till@lagged.biz'
);

/**
 * Generate the list of files in {@link $GLOBALS['files']}
 *
 * @param string $path
 *
 * @return void
 */
function readDirectory($path) {
    foreach (glob($path . '/*') as $file) {
        if (!is_dir($file)) {
            $GLOBALS['files'][] = $file;
        } else {
            readDirectory($file);
        }
    }   
}

$files = array();
readDirectory(__DIR__ . '/src');

/**
 * @desc Strip this from the filename for 'addInstallAs'
 */
$base = __DIR__ . '/';

foreach ($files as $file) {

    $file2 = str_replace($base, '', $file);

    $package->addReplacement(
       $file2,
       'package-info',
       '@package_version@',
       'version'
    );

    $package->addReplacement(
        $file2,
        'pear-config',
        '@php_dir@',
        'php_dir'
    );

    $file2 = str_replace($base, '', $file);
    $package->addInstallAs($file2, str_replace('src/', '', $file2));
}

$package->setPhpDep('5.2.0');

$package->addPackageDepWithChannel(
    'optional',
    'ZF',
    'pear.zfcampus.org',
    '1.10.0'
);

$package->addExtensionDep('required', 'spl');
$package->setPearInstallerDep('1.4.0a7');
$package->generateContents();

if (   isset($_GET['make'])
    || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')
) {
    $package->writePackageFile();
} else {
    $package->debugPackageFile();
}
