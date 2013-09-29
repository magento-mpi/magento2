<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Giftcard module helper
 */
namespace Magento\GiftCard\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * @var \Magento\Core\Model\Layout
     */
    protected $_layout;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Layout $layout
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Layout $layout
    ) {
        $this->_layout = $layout;
        parent::__construct($context);
    }

    /**
     * Instantiate giftardaccounts block when a gift card email should be sent
     * @return \Magento\Core\Block\Template
     */
    public function getEmailGeneratedItemsBlock()
    {
        /** @var $block \Magento\Core\Block\Template */
        $block = $this->_layout->createBlock('Magento\Core\Block\Template');
        $block->setTemplate('Magento_GiftCard::email/generated.phtml');
        return $block;
    }
}
