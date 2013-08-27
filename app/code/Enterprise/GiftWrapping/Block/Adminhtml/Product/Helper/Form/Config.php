<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml additional helper block for product configuration
 *
 * @category   Enterprise
 * @package    Enterprise_GiftWrapping
 */
class Enterprise_GiftWrapping_Block_Adminhtml_Product_Helper_Form_Config
    extends Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Config
{
    /**
     * Gift wrapping data
     *
     * @var Enterprise_GiftWrapping_Helper_Data
     */
    protected $_giftWrappingData = null;

    /**
     * @param Enterprise_GiftWrapping_Helper_Data $giftWrappingData
     * @param  $attributes
     */
    public function __construct(
        Enterprise_GiftWrapping_Helper_Data $giftWrappingData,
        $attributes = array()
    ) {
        $this->_giftWrappingData = $giftWrappingData;
        parent::__construct($attributes);
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
