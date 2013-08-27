<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cart link block
 */
class Enterprise_GiftRegistry_Block_Cart_Link extends Magento_Core_Block_Template
{

    /**
     * Gift registry data
     *
     * @var Enterprise_GiftRegistry_Helper_Data
     */
    protected $_giftRegistryData = null;

    /**
     * @param Enterprise_GiftRegistry_Helper_Data $giftRegistryData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_GiftRegistry_Helper_Data $giftRegistryData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_giftRegistryData = $giftRegistryData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Return add url
     *
     * @return bool
     */
    public function getAddUrl()
    {
        return $this->getUrl('giftregistry/index/cart');
    }

    /**
     * Check whether module is available
     *
     * @return bool
     */
    public function getEnabled()
    {
        return  $this->_giftRegistryData->isEnabled();
    }

    /**
     * Return list of current customer gift registries
     *
     * @return Enterprise_GiftRegistry_Model_Resource_GiftRegistry_Collection
     */
    public function getEntityValues()
    {
        return $this->_giftRegistryData->getCurrentCustomerEntityOptions();
    }
}
