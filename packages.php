<?php

set_include_path(get_include_path().PATH_SEPARATOR.'./lib');

require_once "Varien/Pear.php";
require_once "Varien/Pear/Package/Varien.php";

Varien_Pear::getInstance()->run('channel-discover', array(), array('var-dev.varien.com'));

$pkg = new Varien_Pear_Package_Varien();
$pkg->generatePackage();

/*
class Lib_Varien extends Varien_Pear_Package {
    public function __construct()
    {
        $this->setImportOptions(array(
            'baseinstalldir'=>'Moshe',
            'packagedirectory'=>'/home/moshe/dev/magento/lib/Varien',
            'outputdirectory'=>dirname(__FILE__).'/',
            'filelistgenerator'=>'svn',
            'ignore'=>array('package.php', 'package2.xml', 'package.xml', '*.tgz'),
            'simpleoutput'=>true,
        ));
    }

    public function definePackage($pfm)
    {
        $pfm->setPackage('Lib_Varien');
        $pfm->setSummary('Varien PHP Library');
        $pfm->setDescription('Varien library');
        $pfm->addMaintainer('lead', 'moshe', 'Moshe Gurvich', 'moshe@varien.com', 'yes');
    }

    public function defineRelease($pfm)
    {
        $pfm->setAPIVersion('0.1.0');
        $pfm->setReleaseVersion('0.1.0');
        $pfm->setAPIStability('beta');
        $pfm->setReleaseStability('beta');
        $pfm->setNotes('initial release');
        #$pfm->addPackageDepWithChannel('required', 'PEAR', 'pear.php.net', '1.4.3');
    }
}
Lib_Varien::run();
*/
