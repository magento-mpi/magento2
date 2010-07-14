<?php
/* Create all packages from xml files from PACKAGES_DIR of MAGENTO_DIR (see constants below) */

die("This is console script, try pfp -f package_all.php \n");

define('MAGENTO_DIR', 'magento_rc3');
define('PACKAGES_DIR', './var/connect/Enterprise');
define('CURRENT_RELEASE', '1.9.0.0');
define('DEBUG_MODE', false);

chdir(MAGENTO_DIR);

require 'app/Mage.php';

if (!Mage::isInstalled()) {
    echo "Application is not installed yet, please complete install wizard first.";
    exit;
}
// Only for urls
// Don't remove this
$_SERVER['SCRIPT_NAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_NAME']);
$_SERVER['SCRIPT_FILENAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_FILENAME']);

Mage::app('admin')->setUseSessionInUrl(false);

try {
    $releasesDir=realpath(PACKAGES_DIR.DIRECTORY_SEPARATOR.CURRENT_RELEASE);
    if(DEBUG_MODE)echo("Releases dir is $releasesDir;\n");
    foreach (scandir($releasesDir) as $packageFileXML) {
        $_packageFileXML=$releasesDir . DIRECTORY_SEPARATOR . $packageFileXML;
        if (($packageFileXML != '.') && ($packageFileXML != '..') && substr($packageFileXML,-4)=='.xml' && is_file($_packageFileXML) && filesize($_packageFileXML)>0) {
            if(DEBUG_MODE)echo("Package xml file is $_packageFileXML \n");
            //$packageName = '1.9.0.0/Interface_Frontend_Enterprise-1.9.0.0';
            $packageName=CURRENT_RELEASE . DIRECTORY_SEPARATOR . substr($packageFileXML,0,-4);
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
}
echo("\n");