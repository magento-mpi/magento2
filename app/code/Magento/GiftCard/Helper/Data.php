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
     * @var \Magento\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\View\LayoutInterface $layout
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\View\LayoutInterface $layout
    ) {
        $this->_layout = $layout;
        parent::__construct($context);
    }

    /**
     * Instantiate giftardaccounts block when a gift card email should be sent
     * @return \Magento\View\Block\Template
     */
    public function getEmailGeneratedItemsBlock()
    {
        /** @var $block \Magento\View\Block\Template */
        $block = $this->_layout->createBlock('Magento\View\Block\Template);
        $block->setTemplate('Magento_GiftCard::email/generated.phtml');
        return $block;
    }
}
