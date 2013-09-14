<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog layer model integrated with search engine
 */
namespace Magento\Search\Model\Search;

class Layer extends \Magento\CatalogSearch\Model\Layer
{
    /**
     * Retrieve current layer product collection
     *
     * @return \Magento\Catalog\Model\Resource\Product\Attribute\Collection
     */
    public function getProductCollection()
    {
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = \Mage::helper('Magento\CatalogSearch\Helper\Data')->getEngine()->getResultCollection();
            $collection->setStoreId($this->getCurrentCategory()->getStoreId());
            $this->prepareProductCollection($collection);
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
        }

        return $collection;
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
            \Magento\Catalog\Model\Category::CACHE_TAG . $this->getCurrentCategory()->getId() . '_SEARCH'
        ));

        return parent::getStateTags($additionalTags);
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
        /* @var $collection \Magento\Catalog\Model\Resource\Product\Attribute\Collection */
        $collection = \Mage::getResourceModel('Magento\Catalog\Model\Resource\Product\Attribute\Collection')
            ->setItemObjectClass('Magento\Catalog\Model\Resource\Eav\Attribute');

        if (\Mage::helper('Magento\Search\Helper\Data')->getTaxInfluence()) {
            $collection->removePriceFilter();
        }

        $collection
            ->setAttributeSetFilter($setIds)
            ->addStoreLabel(\Mage::app()->getStore()->getId())
            ->setOrder('position', 'ASC');
        $collection = $this->_prepareAttributeCollection($collection);
        $collection->load();

        return $collection;
    }
}
