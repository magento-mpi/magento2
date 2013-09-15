<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Model;

class Layer extends \Magento\Catalog\Model\Layer
{
    const XML_PATH_DISPLAY_LAYER_COUNT = 'catalog/search/use_layered_navigation_count';

    /**
     * Catalog search data
     *
     * @var \Magento\CatalogSearch\Helper\Data
     */
    protected $_catalogSearchData = null;

    /**
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\CatalogSearch\Helper\Data $catalogSearchData
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\CatalogSearch\Helper\Data $catalogSearchData,
        array $data = array()
    ) {
        $this->_catalogSearchData = $catalogSearchData;
        parent::__construct($coreRegistry, $data);
    }

    /**
     * Get current layer product collection
     *
     * @return Magento_Catalog_Model_Resource_Eav_Resource_Product_Collection
     */
    public function getProductCollection()
    {
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = \Mage::getResourceModel('Magento\CatalogSearch\Model\Resource\Fulltext\Collection');
            $this->prepareProductCollection($collection);
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
        }
        return $collection;
    }

    /**
     * Prepare product collection
     *
     * @param Magento_Catalog_Model_Resource_Eav_Resource_Product_Collection $collection
     * @return \Magento\Catalog\Model\Layer
     */
    public function prepareProductCollection($collection)
    {
        $collection
            ->addAttributeToSelect(Mage::getSingleton('Magento\Catalog\Model\Config')->getProductAttributes())
            ->addSearchFilter($this->_catalogSearchData->getQuery()->getQueryText())
            ->setStore(Mage::app()->getStore())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addUrlRewrite()
            ->setVisibility(\Mage::getSingleton('Magento\Catalog\Model\Product\Visibility')->getVisibleInSearchIds());

        return $this;
    }

    /**
     * Get layer state key
     *
     * @return string
     */
    public function getStateKey()
    {
        if ($this->_stateKey === null) {
            $this->_stateKey = 'Q_' . $this->_catalogSearchData->getQuery()->getId()
                . '_'. parent::getStateKey();
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
        $additionalTags = parent::getStateTags($additionalTags);
        $additionalTags[] = \Magento\CatalogSearch\Model\Query::CACHE_TAG;
        return $additionalTags;
    }

    /**
     * Add filters to attribute collection
     *
     * @param   \Magento\Catalog\Model\Resource\Eav\Resource\Product\Attribute\Collection $collection
     * @return  \Magento\Catalog\Model\Resource\Eav\Resource\Product\Attribute\Collection
     */
    protected function _prepareAttributeCollection($collection)
    {
        $collection->addIsFilterableInSearchFilter()
            ->addVisibleFilter();
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
        $attribute = parent::_prepareAttribute($attribute);
        $attribute->setIsFilterable(\Magento\Catalog\Model\Layer\Filter\Attribute::OPTIONS_ONLY_WITH_RESULTS);
        return $attribute;
    }
}
