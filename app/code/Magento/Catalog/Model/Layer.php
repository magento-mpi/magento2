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
 * Catalog view layer model
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Layer extends Magento_Object
{
    /**
     * Product collections array
     *
     * @var array
     */
    protected $_productCollections = array();

    /**
     * Key which can be used for load/save aggregation data
     *
     * @var string
     */
    protected $_stateKey = null;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * Constructor
     *
     * By default is looking for first argument as array and assigns it as object
     * attributes This behavior may change in child classes
     *
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($data);
    }

    /**
     * Get layer state key
     *
     * @return string
     */
    public function getStateKey()
    {
        if ($this->_stateKey === null) {
            $this->_stateKey = 'STORE_' . Mage::app()->getStore()->getId()
                . '_CAT_' . $this->getCurrentCategory()->getId()
                . '_CUSTGROUP_' . Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerGroupId();
        }

        return $this->_stateKey;
    }

    /**
     * Get default tags for current layer state
     *
     * @param   array $additionalTags
     * @return  array
     */
    public function getStateTags(array $additionalTags = array())
    {
        $additionalTags = array_merge($additionalTags, array(
            Magento_Catalog_Model_Category::CACHE_TAG.$this->getCurrentCategory()->getId()
        ));

        return $additionalTags;
    }

    /**
     * Retrieve current layer product collection
     *
     * @return Magento_Catalog_Model_Resource_Product_Collection
     */
    public function getProductCollection()
    {
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = $this->getCurrentCategory()->getProductCollection();
            $this->prepareProductCollection($collection);
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
        }

        return $collection;
    }

    /**
     * Initialize product collection
     *
     * @param Magento_Catalog_Model_Resource_Product_Collection $collection
     * @return Magento_Catalog_Model_Layer
     */
    public function prepareProductCollection($collection)
    {
        $collection
            ->addAttributeToSelect(Mage::getSingleton('Magento_Catalog_Model_Config')->getProductAttributes())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addUrlRewrite($this->getCurrentCategory()->getId())
            ->setVisibility(Mage::getSingleton('Magento_Catalog_Model_Product_Visibility')->getVisibleInCatalogIds());

        return $this;
    }

    /**
     * Apply layer
     * Method is colling after apply all filters, can be used
     * for prepare some index data before getting information
     * about existing intexes
     *
     * @return Magento_Catalog_Model_Layer
     */
    public function apply()
    {
        $stateSuffix = '';
        foreach ($this->getState()->getFilters() as $filterItem) {
            $stateSuffix .= '_' . $filterItem->getFilter()->getRequestVar()
                . '_' . $filterItem->getValueString();
        }
        if (!empty($stateSuffix)) {
            $this->_stateKey = $this->getStateKey().$stateSuffix;
        }

        return $this;
    }

    /**
     * Retrieve current category model
     * If no category found in registry, the root will be taken
     *
     * @return Magento_Catalog_Model_Category
     */
    public function getCurrentCategory()
    {
        $category = $this->getData('current_category');
        if (is_null($category)) {
            $category = $this->_coreRegistry->registry('current_category');
            if ($category) {
                $this->setData('current_category', $category);
            } else {
                $category = Mage::getModel('Magento_Catalog_Model_Category')
                    ->load($this->getCurrentStore()->getRootCategoryId());
                $this->setData('current_category', $category);
            }
        }

        return $category;
    }

    /**
     * Change current category object
     *
     * @param mixed $category
     * @return Magento_Catalog_Model_Layer
     */
    public function setCurrentCategory($category)
    {
        if (is_numeric($category)) {
            $category = Mage::getModel('Magento_Catalog_Model_Category')->load($category);
        }
        if (!$category instanceof Magento_Catalog_Model_Category) {
            Mage::throwException(__('The category must be an instance of Magento_Catalog_Model_Category.'));
        }
        if (!$category->getId()) {
            Mage::throwException(__('Please correct the category.'));
        }

        if ($category->getId() != $this->getCurrentCategory()->getId()) {
            $this->setData('current_category', $category);
        }

        return $this;
    }

    /**
     * Retrieve current store model
     *
     * @return Magento_Core_Model_Store
     */
    public function getCurrentStore()
    {
        return Mage::app()->getStore();
    }

    /**
     * Get collection of all filterable attributes for layer products set
     *
     * @return Magento_Catalog_Model_Resource_Attribute_Collection
     */
    public function getFilterableAttributes()
    {
        $setIds = $this->_getSetIds();
        if (!$setIds) {
            return array();
        }
        /** @var $collection Magento_Catalog_Model_Resource_Product_Attribute_Collection */
        $collection = Mage::getResourceModel('Magento_Catalog_Model_Resource_Product_Attribute_Collection');
        $collection
            ->setItemObjectClass('Magento_Catalog_Model_Resource_Eav_Attribute')
            ->setAttributeSetFilter($setIds)
            ->addStoreLabel(Mage::app()->getStore()->getId())
            ->setOrder('position', 'ASC');
        $collection = $this->_prepareAttributeCollection($collection);
        $collection->load();

        return $collection;
    }

    /**
     * Prepare attribute for use in layered navigation
     *
     * @param   Magento_Eav_Model_Entity_Attribute $attribute
     * @return  Magento_Eav_Model_Entity_Attribute
     */
    protected function _prepareAttribute($attribute)
    {
        Mage::getResourceSingleton('Magento_Catalog_Model_Resource_Product')->getAttribute($attribute);
        return $attribute;
    }

    /**
     * Add filters to attribute collection
     *
     * @param   Magento_Catalog_Model_Resource_Attribute_Collection $collection
     * @return  Magento_Catalog_Model_Resource_Attribute_Collection
     */
    protected function _prepareAttributeCollection($collection)
    {
        $collection->addIsFilterableFilter();
        return $collection;
    }

    /**
     * Retrieve layer state object
     *
     * @return Magento_Catalog_Model_Layer_State
     */
    public function getState()
    {
        $state = $this->getData('state');
        if (is_null($state)) {
            Magento_Profiler::start(__METHOD__);
            $state = Mage::getModel('Magento_Catalog_Model_Layer_State');
            $this->setData('state', $state);
            Magento_Profiler::stop(__METHOD__);
        }

        return $state;
    }

    /**
     * Get attribute sets identifiers of current product set
     *
     * @return array
     */
    protected function _getSetIds()
    {
        return $this->getProductCollection()->getSetIds();
    }
}
