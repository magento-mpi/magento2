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

    protected function _construct()
    {
        $this->setTemplate('system/currency/rates.phtml');
    }

    protected function _prepareLayout()
    {
        $this->addChild('save_button', 'Mage_Adminhtml_Block_Widget_Button', array(
            'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Save Currency Rates'),
            'class'     => 'save',
            'data_attr'  => array(
                'widget-button' => array('event' => 'save', 'related' => '#rate-form')
            )
        ));

        $this->addChild('reset_button', 'Mage_Adminhtml_Block_Widget_Button', array(
            'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Reset'),
            'onclick'   => 'document.location.reload()',
            'class'     => 'reset'
        ));

        $this->addChild('import_button', 'Mage_Adminhtml_Block_Widget_Button', array(
            'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Import'),
            'class'     => 'add',
            'type'      => 'submit',
        ));

        $this->addChild('rates_matrix', 'Mage_Adminhtml_Block_System_Currency_Rate_Matrix');

        $this->addChild('import_services', 'Mage_Adminhtml_Block_System_Currency_Rate_Services');

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
