<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Custmer addresses forms
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Addresses extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('customer/tab/addresses.phtml');
    }

    public function getRegionsUrl()
    {
        return Mage::getUrl('*/json/countryRegion');
    }

    protected function _prepareLayout()
    {
        $this->setChild('delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'  => __('Delete Address'),
                    'name'   => 'delete_address',
                    'class'  => 'delete'
                ))
        );
        $this->setChild('add_address_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'  => __('Add New Address'),
                    'id'     => 'add_address_button',
                    'name'   => 'add_address_button',
                    'class'  => 'add left',
                    'style'  => 'margin:3px 0px;',
                    'onclick'=> 'customerAddresses.addNewAddress()'
                ))
        );
        $this->setChild('cencel_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'  => __('Cencel'),
                    'id'     => 'cancel_add_address'.$this->getTemplatePrefix(),
                    'name'   => 'cencel_address',
                    'class'  => 'delete-address',
                    'onclick'=> 'customerAddresses.cancelAdd(this)',
                ))
        );
        return parent::_prepareLayout();
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    public function initForm()
    {

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('address_fieldset', array('legend'=>__("Edit Customer's Address")));

        $addressModel = Mage::getModel('customer/address');

        $this->_setFieldset($addressModel->getAttributes(), $fieldset);

        if ($regionElement = $form->getElement('region')) {
            $regionElement->setRenderer(Mage::getModel('adminhtml/customer_renderer_region'));
        }
        
        if ($country = $form->getElement('country_id')) {
            $country->addClass('countries');
        }
        
        $addressCollection = Mage::registry('current_customer')->getLoadedAddressCollection();
        $this->assign('customer', Mage::registry('current_customer'));
        $this->assign('addressCollection', $addressCollection);
        $this->setForm($form);

        return $this;
    }
    
    public function getCencelButtonHtml()
    {
        return $this->getChildHtml('cencel_button');
    }
    
    public function getAddNewButtonHtml()
    {
        return $this->getChildHtml('add_address_button');
    }
    
    public function getTemplatePrefix()
    {
        return '_template_';
    }
}
