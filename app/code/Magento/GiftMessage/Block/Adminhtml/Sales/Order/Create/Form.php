<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order create gift message form
 *
 * @category   Magento
 * @package    Magento_GiftMessage
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftMessage\Block\Adminhtml\Sales\Order\Create;

class Form extends \Magento\Adminhtml\Block\Template
{
    /**
     * Indicates that block can display gift message form
     *
     * @return bool
     */
    public function canDisplayGiftmessageForm()
    {
        $quote = \Mage::getSingleton('Magento\Adminhtml\Model\Session\Quote')->getQuote();
        return $this->helper('\Magento\GiftMessage\Helper\Message')->getIsMessagesAvailable('items', $quote, $quote->getStore());
    }
}
