<?php

include "Varien/Pear/Package/Mage.php";

class Varien_Pear_Package_Varien_Pear extends Varien_Pear_Package_Mage
{
    public function definePackage()
    {
        parent::definePackage();

        $pfm = $this->getPfm();
        $pfm->setPackage('Varien_Pear');
        $pfm->setSummary('Varien PEAR Wrapper Library');
        $pfm->setDescription('Varien PEAR Wrapper Library');
        $pfm->addMaintainer('lead', 'moshe', 'Moshe Gurvich', 'moshe@varien.com', 'yes');

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