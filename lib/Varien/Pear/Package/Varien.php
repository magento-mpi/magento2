<?php

require_once "Varien/Pear/Package/Mage.php";

class Varien_Pear_Package_Varien extends Varien_Pear_Package_Mage
{
    public function defineData()
    {
        parent::defineData();

        $this->set('options/baseinstalldir', 'Varien');
        $this->set('options/packagedirectory', $this->getPear()->getBaseDir().DS.'lib'.DS.'Varien');

        return $this;
    }

    public function definePackage()
    {
        parent::definePackage();

        $pfm = $this->getPfm();
        $pfm->setPackage('Varien');
        $pfm->setSummary('Varien Library');
        $pfm->setDescription('Varien Library');
        $pfm->addMaintainer('lead', 'moshe', 'Moshe Gurvich', 'moshe@varien.com', 'yes');
        $pfm->addMaintainer('lead', 'dmitriy', 'Dmitriy Soroka', 'dmitriy@varien.com', 'yes');

        return $this;
    }

    public function defineRelease()
    {
        parent::defineRelease();

        $pfm = $this->getPfm();
        $pfm->setAPIVersion('0.7.0');
        $pfm->setReleaseVersion('0.7.0');
        $pfm->setAPIStability('beta');
        $pfm->setReleaseStability('beta');
        $pfm->setNotes('initial PEAR release');

        return $this;
    }
}