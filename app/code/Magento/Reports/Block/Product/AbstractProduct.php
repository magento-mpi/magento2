<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reports Recently Products Abstract Block
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Block\Product;

abstract class AbstractProduct extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * Product Index model name
     *
     * @var string
     */
    protected $_indexName;

    /**
     * Product Index model instance
     *
     * @var \Magento\Reports\Model\Product\Index\AbstractIndex
     */
    protected $_indexModel;

    /**
     * Product Index Collection
     *
     * @var \Magento\Reports\Model\Resource\Product\Index\Collection\AbstractCollection
     */
    protected $_collection;

    /**
     * Retrieve page size
     *
     * @return int
     */
    public function getPageSize()
    {
        if ($this->hasData('page_size')) {
            return $this->getData('page_size');
        }
        return 5;
    }

    /**
     * Retrieve product ids, that must not be included in collection
     *
     * @return array
     */
    protected function _getProductsToSkip()
    {
        return array();
    }

    /**
     * Retrieve Product Index model instance
     *
     * @return \Magento\Reports\Model\Product\Index\AbstractIndex
     */
    protected function _getModel()
    {
        if (is_null($this->_indexModel)) {
            if (is_null($this->_indexName)) {
                \Mage::throwException(__('Index model name must be defined'));
            }

            $this->_indexModel = \Mage::getModel($this->_indexName);
        }

        return $this->_indexModel;
    }

    /**
     * Public method for retrieve Product Index model
     *
     * @return \Magento\Reports\Model\Product\Index\AbstractIndex
     */
    public function getModel()
    {
        return $this->_getModel();
    }

    /**
     * Retrieve Index Product Collection
     *
     * @return \Magento\Reports\Model\Resource\Product\Index\Collection\AbstractCollection
     */
    public function getItemsCollection()
    {
        if (is_null($this->_collection)) {
            $attributes = \Mage::getSingleton('Magento\Catalog\Model\Config')->getProductAttributes();

            $this->_collection = $this->_getModel()
                ->getCollection()
                ->addAttributeToSelect($attributes);

                if ($this->getCustomerId()) {
                    $this->_collection->setCustomerId($this->getCustomerId());
                }

                $this->_collection->excludeProductIds($this->_getModel()->getExcludeProductIds())
                    ->addUrlRewrite()
                    ->setPageSize($this->getPageSize())
                    ->setCurPage(1);

            /* Price data is added to consider item stock status using price index */
            $this->_collection->addPriceData();

            $ids = $this->getProductIds();
            if (empty($ids)) {
                $this->_collection->addIndexFilter();
            } else {
                $this->_collection->addFilterByIds($ids);
            }
            $this->_collection->setAddedAtOrder()
                ->setVisibility(\Mage::getSingleton('Magento\Catalog\Model\Product\Visibility')->getVisibleInSiteIds());
        }

        return $this->_collection;
    }

    /**
     * Retrieve count of product index items
     *
     * @return int
     */
    public function getCount()
    {
        if (!$this->_getModel()->getCount()) {
            return 0;
        }
        return $this->getItemsCollection()->count();
    }
}
