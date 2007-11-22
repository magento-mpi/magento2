<?php

class Varien_Pear_Package_Mage extends Varien_Pear_Package
{
    public function defineData()
    {
        parent::defineData();

        $this
            ->set('options/filelistgenerator', 'svn')
        ;

        return $this;
    }

    public function definePackage()
    {
        parent::definePackage();

        $this->getPfm()
            ->setPackageType('php')
            ->setChannel('var-dev.varien.com')
            ->setLicense('Open Software License (OSL 3.0)', 'http://opensource.org/licenses/osl-3.0.php')
        ;

        return $this;
    }

    public function defineRelease()
    {
        parent::defineRelease();

        $this->getPfm()
            ->clearDeps()
            ->setPhpDep('5.2.0')
            ->setPearinstallerDep('1.4.3')
        ;

        return $this;
    }
}