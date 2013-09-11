<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
/**
 * Abstract installation block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Install\Block;

abstract class AbstractBlock extends \Magento\Core\Block\Template
{
    /**
     * Retrieve installer model
     *
     * @return \Magento\Install\Model\Installer
     */
    public function getInstaller()
    {
        return \Mage::getSingleton('Magento\Install\Model\Installer');
    }
    
    /**
     * Retrieve wizard model
     *
     * @return \Magento\Install\Model\Wizard
     */
    public function getWizard()
    {
        return \Mage::getSingleton('Magento\Install\Model\Wizard');
    }
    
    /**
     * Retrieve current installation step
     *
     * @return \Magento\Object
     */
    public function getCurrentStep()
    {
        return $this->getWizard()->getStepByRequest($this->getRequest());
    }
}
