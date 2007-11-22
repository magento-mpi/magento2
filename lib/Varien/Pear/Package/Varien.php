<?php

class Varien_Pear_Package_Varien extends Varien_Pear_Package_Mage
{
    public function defineData()
    {
        parent::defineData();


        $this->setImportOptions(array(
            'baseinstalldir'=>'Moshe',
            'packagedirectory'=>'/home/moshe/dev/magento/lib/Varien',
            'outputdirectory'=>dirname(__FILE__).'/',
            'filelistgenerator'=>'svn',
            'ignore'=>array('package.php', 'package2.xml', 'package.xml', '*.tgz'),
            'simpleoutput'=>true,
        ));

        $this
            ->set('options/baseinstalldir', 'Varien')
            ->set('options/packagedirectory', )
        ;

        return $this;
    }

    public function definePackage()
    {
        parent::definePackage();

        $this->getPfm()
            ->setPackage('Varien')
            ->setSummary('Varien Library')
            ->setDescription('Varien Library')
            ->addMaintainer('lead', 'moshe', 'Moshe Gurvich', 'moshe@varien.com', 'yes')
            ->addMaintainer('lead', 'dmitriy', 'Dmitriy Soroka', 'dmitriy@varien.com', 'yes')
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