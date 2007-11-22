<?php

require_once"Varien/Pear/Package.php";

class Varien_Pear_Package_Mage extends Varien_Pear_Package
{
    public function defineData()
    {
        parent::defineData();

        $this->set('options/filelistgenerator', 'svn');

        return $this;
    }

    public function definePackage()
    {
        parent::definePackage();

        $pfm = $this->getPfm();
        $pfm->setPackageType('php');
        $pfm->setChannel('var-dev.varien.com');
        $pfm->setLicense('Open Software License (OSL 3.0)', 'http://opensource.org/licenses/osl-3.0.php');

        return $this;
    }

    public function defineRelease()
    {
        parent::defineRelease();

        $pfm = $this->getPfm();
        $pfm->clearDeps();
        $pfm->setPhpDep('5.2.0');
        $pfm->setPearinstallerDep('1.4.3');

        return $this;
    }
}