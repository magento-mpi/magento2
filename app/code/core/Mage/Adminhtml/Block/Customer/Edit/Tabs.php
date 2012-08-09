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
 * admin customer left menu
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('Mage_Customer_Helper_Data')->__('Customer Information'));
    }

    protected function _beforeToHtml()
    {
/*
        if (Mage::registry('current_customer')->getId()) {
            $this->addTab('view', array(
                'label'     => Mage::helper('Mage_Customer_Helper_Data')->__('Customer View'),
                'content'   => $this->getLayout()->createBlock('Mage_Adminhtml_Block_Customer_Edit_Tab_View')->toHtml(),
                'active'    => true
            ));
        }
*/
        $this->addTab('account', array(
            'label'     => Mage::helper('Mage_Customer_Helper_Data')->__('Account Information'),
            'content'   => $this->getLayout()
                ->createBlock('Mage_Adminhtml_Block_Customer_Edit_Tab_Account')->initForm()->toHtml(),
            'active'    => Mage::registry('current_customer')->getId() ? false : true
        ));

        $this->addTab('addresses', array(
            'label'     => Mage::helper('Mage_Customer_Helper_Data')->__('Addresses'),
            'content'   => $this->getLayout()
                ->createBlock('Mage_Adminhtml_Block_Customer_Edit_Tab_Addresses')->initForm()->toHtml(),
        ));


        // load: Orders, Shopping Cart, Wishlist, Product Reviews - with ajax

        if (Mage::registry('current_customer')->getId()) {

            if (Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('Mage_Sales::actions_view')) {
                $this->addTab('orders', array(
                    'label'     => Mage::helper('Mage_Customer_Helper_Data')->__('Orders'),
                    'class'     => 'ajax',
                    'url'       => $this->getUrl('*/*/orders', array('_current' => true)),
                 ));
            }

            $this->addTab('cart', array(
                'label'     => Mage::helper('Mage_Customer_Helper_Data')->__('Shopping Cart'),
                'class'     => 'ajax',
                'url'       => $this->getUrl('*/*/carts', array('_current' => true)),
            ));

            $this->addTab('wishlist', array(
                'label'     => Mage::helper('Mage_Customer_Helper_Data')->__('Wishlist'),
                'class'     => 'ajax',
                'url'       => $this->getUrl('*/*/wishlist', array('_current' => true)),
            ));

            if (Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('Mage_Newsletter::subscriber')) {
                $this->addTab('newsletter', array(
                    'label'     => Mage::helper('Mage_Customer_Helper_Data')->__('Newsletter'),
                    'content'   => $this->getLayout()
                        ->createBlock('Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter')->initForm()->toHtml()
                ));
            }

            if (Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('Mage_Review::reviews_ratings')) {
                $this->addTab('reviews', array(
                    'label'     => Mage::helper('Mage_Customer_Helper_Data')->__('Product Reviews'),
                    'class'     => 'ajax',
                    'url'       => $this->getUrl('*/*/productReviews', array('_current' => true)),
                ));
            }
        }

        $this->_updateActiveTab();
        Magento_Profiler::stop('customer/tabs');
        return parent::_beforeToHtml();
    }

    protected function _updateActiveTab()
    {
        $tabId = $this->getRequest()->getParam('tab');
        if( $tabId ) {
            $tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
            if($tabId) {
                $this->setActiveTab($tabId);
            }
        }
    }
}
