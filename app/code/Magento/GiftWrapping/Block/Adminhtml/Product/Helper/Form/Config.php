<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml additional helper block for product configuration
 *
 * @category   Magento
 * @package    Magento_GiftWrapping
 */
namespace Magento\GiftWrapping\Block\Adminhtml\Product\Helper\Form;

class Config
    extends \Magento\Adminhtml\Block\Catalog\Product\Helper\Form\Config
{
    /**
     * Gift wrapping data
     *
     * @var \Magento\GiftWrapping\Helper\Data
     */
    protected $_giftWrappingData = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\GiftWrapping\Helper\Data $giftWrappingData
     * @param array $attributes
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Data\Form\Element\Factory $factoryElement,
        \Magento\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\GiftWrapping\Helper\Data $giftWrappingData,
        $attributes = array()
    ) {
        $this->_giftWrappingData = $giftWrappingData;
        parent::__construct($coreData, $factoryElement, $factoryCollection, $attributes);
    }

    /**
     * Get config value data
     *
     * @return mixed
     */
    protected function _getValueFromConfig()
    {
        return $this->_giftWrappingData->isGiftWrappingAvailableForItems();
    }
}
