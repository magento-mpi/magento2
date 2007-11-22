<?php

set_include_path(get_include_path().PATH_SEPARATOR.'./lib');

#require_once "Varien/Pear.php";
require_once "Varien/Pear/Package.php";

$pear = Varien_Pear::getInstance();
$result = $pear->run('channel-discover', array(), array('var-dev.varien.com'));
echo "<pre>"; print_r($result); echo "</pre>";

PEAR::setErrorHandling(PEAR_ERROR_DIE);

$options = array(
    'baseinstalldir'=>'Varien',
    'packagedirectory'=>$pear->getBaseDir().'/lib/Varien',
    'outputdirectory'=>$pear->getPearDir().'/output/',
    'simpleoutput'=>true,
    'filelistgenerator'=>'svn',
);

$pfm = PEAR_PackageFileManager2::importOptions('package2.xml', $options);

$pfm->setPackageType('php');
$pfm->setChannel('var-dev.varien.com');
$pfm->setLicense('Open Software License (OSL 3.0)', 'http://opensource.org/licenses/osl-3.0.php');

$pfm->setPackage('Varien');
$pfm->setSummary('Varien Library');
$pfm->setDescription('Varien Library');
$pfm->addMaintainer('lead', 'moshe', 'Moshe Gurvich', 'moshe@varien.com', 'yes');
$pfm->addMaintainer('lead', 'dmitriy', 'Dmitriy Soroka', 'dmitriy@varien.com', 'yes');

$pfm->addRelease();

$pfm->clearDeps();
$pfm->setPhpDep('5.2.0');
$pfm->setPearinstallerDep('1.4.3');

$pfm->setAPIVersion('0.7.0');
$pfm->setReleaseVersion('0.7.0');
$pfm->setAPIStability('beta');
$pfm->setReleaseStability('beta');
$pfm->setNotes('initial PEAR release');

$pfm->generateContents();
$pfm1 = $pfm->exportCompatiblePackageFile1($options);

if (true) {
    $pfm1->writePackageFile();
    $pfm->writePackageFile();

/*
    $outputDir = $this->get('options/outputdirectory');
    MagePearWrapper::getInstance()->run('package', array(),
        array($outputDir.'package.xml', $outputDir.'package2.xml')
    );
*/
} else {
    $pfm1->debugPackageFile();
    $pfm->debugPackageFile();
}

/*
require_once "Varien/Pear/Package/Varien.php";
$pkg = new Varien_Pear_Package_Varien();
$pkg->generatePackage();
*/
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
