<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Block\Adminhtml\Product\Helper\Form;

/**
 * Adminhtml additional helper block for product configuration
 *
 * @category   Magento
 * @package    Magento_GiftWrapping
 */
class Config extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Config
{
    /**
     * Gift wrapping data
     *
     * @var \Magento\GiftWrapping\Helper\Data|null
     */
    protected $_giftWrappingData = null;

    /**
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Escaper $escaper
     * @param \Magento\GiftWrapping\Helper\Data $giftWrappingData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Escaper $escaper,
        \Magento\GiftWrapping\Helper\Data $giftWrappingData,
        $data = array()
    ) {
        $this->_giftWrappingData = $giftWrappingData;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * Get config value data
     *
     * @return string|null
     */
    protected function _getValueFromConfig()
    {
        return $this->_giftWrappingData->isGiftWrappingAvailableForItems();
    }
}
