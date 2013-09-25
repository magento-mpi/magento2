<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog advanced search model
 *
 * @method Magento_CatalogSearch_Model_Resource_Advanced getResource()
 * @method int getEntityTypeId()
 * @method Magento_CatalogSearch_Model_Advanced setEntityTypeId(int $value)
 * @method int getAttributeSetId()
 * @method Magento_CatalogSearch_Model_Advanced setAttributeSetId(int $value)
 * @method string getTypeId()
 * @method Magento_CatalogSearch_Model_Advanced setTypeId(string $value)
 * @method string getSku()
 * @method Magento_CatalogSearch_Model_Advanced setSku(string $value)
 * @method int getHasOptions()
 * @method Magento_CatalogSearch_Model_Advanced setHasOptions(int $value)
 * @method int getRequiredOptions()
 * @method Magento_CatalogSearch_Model_Advanced setRequiredOptions(int $value)
 * @method string getCreatedAt()
 * @method Magento_CatalogSearch_Model_Advanced setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Magento_CatalogSearch_Model_Advanced setUpdatedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogSearch_Model_Advanced extends Magento_Core_Model_Abstract
{
    /**
     * User friendly search criteria list
     *
     * @var array
     */
    protected $_searchCriterias = array();

    /**
     * Current search engine
     *
     * @var object|Magento_CatalogSearch_Model_Resource_Fulltext_Engine
     */
    protected $_engine;

    /**
     * Found products collection
     *
     * @var Magento_CatalogSearch_Model_Resource_Advanced_Collection
     */
    protected $_productCollection;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Product factory
     *
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_productFactory;

    /**
     * Currency factory
     *
     * @var Magento_Directory_Model_CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * Catalog config
     *
     * @var Magento_Catalog_Model_Config
     */
    protected $_catalogConfig;

    /**
     * Catalog product visibility
     *
     * @var Magento_Catalog_Model_Product_Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * Attribute collection factory
     *
     * @var Magento_Catalog_Model_Resource_Product_Attribute_CollectionFactory
     */
    protected $_attributeCollectionFactory;

    /**
     * Initialize dependencies
     *
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Catalog_Model_Resource_Product_Attribute_CollectionFactory $attributeCollectionFactory
     * @param Magento_Catalog_Model_Product_Visibility $catalogProductVisibility
     * @param Magento_Catalog_Model_Config $catalogConfig
     * @param Magento_Directory_Model_CurrencyFactory $currencyFactory
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_CatalogSearch_Helper_Data $helper
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Catalog_Model_Resource_Product_Attribute_CollectionFactory $attributeCollectionFactory,
        Magento_Catalog_Model_Product_Visibility $catalogProductVisibility,
        Magento_Catalog_Model_Config $catalogConfig,
        Magento_Directory_Model_CurrencyFactory $currencyFactory,
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_CatalogSearch_Helper_Data $helper,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_attributeCollectionFactory = $attributeCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_catalogConfig = $catalogConfig;
        $this->_currencyFactory = $currencyFactory;
        $this->_productFactory = $productFactory;
        $this->_storeManager = $storeManager;
        $this->_engine = $helper->getEngine();
        $this->_setResourceModel($this->_engine->getResourceName());
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve array of attributes used in advanced search
     *
     * @return array
     */
    public function getAttributes()
    {
        /* @var $attributes Magento_Catalog_Model_Resource_Eav_Resource_Product_Attribute_Collection */
        $attributes = $this->getData('attributes');
        if (is_null($attributes)) {
            $product = $this->_productFactory->create();
            $attributes = $this->_attributeCollectionFactory->create()
                ->addHasOptionsFilter()
                ->addDisplayInAdvancedSearchFilter()
                ->addStoreLabel($this->_storeManager->getStore()->getId())
                ->setOrder('main_table.attribute_id', 'asc')
                ->load();
            foreach ($attributes as $attribute) {
                $attribute->setEntity($product->getResource());
            }
            $this->setData('attributes', $attributes);
        }
        return $attributes;
    }

    /**
     * Add advanced search filters to product collection
     *
     * @param   array $values
     * @return  Magento_CatalogSearch_Model_Advanced
     * @throws Magento_Core_Exception
     */
    public function addFilters($values)
    {
        $attributes     = $this->getAttributes();
        $hasConditions  = false;
        $allConditions  = array();

        foreach ($attributes as $attribute) {
            /* @var $attribute Magento_Catalog_Model_Resource_Eav_Attribute */
            if (!isset($values[$attribute->getAttributeCode()])) {
                continue;
            }
            $value = $values[$attribute->getAttributeCode()];

            if ($attribute->getAttributeCode() == 'price') {
                $value['from'] = isset($value['from']) ? trim($value['from']) : '';
                $value['to'] = isset($value['to']) ? trim($value['to']) : '';
                if (is_numeric($value['from']) || is_numeric($value['to'])) {
                    if (!empty($value['currency'])) {
                        $rate = $this->_storeManager->getStore()->getBaseCurrency()->getRate($value['currency']);
                    } else {
                        $rate = 1;
                    }
                    if ($this->_getResource()->addRatedPriceFilter(
                        $this->getProductCollection(), $attribute, $value, $rate)
                    ) {
                        $hasConditions = true;
                        $this->_addSearchCriteria($attribute, $value);
                    }
                }
            } else if ($attribute->isIndexable()) {
                if (!is_string($value) || strlen($value) != 0) {
                    if ($this->_getResource()->addIndexableAttributeModifiedFilter(
                        $this->getProductCollection(), $attribute, $value)) {
                        $hasConditions = true;
                        $this->_addSearchCriteria($attribute, $value);
                    }
                }
            } else {
                $condition = $this->_getResource()->prepareCondition($attribute, $value, $this->getProductCollection());
                if ($condition === false) {
                    continue;
                }

                $this->_addSearchCriteria($attribute, $value);

                $table = $attribute->getBackend()->getTable();
                if ($attribute->getBackendType() == 'static'){
                    $attributeId = $attribute->getAttributeCode();
                } else {
                    $attributeId = $attribute->getId();
                }
                $allConditions[$table][$attributeId] = $condition;
            }
        }
        if ($allConditions) {
            $this->getProductCollection()->addFieldsToFilter($allConditions);
        } else if (!$hasConditions) {
            throw new Magento_Core_Exception(__('Please specify at least one search term.'));
        }

        return $this;
    }

    /**
     * Add data about search criteria to object state
     *
     * @param   Magento_Eav_Model_Entity_Attribute $attribute
     * @param   mixed $value
     * @return  Magento_CatalogSearch_Model_Advanced
     */
    protected function _addSearchCriteria($attribute, $value)
    {
        $name = $attribute->getStoreLabel();

        if (is_array($value)) {
            if (isset($value['from']) && isset($value['to'])) {
                if (!empty($value['from']) || !empty($value['to'])) {
                    if (isset($value['currency'])) {
                        /** @var $currencyModel Magento_Directory_Model_Currency */
                        $currencyModel = $this->_currencyFactory->create()->load($value['currency']);
                        $from = $currencyModel->format($value['from'], array(), false);
                        $to = $currencyModel->format($value['to'], array(), false);
                    } else {
                        $currencyModel = null;
                    }

                    if (strlen($value['from']) > 0 && strlen($value['to']) > 0) {
                        // -
                        $value = sprintf('%s - %s',
                            ($currencyModel ? $from : $value['from']), ($currencyModel ? $to : $value['to']));
                    } elseif (strlen($value['from']) > 0) {
                        // and more
                        $value = __('%1 and greater', ($currencyModel ? $from : $value['from']));
                    } elseif (strlen($value['to']) > 0) {
                        // to
                        $value = __('up to %1', ($currencyModel ? $to : $value['to']));
                    }
                } else {
                    return $this;
                }
            }
        }

        if (($attribute->getFrontendInput() == 'select' || $attribute->getFrontendInput() == 'multiselect')
            && is_array($value)
        ) {
            foreach ($value as $key => $val){
                $value[$key] = $attribute->getSource()->getOptionText($val);

                if (is_array($value[$key])) {
                    $value[$key] = $value[$key]['label'];
                }
            }
            $value = implode(', ', $value);
        } else if ($attribute->getFrontendInput() == 'select' || $attribute->getFrontendInput() == 'multiselect') {
            $value = $attribute->getSource()->getOptionText($value);
            if (is_array($value))
                $value = $value['label'];
        } else if ($attribute->getFrontendInput() == 'boolean') {
            $value = $value == 1
                ? __('Yes')
                : __('No');
        }

        $this->_searchCriterias[] = array('name' => $name, 'value' => $value);
        return $this;
    }

    /**
     * Returns prepared search criterias in text
     *
     * @return array
     */
    public function getSearchCriterias()
    {
        return $this->_searchCriterias;
    }

    /**
     * Retrieve advanced search product collection
     *
     * @return Magento_CatalogSearch_Model_Resource_Advanced_Collection
     */
    public function getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $collection = $this->_engine->getAdvancedResultCollection();
            $this->prepareProductCollection($collection);
            if (!$collection) {
                return $collection;
            }
            $this->_productCollection = $collection;
        }

        return $this->_productCollection;
    }

    /**
     * Prepare product collection
     *
     * @param Magento_CatalogSearch_Model_Resource_Advanced_Collection $collection
     * @return Magento_Catalog_Model_Layer
     */
    public function prepareProductCollection($collection)
    {
        $collection->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
            ->setStore($this->_storeManager->getStore())
            ->addMinimalPrice()
            ->addTaxPercents()
            ->addStoreFilter()
            ->setVisibility($this->_catalogProductVisibility->getVisibleInSearchIds());

        return $this;
    }
}
