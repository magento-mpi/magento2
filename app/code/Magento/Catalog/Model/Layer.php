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
namespace Magento\Catalog\Model;

class Layer extends \Magento\Object
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
     * Get layer state key
     *
     * @return string
     */
    public function getStateKey()
    {
        if ($this->_stateKey === null) {
            $this->_stateKey = 'STORE_'.Mage::app()->getStore()->getId()
                . '_CAT_' . $this->getCurrentCategory()->getId()
                . '_CUSTGROUP_' . \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerGroupId();
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
            \Magento\Catalog\Model\Category::CACHE_TAG.$this->getCurrentCategory()->getId()
        ));

        return $additionalTags;
    }

    /**
     * Retrieve current layer product collection
     *
     * @return \Magento\Catalog\Model\Resource\Product\Collection
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
     * @param \Magento\Catalog\Model\Resource\Product\Collection $collection
     * @return \Magento\Catalog\Model\Layer
     */
    public function prepareProductCollection($collection)
    {
        $collection
            ->addAttributeToSelect(\Mage::getSingleton('Magento\Catalog\Model\Config')->getProductAttributes())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addUrlRewrite($this->getCurrentCategory()->getId())
            ->setVisibility(\Mage::getSingleton('Magento\Catalog\Model\Product\Visibility')->getVisibleInCatalogIds());

        return $this;
    }

    /**
     * Apply layer
     * Method is colling after apply all filters, can be used
     * for prepare some index data before getting information
     * about existing intexes
     *
     * @return \Magento\Catalog\Model\Layer
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
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory()
    {
        $category = $this->getData('current_category');
        if (is_null($category)) {
            if ($category = \Mage::registry('current_category')) {
                $this->setData('current_category', $category);
            }
            else {
                $category = \Mage::getModel('Magento\Catalog\Model\Category')->load($this->getCurrentStore()->getRootCategoryId());
                $this->setData('current_category', $category);
            }
        }

        return $category;
    }

    /**
     * Change current category object
     *
     * @param mixed $category
     * @return \Magento\Catalog\Model\Layer
     */
    public function setCurrentCategory($category)
    {
        if (is_numeric($category)) {
            $category = \Mage::getModel('Magento\Catalog\Model\Category')->load($category);
        }
        if (!$category instanceof \Magento\Catalog\Model\Category) {
            \Mage::throwException(__('The category must be an instance of \Magento\Catalog\Model\Category.'));
        }
        if (!$category->getId()) {
            \Mage::throwException(__('Please correct the category.'));
        }

        if ($category->getId() != $this->getCurrentCategory()->getId()) {
            $this->setData('current_category', $category);
        }

        return $this;
    }

    /**
     * Retrieve current store model
     *
     * @return \Magento\Core\Model\Store
     */
    public function getCurrentStore()
    {
        return \Mage::app()->getStore();
    }

    /**
     * Get collection of all filterable attributes for layer products set
     *
     * @return Magento_Catalog_Model_Resource_Attribute_Collection
     */
    public function getFilterableAttributes()
    {
//        $entity = \Mage::getSingleton('Magento\Eav\Model\Config')
//            ->getEntityType('catalog_product');

        $setIds = $this->_getSetIds();
        if (!$setIds) {
            return array();
        }
        /** @var $collection \Magento\Catalog\Model\Resource\Product\Attribute\Collection */
        $collection = \Mage::getResourceModel('Magento\Catalog\Model\Resource\Product\Attribute\Collection');
        $collection
            ->setItemObjectClass('\Magento\Catalog\Model\Resource\Eav\Attribute')
            ->setAttributeSetFilter($setIds)
            ->addStoreLabel(\Mage::app()->getStore()->getId())
            ->setOrder('position', 'ASC');
        $collection = $this->_prepareAttributeCollection($collection);
        $collection->load();

        return $collection;
    }

    /**
     * Prepare attribute for use in layered navigation
     *
     * @param   \Magento\Eav\Model\Entity\Attribute $attribute
     * @return  \Magento\Eav\Model\Entity\Attribute
     */
    protected function _prepareAttribute($attribute)
    {
        \Mage::getResourceSingleton('Magento\Catalog\Model\Resource\Product')->getAttribute($attribute);
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
     * @return \Magento\Catalog\Model\Layer\State
     */
    public function getState()
    {
        $state = $this->getData('state');
        if (is_null($state)) {
            \Magento\Profiler::start(__METHOD__);
            $state = \Mage::getModel('Magento\Catalog\Model\Layer\State');
            $this->setData('state', $state);
            \Magento\Profiler::stop(__METHOD__);
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
