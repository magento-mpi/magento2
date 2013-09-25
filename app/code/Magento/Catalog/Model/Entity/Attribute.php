<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product attribute extension with event dispatching
 *
 * @method Magento_Catalog_Model_Resource_Attribute _getResource()
 * @method Magento_Catalog_Model_Resource_Attribute getResource()
 * @method string getFrontendInputRenderer()
 * @method Magento_Catalog_Model_Entity_Attribute setFrontendInputRenderer(string $value)
 * @method int setIsGlobal(int $value)
 * @method int getIsVisible()
 * @method int setIsVisible(int $value)
 * @method int getIsSearchable()
 * @method Magento_Catalog_Model_Entity_Attribute setIsSearchable(int $value)
 * @method int getSearchWeight()
 * @method Magento_Catalog_Model_Entity_Attribute setSearchWeight(int $value)
 * @method int getIsFilterable()
 * @method Magento_Catalog_Model_Entity_Attribute setIsFilterable(int $value)
 * @method int getIsComparable()
 * @method Magento_Catalog_Model_Entity_Attribute setIsComparable(int $value)
 * @method Magento_Catalog_Model_Entity_Attribute setIsVisibleOnFront(int $value)
 * @method int getIsHtmlAllowedOnFront()
 * @method Magento_Catalog_Model_Entity_Attribute setIsHtmlAllowedOnFront(int $value)
 * @method int getIsUsedForPriceRules()
 * @method Magento_Catalog_Model_Entity_Attribute setIsUsedForPriceRules(int $value)
 * @method int getIsFilterableInSearch()
 * @method Magento_Catalog_Model_Entity_Attribute setIsFilterableInSearch(int $value)
 * @method int getUsedInProductListing()
 * @method Magento_Catalog_Model_Entity_Attribute setUsedInProductListing(int $value)
 * @method int getUsedForSortBy()
 * @method Magento_Catalog_Model_Entity_Attribute setUsedForSortBy(int $value)
 * @method int getIsConfigurable()
 * @method Magento_Catalog_Model_Entity_Attribute setIsConfigurable(int $value)
 * @method string getApplyTo()
 * @method Magento_Catalog_Model_Entity_Attribute setApplyTo(string $value)
 * @method int getIsVisibleInAdvancedSearch()
 * @method Magento_Catalog_Model_Entity_Attribute setIsVisibleInAdvancedSearch(int $value)
 * @method int getPosition()
 * @method Magento_Catalog_Model_Entity_Attribute setPosition(int $value)
 * @method int getIsWysiwygEnabled()
 * @method Magento_Catalog_Model_Entity_Attribute setIsWysiwygEnabled(int $value)
 * @method int getIsUsedForPromoRules()
 * @method Magento_Catalog_Model_Entity_Attribute setIsUsedForPromoRules(int $value)
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Entity_Attribute extends Magento_Eav_Model_Entity_Attribute
{
    protected $_eventPrefix = 'catalog_entity_attribute';
    protected $_eventObject = 'attribute';
    const MODULE_NAME = 'Magento_Catalog';

    /**
     * Class constructor
     *
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Eav_Model_Entity_TypeFactory $eavTypeFactory
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Eav_Model_Resource_Helper $resourceHelper
     * @param Magento_Validator_UniversalFactory $universalFactory
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Catalog_Model_ProductFactory $catalogProductFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Helper_Data $coreData,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Eav_Model_Entity_TypeFactory $eavTypeFactory,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Eav_Model_Resource_Helper $resourceHelper,
        Magento_Validator_UniversalFactory $universalFactory,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Catalog_Model_ProductFactory $catalogProductFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $registry,
            $coreData,
            $eavConfig,
            $eavTypeFactory,
            $storeManager,
            $resourceHelper,
            $universalFactory,
            $locale,
            $catalogProductFactory,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Processing object before save data
     *
     * @return Magento_Core_Model_Abstract
     * @throws Magento_Eav_Exception
     */
    protected function _beforeSave()
    {
        if ($this->_getResource()->isUsedBySuperProducts($this)) {
            throw new Magento_Eav_Exception(__('This attribute is used in configurable products'));
        }
        $this->setData('modulePrefix', self::MODULE_NAME);
        return parent::_beforeSave();
    }

    /**
     * Processing object after save data
     *
     * @return Magento_Core_Model_Abstract
     */
    protected function _afterSave()
    {
        /**
         * Fix saving attribute in admin
         */
        $this->_eavConfig->clear();
        return parent::_afterSave();
    }
}
