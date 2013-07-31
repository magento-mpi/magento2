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
 * Manage currency block
 *
 * @category   Mage
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CurrencySymbol_Block_Adminhtml_System_Currency extends Mage_Backend_Block_Template
{
    protected $_template = 'system/currency/rates.phtml';

    protected function _prepareLayout()
    {
        $this->addChild('save_button', 'Magento_Adminhtml_Block_Widget_Button', array(
                'label' => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Save Currency Rates'),
                'class' => 'save',
                'data_attribute' => array(
                    'mage-init' => array(
                        'button' => array('event' => 'save', 'target' => '#rate-form'),
        ))));

        $this->addChild('reset_button', 'Magento_Adminhtml_Block_Widget_Button', array(
                'label' => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Reset'),
                'onclick' => 'document.location.reload()',
                'class' => 'reset'
        ));

        $this->addChild('import_button', 'Magento_Adminhtml_Block_Widget_Button', array(
                'label' => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Import'),
                'class' => 'add',
                'type' => 'submit',
        ));

        $this->addChild('rates_matrix', 'Mage_CurrencySymbol_Block_Adminhtml_System_Currency_Rate_Matrix');

        $this->addChild('import_services', 'Mage_CurrencySymbol_Block_Adminhtml_System_Currency_Rate_Services');

        return parent::_prepareLayout();
    }

    public function getHeader()
    {
        return Mage::helper('Magento_Adminhtml_Helper_Data')->__('Manage Currency Rates');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    public function getImportButtonHtml()
    {
        return $this->getChildHtml('import_button');
    }

    public function getServicesHtml()
    {
        return $this->getChildHtml('import_services');
    }

    public function getRatesMatrixHtml()
    {
        return $this->getChildHtml('rates_matrix');
    }

    public function getImportFormAction()
    {
        return $this->getUrl('*/*/fetchRates');
    }

}
