<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Helper;

/**
 * Giftcard module helper
 */
class Data extends \Magento\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\View\LayoutInterface $layout
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\View\LayoutInterface $layout
    ) {
        $this->_layout = $layout;
        parent::__construct($context);
    }

    /**
     * Instantiate giftardaccounts block when a gift card email should be sent
     *
     * @return \Magento\View\Element\Template
     */
    public function getEmailGeneratedItemsBlock()
    {
        /** @var $block \Magento\View\Element\Template */
        $block = $this->_layout->createBlock('Magento\View\Element\Template');
        $block->setTemplate('Magento_GiftCard::email/generated.phtml');
        return $block;
    }
}
