<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Manage currency block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Currency extends Mage_Adminhtml_Block_Template
{

    protected $_template = 'system/currency/rates.phtml';

    protected function _prepareLayout()
    {
        $this->setChild('save_button',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                ->setData(array(
                    'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Save Currency Rates'),
                    'onclick'   => 'currencyForm.submit();',
                    'class'     => 'save'
        )));

        $this->setChild('reset_button',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                ->setData(array(
                    'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Reset'),
                    'onclick'   => 'document.location.reload()',
                    'class'     => 'reset'
        )));

        $this->setChild('import_button',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                ->setData(array(
                    'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Import'),
                    'class'     => 'add',
                    'type'      => 'submit',
        )));

        $this->setChild('rates_matrix',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_System_Currency_Rate_Matrix')
        );

        $this->setChild('import_services',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_System_Currency_Rate_Services')
        );

        return parent::_prepareLayout();
    }

    protected function getHeader()
    {
        return Mage::helper('Mage_Adminhtml_Helper_Data')->__('Manage Currency Rates');
    }

    protected function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    protected function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    protected function getImportButtonHtml()
    {
        return $this->getChildHtml('import_button');
    }

    protected function getServicesHtml()
    {
        return $this->getChildHtml('import_services');
    }

    protected function getRatesMatrixHtml()
    {
        return $this->getChildHtml('rates_matrix');
    }

    protected function getImportFormAction()
    {
        return $this->getUrl('*/*/fetchRates');
    }

}
