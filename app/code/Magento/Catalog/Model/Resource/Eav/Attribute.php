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
 * Catalog attribute model
 *
 * @method Magento_Catalog_Model_Resource_Attribute _getResource()
 * @method Magento_Catalog_Model_Resource_Attribute getResource()
 * @method Magento_Catalog_Model_Resource_Eav_Attribute getFrontendInputRenderer()
 * @method string setFrontendInputRenderer(string $value)
 * @method int setIsGlobal(int $value)
 * @method Magento_Catalog_Model_Resource_Eav_Attribute getIsVisible()
 * @method int setIsVisible(int $value)
 * @method Magento_Catalog_Model_Resource_Eav_Attribute getIsSearchable()
 * @method int setIsSearchable(int $value)
 * @method Magento_Catalog_Model_Resource_Eav_Attribute getSearchWeight()
 * @method int setSearchWeight(int $value)
 * @method Magento_Catalog_Model_Resource_Eav_Attribute getIsFilterable()
 * @method int setIsFilterable(int $value)
 * @method Magento_Catalog_Model_Resource_Eav_Attribute getIsComparable()
 * @method int setIsComparable(int $value)
 * @method int setIsVisibleOnFront(int $value)
 * @method Magento_Catalog_Model_Resource_Eav_Attribute getIsHtmlAllowedOnFront()
 * @method int setIsHtmlAllowedOnFront(int $value)
 * @method Magento_Catalog_Model_Resource_Eav_Attribute getIsUsedForPriceRules()
 * @method int setIsUsedForPriceRules(int $value)
 * @method Magento_Catalog_Model_Resource_Eav_Attribute getIsFilterableInSearch()
 * @method int setIsFilterableInSearch(int $value)
 * @method Magento_Catalog_Model_Resource_Eav_Attribute getUsedInProductListing()
 * @method int setUsedInProductListing(int $value)
 * @method Magento_Catalog_Model_Resource_Eav_Attribute getUsedForSortBy()
 * @method int setUsedForSortBy(int $value)
 * @method Magento_Catalog_Model_Resource_Eav_Attribute getIsConfigurable()
 * @method int setIsConfigurable(int $value)
 * @method string setApplyTo(string $value)
 * @method Magento_Catalog_Model_Resource_Eav_Attribute getIsVisibleInAdvancedSearch()
 * @method int setIsVisibleInAdvancedSearch(int $value)
 * @method Magento_Catalog_Model_Resource_Eav_Attribute getPosition()
 * @method int setPosition(int $value)
 * @method Magento_Catalog_Model_Resource_Eav_Attribute getIsWysiwygEnabled()
 * @method int setIsWysiwygEnabled(int $value)
 * @method Magento_Catalog_Model_Resource_Eav_Attribute getIsUsedForPromoRules()
 * @method int setIsUsedForPromoRules(int $value)
 * @method string getFrontendLabel()
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Resource_Eav_Attribute extends Magento_Eav_Model_Entity_Attribute
{
    const SCOPE_STORE                           = 0;
    const SCOPE_GLOBAL                          = 1;
    const SCOPE_WEBSITE                         = 2;

    const MODULE_NAME                           = 'Magento_Catalog';
    const ENTITY                                = 'catalog_eav_attribute';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix                     = 'catalog_entity_attribute';
    /**
     * Event object name
     *
     * @var string
     */
    protected $_eventObject                     = 'attribute';

    /**
     * Array with labels
     *
     * @var array
     */
    static protected $_labels                   = null;

    /**
     * Index indexer
     *
     * @var Magento_Index_Model_Indexer
     */
    protected $_indexIndexer;

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
     * @param Magento_Index_Model_Indexer $indexIndexer
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
        Magento_Index_Model_Indexer $indexIndexer,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_indexIndexer = $indexIndexer;
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

    protected function _construct()
    {
        $this->_init('Magento_Catalog_Model_Resource_Attribute');
    }

    /**
     * Processing object before save data
     *
     * @throws Magento_Core_Exception
     * @return Magento_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $this->setData('modulePrefix', self::MODULE_NAME);
        if (isset($this->_origData['is_global'])) {
            if (!isset($this->_data['is_global'])) {
                $this->_data['is_global'] = self::SCOPE_GLOBAL;
            }
            if (($this->_data['is_global'] != $this->_origData['is_global'])
                && $this->_getResource()->isUsedBySuperProducts($this)) {
                throw new Magento_Core_Exception(
                    __('Do not change the scope. This attribute is used in configurable products.')
                );
            }
        }
        if ($this->getFrontendInput() == 'price') {
            if (!$this->getBackendModel()) {
                $this->setBackendModel('Magento_Catalog_Model_Product_Attribute_Backend_Price');
            }
        }
        if ($this->getFrontendInput() == 'textarea') {
            if ($this->getIsWysiwygEnabled()) {
                $this->setIsHtmlAllowedOnFront(1);
            }
        }
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
        $this->_indexIndexer->processEntityAction(
            $this, self::ENTITY, Magento_Index_Model_Event::TYPE_SAVE
        );
        return parent::_afterSave();
    }

    /**
     * Register indexing event before delete catalog eav attribute
     *
     * @return Magento_Catalog_Model_Resource_Eav_Attribute
     * @throws Magento_Core_Exception
     */
    protected function _beforeDelete()
    {
        if ($this->_getResource()->isUsedBySuperProducts($this)) {
            throw new Magento_Core_Exception(__('This attribute is used in configurable products.'));
        }
        $this->_indexIndexer->logEvent(
            $this, self::ENTITY, Magento_Index_Model_Event::TYPE_DELETE
        );
        return parent::_beforeDelete();
    }

    /**
     * Init indexing process after catalog eav attribute delete commit
     *
     * @return Magento_Catalog_Model_Resource_Eav_Attribute
     */
    protected function _afterDeleteCommit()
    {
        parent::_afterDeleteCommit();
        $this->_indexIndexer->indexEvents(
            self::ENTITY, Magento_Index_Model_Event::TYPE_DELETE
        );
        return $this;
    }

    /**
     * Return is attribute global
     *
     * @return integer
     */
    public function getIsGlobal()
    {
        return $this->_getData('is_global');
    }

    /**
     * Retrieve attribute is global scope flag
     *
     * @return bool
     */
    public function isScopeGlobal()
    {
        return $this->getIsGlobal() == self::SCOPE_GLOBAL;
    }

    /**
     * Retrieve attribute is website scope website
     *
     * @return bool
     */
    public function isScopeWebsite()
    {
        return $this->getIsGlobal() == self::SCOPE_WEBSITE;
    }

    /**
     * Retrieve attribute is store scope flag
     *
     * @return bool
     */
    public function isScopeStore()
    {
        return !$this->isScopeGlobal() && !$this->isScopeWebsite();
    }

    /**
     * Retrieve store id
     *
     * @return int
     */
    public function getStoreId()
    {
        $dataObject = $this->getDataObject();
        if ($dataObject) {
            return $dataObject->getStoreId();
        }
        return $this->getData('store_id');
    }

    /**
     * Retrieve apply to products array
     * Return empty array if applied to all products
     *
     * @return array
     */
    public function getApplyTo()
    {
        if ($this->getData('apply_to')) {
            if (is_array($this->getData('apply_to'))) {
                return $this->getData('apply_to');
            }
            return explode(',', $this->getData('apply_to'));
        } else {
            return array();
        }
    }

    /**
     * Retrieve source model
     *
     * @return Magento_Eav_Model_Entity_Attribute_Source_Abstract
     */
    public function getSourceModel()
    {
        $model = $this->getData('source_model');
        if (empty($model)) {
            if ($this->getBackendType() == 'int' && $this->getFrontendInput() == 'select') {
                return $this->_getDefaultSourceModel();
            }
        }
        return $model;
    }

    /**
     * Whether allowed for rule condition
     *
     * @return bool
     */
    public function isAllowedForRuleCondition()
    {
        $allowedInputTypes = array(
            'boolean',
            'date',
            'datetime',
            'multiselect',
            'price',
            'select',
            'text',
            'textarea',
            'weight',
        );
        return $this->getIsVisible() && in_array($this->getFrontendInput(), $allowedInputTypes);
    }

    /**
     * Get default attribute source model
     *
     * @return string
     */
    public function _getDefaultSourceModel()
    {
        return 'Magento_Eav_Model_Entity_Attribute_Source_Table';
    }

    /**
     * Check is an attribute used in EAV index
     *
     * @return bool
     */
    public function isIndexable()
    {
        // exclude price attribute
        if ($this->getAttributeCode() == 'price') {
            return false;
        }

        if (!$this->getIsFilterableInSearch() && !$this->getIsVisibleInAdvancedSearch() && !$this->getIsFilterable()) {
            return false;
        }

        $backendType    = $this->getBackendType();
        $frontendInput  = $this->getFrontendInput();

        if ($backendType == 'int' && $frontendInput == 'select') {
            return true;
        } else if ($backendType == 'varchar' && $frontendInput == 'multiselect') {
            return true;
        } else if ($backendType == 'decimal') {
            return true;
        }

        return false;
    }

    /**
     * Retrieve index type for indexable attribute
     *
     * @return string|false
     */
    public function getIndexType()
    {
        if (!$this->isIndexable()) {
            return false;
        }
        if ($this->getBackendType() == 'decimal') {
            return 'decimal';
        }

        return 'source';
    }
}
