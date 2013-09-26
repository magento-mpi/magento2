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
 * Customer giftregistry list block
 *
 * @category   Magento
 * @package    Magento_GiftRegistry
 */
class Magento_GiftRegistry_Block_Customer_Edit_Registrants extends  Magento_GiftRegistry_Block_Customer_Edit_Abstract
{
    /**
     * Scope Selector 'registry/registrant'
     *
     * @var string
     */
    protected $_prefix = 'registrant';

    /**
     * Gift registry data
     *
     * @var Magento_GiftRegistry_Helper_Data
     */
    protected $_giftRegistryData = null;

    /**
     * @param Magento_GiftRegistry_Helper_Data $giftRegistryData
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Directory_Model_Resource_Region_CollectionFactory $regionCollFactory
     * @param Magento_Directory_Model_Resource_Country_CollectionFactory $countryCollFactory
     * @param array $data
     */
    public function __construct(
        Magento_GiftRegistry_Helper_Data $giftRegistryData,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_Cache_Type_Config $configCacheType,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Directory_Model_Resource_Region_CollectionFactory $regionCollFactory,
        Magento_Directory_Model_Resource_Country_CollectionFactory $countryCollFactory,
        array $data = array()
    ) {
        $this->_giftRegistryData = $giftRegistryData;
        parent::__construct(
            $coreRegistry,
            $configCacheType,
            $coreData,
            $context,
            $storeManager,
            $regionCollFactory,
            $countryCollFactory,
            $data
        );
    }

    /**
     * Retrieve Max Recipients
     *
     * @return int
     */
    public function getMaxRegistrant()
    {
        return $this->_giftRegistryData->getMaxRegistrant();
    }

    /**
     * Return array of attributes groupped by group
     *
     * @return array
     */
    public function getGroupedRegistrantAttributes()
    {
        return $this->getGroupedAttributes();
    }

    /**
     * Return registrant collection
     *
     * @return Magento_GiftRegistry_Model_Resource_Person_Collection
     */
    public function getRegistrantList() {
        return $this->getEntity->getRegistrantCollection();
    }

    /**
     * Reorder attributes array  by group
     *
     * @param array $attributes
     * @return array
     */
    protected function _groupAttributes($attributes)
    {
        $grouped = array();
        if (is_array($attributes)) {
            foreach ($attributes as $field => $fdata){
                if (is_array($fdata)) {
                    $grouped[$field] = $fdata;
                    $grouped[$field]['id'] = $this->_getElementId($field);
                    $grouped[$field]['name'] = $this->_getElementName($field);
                }
            }
        }
        return $grouped;
    }

    /**
     * Prepare html element name
     *
     * @param string $code
     * @return string
     */
    protected function _getElementName($code)
    {
        $custom = ($this->isAttributeStatic($code)) ? '' : '[custom]';
        return $this->_prefix . '[${_index_}]'. $custom . '[' . $code . ']';
    }

    /**
     * Prepare html element id
     *
     * @param string $code
     * @return string
     */
    protected function _getElementId($code)
    {
        $custom = ($this->isAttributeStatic($code)) ? '' : 'custom:';
        return $this->_prefix . ':'. $custom . $code . '${_index_}';
    }

    /**
     * Get current registrant info , formatted in php array of JSON data
     *
     * @param int - id of the giftregistry entity
     * @return array
     */
    public function getRegistrantPresets($entityId)
    {
        $data = array();
        $registrantCollection = $this->getEntity()->getRegistrantsCollection();
        foreach ($registrantCollection->getItems() as $registrant) {
            $data[] = $registrant->unserialiseCustom()->getData();
        }
        return $data;
    }
}
