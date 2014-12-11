<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftCard\Helper;

/**
 * Giftcard module helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\View\LayoutInterface $layout
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context, \Magento\Framework\View\LayoutInterface $layout)
    {
        $this->_layout = $layout;
        parent::__construct($context);
    }

    /**
     * Instantiate giftardaccounts block when a gift card email should be sent
     *
     * @return \Magento\Framework\View\Element\Template
     */
    public function getEmailGeneratedItemsBlock()
    {
        /** @var $block \Magento\Framework\View\Element\Template */
        $block = $this->_layout->createBlock('Magento\Framework\View\Element\Template');
        $block->setTemplate('Magento_GiftCard::email/generated.phtml');
        return $block;
    }
}
