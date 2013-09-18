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
     * @var Magento_GiftRegistry_Helper_Data
     *
     * @var \Magento\GiftRegistry\Helper\Data
     */
    protected $_giftHelper = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_GiftRegistry_Helper_Data $giftHelper
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_GiftRegistry_Helper_Data $giftHelper,
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
