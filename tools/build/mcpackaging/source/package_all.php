#!/usr/bin/php
<?php
/**
 * Create packages from Magento sources
 *
 * usage:
 *  package_all.php sources_path release_number
 *
 * where
 *  sources_path - path to Magento sources directory
 *  release_number - factually directory name with extensions data files in magento/var/connect/ Ex.:1.5.0.0
 */

define('DEBUG_MODE', false);
define('PACKAGES_DIR', 'var/connect/');
define('DS', DIRECTORY_SEPARATOR);


$MAGENTO_DIR = '1.5.x';
$CURRENT_RELEASE = '';

if (isset($argv) && !empty($argv)) {
    if (isset($argv[1]) && !empty($argv[1])) {
    	$MAGENTO_DIR = realpath($argv[1]);
    }
    if (isset($argv[2]) && !empty($argv[2])) {
        $CURRENT_RELEASE = $argv[2];
    }
}

if (!file_exists($MAGENTO_DIR) || !file_exists($MAGENTO_DIR . DS . PACKAGES_DIR . DS . $CURRENT_RELEASE)) {
    echo("Enter valid sources_path and release_number\n");
    exit(1);
}

chdir($MAGENTO_DIR);

require 'app/Mage.php';

if (!Mage::isInstalled()) {
    echo("Application is not installed yet, please complete install wizard first.\n");
    exit(1);
}
// Only for urls
// Don't remove this
$_SERVER['SCRIPT_NAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_NAME']);
$_SERVER['SCRIPT_FILENAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_FILENAME']);

Mage::app('admin')->setUseSessionInUrl(false);

try {
    $releasesDir=realpath(PACKAGES_DIR . DS . $CURRENT_RELEASE);
    if(DEBUG_MODE)echo("Releases dir is $releasesDir;\n");
    foreach (scandir($releasesDir) as $packageFileXML) {
        $_packageFileXML=$releasesDir . DS . $packageFileXML;
        if (($packageFileXML != '.') &&
                ($packageFileXML != '..') &&
                substr($packageFileXML,-4)=='.xml' &&
                is_file($_packageFileXML) &&
                filesize($_packageFileXML)>0
        ) {
            if(DEBUG_MODE)echo("Package xml file is $_packageFileXML \n");
            //$packageName = '1.9.0.0/Interface_Frontend_Enterprise-1.9.0.0';
            $packageName=$CURRENT_RELEASE . DS . substr($packageFileXML,0,-4);
            try {
                $data = Mage::helper('connect')->loadLocalPackage($packageName);
                if (!$data) {
                    Mage::throwException("Failed to load the package data.\n");
                }
                $data = array_merge($data, array('file_name' => $packageName));
            } catch (Exception $e) {
                echo('Package ' . $packageName . ' failed. '.$e->getMessage()."\n");
                continue;
            }
            try {
                $ext = Mage::getModel('connect/extension');
                $ext->setData($data);
                $ext->createPackage();
                echo("Package " . $packageName . " created.\n");
            } catch(Mage_Core_Exception $e){
                echo('Package ' . $packageName . ' failed. '.$e->getMessage()."\n");
            } catch(Exception $e){
                echo('Package ' . $packageName . ' failed. '."Failed to create the package. ".$e->getMessage()." \n");
            }
        }
    }

} catch (Exception $e) {
    Mage::printException($e);
    exit (1);
}
echo("\n");
exit (0);