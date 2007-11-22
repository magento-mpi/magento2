<?php

class Varien_Pear_Package_Varien_Pear extends Varien_Pear_Package_Mage
{
    public function definePackage()
    {
        parent::definePackage();

        $this->getPfm()
            ->setPackage('Varien_Pear')
            ->setSummary('Varien PEAR Wrapper Library')
            ->setDescription('Varien PEAR Wrapper Library')
            ->addMaintainer('lead', 'moshe', 'Moshe Gurvich', 'moshe@varien.com', 'yes')
        ;

        return $this;
    }

    public function defineRelease()
    {
        parent::defineRelease();

        $this->getPfm()
            ->setAPIVersion('0.7.0')
            ->setReleaseVersion('0.7.0')
            ->setAPIStability('beta')
            ->setReleaseStability('beta')
            ->setNotes('initial PEAR release')
        ;

        return $this;
    }
}