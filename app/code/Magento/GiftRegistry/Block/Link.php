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
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\App\DefaultPathInterface $defaultPath
     * @param \Magento\GiftRegistry\Helper\Data $giftHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\App\DefaultPathInterface $defaultPath,
        \Magento\GiftRegistry\Helper\Data $giftHelper,
        array $data = array()
    ) {
        parent::__construct($context, $coreData, $defaultPath, $data);
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
