<?php
/* Create all packages from xml files from PACKAGES_DIR of MAGENTO_DIR*/

define('MAGENTO_DIR', '1.4_dima');
//define('PACKAGES_DIR', './var/connect');
define('CONNECT_DIR', './var/pear');
define('PACKAGES_DIR', '');

define('CURRENT_RELEASE', '1.4.1.1');
define('DEBUG_MODE', true);

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
    $releasesDir=realpath(CONNECT_DIR.DIRECTORY_SEPARATOR.PACKAGES_DIR.DIRECTORY_SEPARATOR.CURRENT_RELEASE);
    if(DEBUG_MODE)echo("Releases dir is $releasesDir;\n");
    $cnt=0;
    foreach (scandir($releasesDir) as $packageFileXML) {
        $_packageFileXML=$releasesDir . DIRECTORY_SEPARATOR . $packageFileXML;
        if (($packageFileXML != '.') && ($packageFileXML != '..') && substr($packageFileXML,-4)=='.xml' && is_file($_packageFileXML) && filesize($_packageFileXML)>0) {
            if(DEBUG_MODE)echo("Package xml file is $_packageFileXML \n");
            $packageName=(PACKAGES_DIR?PACKAGES_DIR.DIRECTORY_SEPARATOR:'').CURRENT_RELEASE . DIRECTORY_SEPARATOR . substr($packageFileXML,0,-4);
            try {
                $xml = simplexml_load_file($_packageFileXML);
                $data = Mage::helper('core')->xmlToAssoc($xml);
                $data = array_merge($data, array('file_name' => $packageName));
            } catch (Exception $e) {
                echo('Package ' . $packageName . ' failed. '.$e->getMessage()."\n");
                continue;
            }
            try {
                $ext = Mage::getModel('adminhtml/extension');
                $ext->setData($data);
                if (!$ext->savePackage()) {
                    echo('There was a problem saving package '.$packageName." data \n");
                }else{
                    $result = $ext->createPackage();
                    $pear = Varien_Pear::getInstance();
                    if ($result) {
                        $data = $pear->getOutput();
                        echo("Package " . $packageName . " created.\n");
                    } else {
                        $data = $result->getMessage();
                        echo("Package " . $packageName . " failed.\n");
                    }
                    //echo("Message " . $data . "\n");
                    //var_dump($data);
                }
            } catch(Mage_Core_Exception $e){
                echo('Create package ' . $packageName . ' failed. '.$e->getMessage()."\n");
            } catch(Exception $e){
                echo('Create package ' . $packageName . ' failed. '."Failed to create the package. ".$e->getMessage()." \n");
            }
        }//break;
        $cnt++;
    }

} catch (Exception $e) {
    Mage::printException($e);
}
echo("\n");