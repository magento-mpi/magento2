<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_Catalog_Model_Config extends Magento_Eav_Model_Config
{
    const XML_PATH_LIST_DEFAULT_SORT_BY     = 'catalog/frontend/default_sort_by';

    protected $_attributeSetsById;
    protected $_attributeSetsByName;

    protected $_attributeGroupsById;
    protected $_attributeGroupsByName;

    protected $_productTypesById;

    /**
     * Array of attributes codes needed for product load
     *
     * @var array
     */
    protected $_productAttributes;

    /**
     * Product Attributes used in product listing
     *
     * @var array
     */
    protected $_usedInProductListing;

    /**
     * Product Attributes For Sort By
     *
     * @var array
     */
    protected $_usedForSortBy;

    protected $_storeId = null;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @param Magento_Core_Model_App $app
     * @param Magento_Eav_Model_Entity_TypeFactory $entityTypeFactory
     * @param Magento_Core_Model_Cache_StateInterface $cacheState
     * @param Magento_Validator_UniversalFactory $universalFactory
     * Eav config
     *
     * @var Magento_Eav_Model_Config
     */
    protected $_eavConfig;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Set collection factory
     *
     * @var Magento_Eav_Model_Resource_Entity_Attribute_Set_CollectionFactory
     */
    protected $_setCollectionFactory;

    /**
     * Group collection factory
     *
     * @var Magento_Eav_Model_Resource_Entity_Attribute_Group_CollectionFactory
     */
    protected $_groupCollectionFactory;

    /**
     * Product type factory
     *
     * @var Magento_Catalog_Model_Product_TypeFactory
     */
    protected $_productTypeFactory;

    /**
     * Config factory
     *
     * @var Magento_Catalog_Model_Resource_ConfigFactory
     */
    protected $_configFactory;

    /**
     * Constructor
     *
     * @param Magento_Core_Model_App $app
     * @param Magento_Eav_Model_Entity_TypeFactory $entityTypeFactory
     * @param Magento_Core_Model_Cache_StateInterface $cacheState
     * @param Magento_Validator_UniversalFactory $universalFactory
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Catalog_Model_Resource_ConfigFactory $configFactory
     * @param Magento_Catalog_Model_Product_TypeFactory $productTypeFactory
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Group_CollectionFactory $groupCollectionFactory
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Set_CollectionFactory $setCollectionFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Eav_Model_Config $eavConfig
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Model_App $app,
        Magento_Eav_Model_Entity_TypeFactory $entityTypeFactory,
        Magento_Core_Model_Cache_StateInterface $cacheState,
        Magento_Validator_UniversalFactory $universalFactory,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Catalog_Model_Resource_ConfigFactory $configFactory,
        Magento_Catalog_Model_Product_TypeFactory $productTypeFactory,
        Magento_Eav_Model_Resource_Entity_Attribute_Group_CollectionFactory $groupCollectionFactory,
        Magento_Eav_Model_Resource_Entity_Attribute_Set_CollectionFactory $setCollectionFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Eav_Model_Config $eavConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_configFactory = $configFactory;
        $this->_productTypeFactory = $productTypeFactory;
        $this->_groupCollectionFactory = $groupCollectionFactory;
        $this->_setCollectionFactory = $setCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_eavConfig = $eavConfig;

        parent::__construct($app, $entityTypeFactory, $cacheState, $universalFactory);
    }

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Catalog_Model_Resource_Config');
    }

    /**
     * Set store id
     *
     * @param integer $storeId
     * @return Magento_Catalog_Model_Config
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Return store id, if is not set return current app store
     *
     * @return integer
     */
    public function getStoreId()
    {
        if ($this->_storeId === null) {
            return $this->_storeManager->getStore()->getId();
        }
        return $this->_storeId;
    }

    public function loadAttributeSets()
    {
        if ($this->_attributeSetsById) {
            return $this;
        }

        $attributeSetCollection = $this->_setCollectionFactory->create()
            ->load();

        $this->_attributeSetsById = array();
        $this->_attributeSetsByName = array();
        foreach ($attributeSetCollection as $id=>$attributeSet) {
            $entityTypeId = $attributeSet->getEntityTypeId();
            $name = $attributeSet->getAttributeSetName();
            $this->_attributeSetsById[$entityTypeId][$id] = $name;
            $this->_attributeSetsByName[$entityTypeId][strtolower($name)] = $id;
        }
        return $this;
    }

    public function getAttributeSetName($entityTypeId, $id)
    {
        if (!is_numeric($id)) {
            return $id;
        }
        $this->loadAttributeSets();

        if (!is_numeric($entityTypeId)) {
            $entityTypeId = $this->getEntityType($entityTypeId)->getId();
        }
        return isset($this->_attributeSetsById[$entityTypeId][$id]) ? $this->_attributeSetsById[$entityTypeId][$id] : false;
    }

    public function getAttributeSetId($entityTypeId, $name = null)
    {
        if (is_numeric($name)) {
            return $name;
        }
        $this->loadAttributeSets();

        if (!is_numeric($entityTypeId)) {
            $entityTypeId = $this->getEntityType($entityTypeId)->getId();
        }
        $name = strtolower($name);
        return isset($this->_attributeSetsByName[$entityTypeId][$name]) ? $this->_attributeSetsByName[$entityTypeId][$name] : false;
    }

    public function loadAttributeGroups()
    {
        if ($this->_attributeGroupsById) {
            return $this;
        }

        $attributeSetCollection = $this->_groupCollectionFactory->create()
            ->load();

        $this->_attributeGroupsById = array();
        $this->_attributeGroupsByName = array();
        foreach ($attributeSetCollection as $id=>$attributeGroup) {
            $attributeSetId = $attributeGroup->getAttributeSetId();
            $name = $attributeGroup->getAttributeGroupName();
            $this->_attributeGroupsById[$attributeSetId][$id] = $name;
            $this->_attributeGroupsByName[$attributeSetId][strtolower($name)] = $id;
        }
        return $this;
    }

    public function getAttributeGroupName($attributeSetId, $id)
    {
        if (!is_numeric($id)) {
            return $id;
        }

        $this->loadAttributeGroups();

        if (!is_numeric($attributeSetId)) {
            $attributeSetId = $this->getAttributeSetId($attributeSetId);
        }
        return isset($this->_attributeGroupsById[$attributeSetId][$id]) ? $this->_attributeGroupsById[$attributeSetId][$id] : false;
    }

    public function getAttributeGroupId($attributeSetId, $name)
    {
        if (is_numeric($name)) {
            return $name;
        }

        $this->loadAttributeGroups();

        if (!is_numeric($attributeSetId)) {
            $attributeSetId = $this->getAttributeSetId($attributeSetId);
        }
        $name = strtolower($name);
        return isset($this->_attributeGroupsByName[$attributeSetId][$name]) ? $this->_attributeGroupsByName[$attributeSetId][$name] : false;
    }

    public function loadProductTypes()
    {
        if ($this->_productTypesById) {
            return $this;
        }

        $productTypeCollection = $this->_productTypeFactory->create()
            ->getOptionArray();

        $this->_productTypesById = array();
        $this->_productTypesByName = array();
        foreach ($productTypeCollection as $id=>$type) {
            $name = $type;
            $this->_productTypesById[$id] = $name;
            $this->_productTypesByName[strtolower($name)] = $id;
        }
        return $this;
    }

    public function getProductTypeId($name)
    {
        if (is_numeric($name)) {
            return $name;
        }

        $this->loadProductTypes();

        $name = strtolower($name);
        return isset($this->_productTypesByName[$name]) ? $this->_productTypesByName[$name] : false;
    }

    public function getProductTypeName($id)
    {
        if (!is_numeric($id)) {
            return $id;
        }

        $this->loadProductTypes();

        return isset($this->_productTypesById[$id]) ? $this->_productTypesById[$id] : false;
    }

    public function getSourceOptionId($source, $value)
    {
        foreach ($source->getAllOptions() as $option) {
            if (strcasecmp($option['label'], $value)==0 || $option['value'] == $value) {
                return $option['value'];
            }
        }
        return null;
    }

    /**
     * Load Product attributes
     *
     * @return array
     */
    public function getProductAttributes()
    {
        if (is_null($this->_productAttributes)) {
            $this->_productAttributes = array_keys($this->getAttributesUsedInProductListing());
        }
        return $this->_productAttributes;
    }

    /**
     * Retrieve resource model
     *
     * @return Magento_Catalog_Model_Resource_Config
     */
    protected function _getResource()
    {
        return $this->_configFactory->create();
    }

    /**
     * Retrieve Attributes used in product listing
     *
     * @return array
     */
    public function getAttributesUsedInProductListing() {
        if (is_null($this->_usedInProductListing)) {
            $this->_usedInProductListing = array();
            $entityType = Magento_Catalog_Model_Product::ENTITY;
            $attributesData = $this->_getResource()
                ->setStoreId($this->getStoreId())
                ->getAttributesUsedInListing();
            $this->_eavConfig->importAttributesData($entityType, $attributesData);
            foreach ($attributesData as $attributeData) {
                $attributeCode = $attributeData['attribute_code'];
                $this->_usedInProductListing[$attributeCode] = $this->_eavConfig
                    ->getAttribute($entityType, $attributeCode);
            }
        }
        return $this->_usedInProductListing;
    }

    /**
     * Retrieve Attributes array used for sort by
     *
     * @return array
     */
    public function getAttributesUsedForSortBy() {
        if (is_null($this->_usedForSortBy)) {
            $this->_usedForSortBy = array();
            $entityType     = Magento_Catalog_Model_Product::ENTITY;
            $attributesData = $this->_getResource()
                ->getAttributesUsedForSortBy();
            $this->_eavConfig->importAttributesData($entityType, $attributesData);
            foreach ($attributesData as $attributeData) {
                $attributeCode = $attributeData['attribute_code'];
                $this->_usedForSortBy[$attributeCode] = $this->_eavConfig
                    ->getAttribute($entityType, $attributeCode);
            }
        }
        return $this->_usedForSortBy;
    }

    /**
     * Retrieve Attributes Used for Sort by as array
     * key = code, value = name
     *
     * @return array
     */
    public function getAttributeUsedForSortByArray()
    {
        $options = array(
            'position'  => __('Position')
        );
        foreach ($this->getAttributesUsedForSortBy() as $attribute) {
            /* @var $attribute Magento_Eav_Model_Entity_Attribute_Abstract */
            $options[$attribute->getAttributeCode()] = $attribute->getStoreLabel();
        }

        return $options;
    }

    /**
     * Retrieve Product List Default Sort By
     *
     * @param mixed $store
     * @return string
     */
    public function getProductListDefaultSortBy($store = null) {
        return $this->_coreStoreConfig->getConfig(self::XML_PATH_LIST_DEFAULT_SORT_BY, $store);
    }
}
