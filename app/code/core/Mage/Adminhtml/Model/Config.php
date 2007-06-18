<?php

/**
 * Configuration for Admin model
 * 
 * @package     Mage
 * @subpackage  Admin
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Mage_Adminhtml_Model_Config extends Varien_Simplexml_Config
{
    public function __construct()
    {
        parent::__construct();
        $this->setXml($this->loadFile(Mage::getModuleDir('etc', 'Mage_Adminhtml').DS.'admin.xml'));
    }
}