<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Additional buttons on customer edit form
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Block_Adminhtml_Customer_Edit_Buttons extends Mage_Adminhtml_Block_Customer_Edit
{
    /**
     * Add button to Shopping Cart Management etc.
     *
     * @return return
     */
    public function addButtons()
    {
        if (!Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('sales/enterprise_checkout/view')
            && !Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('sales/enterprise_checkout/update')
            || Mage::app()->getStore()->getWebsiteId() == Mage::registry('current_customer')->getWebsiteId())
        {
            return $this;
        }
        $container = $this->getParentBlock();
        if ($container instanceof Mage_Adminhtml_Block_Template && $container->getCustomerId()) {
            $url = Mage::getSingleton('Mage_Adminhtml_Model_Url')
               ->getUrl('*/checkout/index', array('customer' => $container->getCustomerId()));
            $container->addButton('manage_quote', array(
                'label' => Mage::helper('Enterprise_Checkout_Helper_Data')->__('Manage Shopping Cart'),
                'onclick' => "setLocation('" . $url . "')",
            ), 0);
        }
        return $this;
    }
}
