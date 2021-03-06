<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftRegistry\Block;

/**
 * Front end helper block to add links
 */
class Link extends \Magento\Framework\View\Element\Html\Link\Current
{
    /**
     * @var \Magento\GiftRegistry\Helper\Data
     */
    protected $_giftHelper = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param \Magento\GiftRegistry\Helper\Data $giftHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magento\GiftRegistry\Helper\Data $giftHelper,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->_giftHelper = $giftHelper;
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if ($this->_giftHelper->isEnabled()) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
