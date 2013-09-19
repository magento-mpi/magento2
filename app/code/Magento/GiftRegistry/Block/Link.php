<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Front end helper block to add links
 */
namespace Magento\GiftRegistry\Block;

class Link extends \Magento\Page\Block\Link\Current
{
    /**
     * @var \Magento\GiftRegistry\Helper\Data
     *
     * @var \Magento\GiftRegistry\Helper\Data
     */
    protected $_giftHelper = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\GiftRegistry\Helper\Data $giftHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\GiftRegistry\Helper\Data $giftHelper,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_giftHelper = $giftHelper;
    }

    /**
     * @inheritdoc
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
