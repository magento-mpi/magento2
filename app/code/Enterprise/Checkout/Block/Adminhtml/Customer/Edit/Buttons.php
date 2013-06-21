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
     * Add "Manage Shopping Cart" button on customer management page
     *
     * @return Enterprise_Checkout_Block_Adminhtml_Customer_Edit_Buttons
     */
    public function addButtons()
    {
        if (!$this->_authorization->isAllowed('Enterprise_Checkout::view')
            && !$this->_authorization->isAllowed('Enterprise_Checkout::update')
            || Mage::app()->getStore()->getWebsiteId() == Mage::registry('current_customer')->getWebsiteId()
        ) {
            return $this;
        }
        $container = $this->getParentBlock();
        if ($container instanceof Mage_Backend_Block_Template && $container->getCustomerId()) {
            $url = Mage::getSingleton('Mage_Backend_Model_Url')->getUrl('*/checkout/index', array(
                'customer' => $container->getCustomerId()
            ));
            $container->addButton('manage_quote', array(
                'label' => Mage::helper('Enterprise_Checkout_Helper_Data')->__('Manage Shopping Cart'),
                'onclick' => "setLocation('" . $url . "')",
            ), 0);
        }
        return $this;
    }
}
