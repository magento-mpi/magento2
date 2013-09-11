<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Accordion grid for Recently compared products
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion;

class Rcompared
    extends \Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion\AbstractAccordion
{
    /**
     * Javascript list type name for this grid
     */
    protected $_listType = 'rcompared';

    /**
     * Initialize Grid
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('source_rcompared');
        if ($this->_getStore()) {
            $this->setHeaderText(
                __('Recently Compared Products (%1)', $this->getItemsCount())
            );
        }
    }

    /**
     * Return items collection
     *
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    public function getItemsCollection()
    {
        if (!$this->hasData('items_collection')) {
            $skipProducts = array();
            $collection = \Mage::getModel('Magento\Catalog\Model\Product\Compare\ListCompare')
                ->getItemCollection()
                ->useProductItem(true)
                ->setStoreId($this->_getStore()->getId())
                ->addStoreFilter($this->_getStore()->getId())
                ->setCustomerId($this->_getCustomer()->getId());
            foreach ($collection as $_item) {
                $skipProducts[] = $_item->getProductId();
            }

            // prepare products collection and apply visitors log to it
            $attributes = \Mage::getSingleton('Magento\Catalog\Model\Config')->getProductAttributes();
            if (!in_array('status', $attributes)) {
                // Status attribute is required even if it is not used in product listings
                array_push($attributes, 'status');
            }
            $productCollection = \Mage::getModel('Magento\Catalog\Model\Product')->getCollection()
                ->setStoreId($this->_getStore()->getId())
                ->addStoreFilter($this->_getStore()->getId())
                ->addAttributeToSelect($attributes);
            \Mage::getResourceSingleton('Magento\Reports\Model\Resource\Event')->applyLogToCollection(
                $productCollection,
                \Magento\Reports\Model\Event::EVENT_PRODUCT_COMPARE,
                $this->_getCustomer()->getId(),
                0,
                $skipProducts
            );
            $productCollection = \Mage::helper('Magento\Adminhtml\Helper\Sales')->applySalableProductTypesFilter($productCollection);
            // Remove disabled and out of stock products from the grid
            foreach ($productCollection as $product) {
                if (!$product->getStockItem()->getIsInStock() || !$product->isInStock()) {
                    $productCollection->removeItemByKey($product->getId());
                }
            }
            $productCollection->addOptionsToResult();
            $this->setData('items_collection', $productCollection);
        }
        return $this->_getData('items_collection');
    }

    /**
     * Retrieve Grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/viewRecentlyCompared', array('_current'=>true));
    }
}
