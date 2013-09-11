<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin Checkout block for showing messages
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage;

class Messages extends \Magento\Adminhtml\Block\Messages
{
    /**
     * Prepares layout for current block
     */
    public function _prepareLayout()
    {
        $this->addMessages(\Mage::getSingleton('Magento\Adminhtml\Model\Session')->getMessages(true));
        parent::_prepareLayout();
    }
}
