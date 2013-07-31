<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Manage currency import services block
 *
 * @category   Mage
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CurrencySymbol_Block_Adminhtml_System_Currency_Rate_Services extends Mage_Backend_Block_Template
{
    protected $_template = 'system/currency/rate/services.phtml';

    /**
     * Create import services form select element
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'import_services',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Html_Select')
                ->setOptions(Mage::getModel('Mage_Backend_Model_Config_Source_Currency_Service')->toOptionArray(0))
                ->setId('rate_services')
                ->setName('rate_services')
                ->setValue(Mage::getSingleton('Magento_Adminhtml_Model_Session')->getCurrencyRateService(true))
                ->setTitle(Mage::helper('Magento_Adminhtml_Helper_Data')->__('Import Service'))
        );

        return parent::_prepareLayout();
    }

}
