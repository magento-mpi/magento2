<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Block;

/**
 * Front end helper block to add links
 */
class Link extends \Magento\View\Element\Html\Link\Current
{
    /**
     * @var \Magento\GiftRegistry\Helper\Data
     */
    protected $_giftHelper = null;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\App\DefaultPathInterface $defaultPath
     * @param \Magento\GiftRegistry\Helper\Data $giftHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\App\DefaultPathInterface $defaultPath,
        \Magento\GiftRegistry\Helper\Data $giftHelper,
        array $data = array()
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
