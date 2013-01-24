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
 * Admin Checkout block for showing messages
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Block_Adminhtml_Manage_Messages extends Mage_Core_Block_Messages
{
    /**
     * Prepares layout for current block
     */
    public function _prepareLayout()
    {
        $this->addMessages(Mage::getSingleton('Mage_Adminhtml_Model_Session')->getMessages(true));
        parent::_prepareLayout();
    }
}
