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
class Magento_GiftWrapping_Block_Adminhtml_Product_Helper_Form_Config
    extends Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Config
{
    /**
     * Gift wrapping data
     *
     * @var Magento_GiftWrapping_Helper_Data
     */
    protected $_giftWrappingData = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Data_Form_Element_Factory $factoryElement
     * @param Magento_Data_Form_Element_CollectionFactory $factoryCollection
     * @param Magento_GiftWrapping_Helper_Data $giftWrappingData
     * @param array $attributes
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Data_Form_Element_Factory $factoryElement,
        Magento_Data_Form_Element_CollectionFactory $factoryCollection,
        Magento_GiftWrapping_Helper_Data $giftWrappingData,
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
