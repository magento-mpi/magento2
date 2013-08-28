<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCard_Block_Checkout_Cart_Item_Renderer extends Magento_Checkout_Block_Cart_Item_Renderer
{
    /**
     * Gift card catalog product configuration
     *
     * @var Enterprise_GiftCard_Helper_Catalog_Product_Configuration
     */
    protected $_giftCardCatalogProductConfiguration = null;

    /**
     * @param Enterprise_GiftCard_Helper_Catalog_Product_Configuration
     * $giftCardCatalogProductConfiguration
     * @param Magento_Catalog_Helper_Product_Configuration $catalogProductConfiguration
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_GiftCard_Helper_Catalog_Product_Configuration $giftCardCatalogProductConfiguration,
        Magento_Catalog_Helper_Product_Configuration $catalogProductConfiguration,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_giftCardCatalogProductConfiguration = $giftCardCatalogProductConfiguration;
        parent::__construct($catalogProductConfiguration, $coreData, $context, $data);
    }

    /**
     * Prepare custom option for display, returns false if there's no value
     *
     * @param string $code
     * @return mixed
     */
    protected function _prepareCustomOption($code)
    {
        return $this->_giftCardCatalogProductConfiguration
            ->prepareCustomOption($this->getItem(), $code);
    }

    /**
     * Get gift card option list
     *
     * @return array
     */
    protected function _getGiftcardOptions()
    {
        return $this->_giftCardCatalogProductConfiguration
            ->getGiftcardOptions($this->getItem());
    }

    /**
     * Return gift card and custom options array
     *
     * @return array
     */
    public function getOptionList()
    {
        return $this->_giftCardCatalogProductConfiguration
            ->getOptions($this->getItem());
    }
}
