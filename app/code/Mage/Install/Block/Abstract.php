<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
/**
 * Abstract installation block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Install_Block_Abstract extends Mage_Core_Block_Template
{
    /**
     * Retrieve installer model
     *
     * @return Mage_Install_Model_Installer
     */
    public function getInstaller()
    {
        return Mage::getSingleton('Mage_Install_Model_Installer');
    }
    
    /**
     * Retrieve wizard model
     *
     * @return Mage_Install_Model_Wizard
     */
    public function getWizard()
    {
        return Mage::getSingleton('Mage_Install_Model_Wizard');
    }
    
    /**
     * Retrieve current installation step
     *
     * @return Magento_Object
     */
    public function getCurrentStep()
    {
        return $this->getWizard()->getStepByRequest($this->getRequest());
    }
}
