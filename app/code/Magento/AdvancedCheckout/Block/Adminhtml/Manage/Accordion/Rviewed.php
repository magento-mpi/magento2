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
 * Accordion grid for recently viewed products
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion;

class Rviewed
    extends \Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion\AbstractAccordion
{
    /**
     * Javascript list type name for this grid
     */
    protected $_listType = 'rviewed';

    /**
     * Adminhtml sales
     *
     * @var \Magento\Adminhtml\Helper\Sales
     */
    protected $_adminhtmlSales = null;

    /**
     * @param \Magento\Adminhtml\Helper\Sales $adminhtmlSales
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Adminhtml\Helper\Sales $adminhtmlSales,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Core\Model\Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_adminhtmlSales = $adminhtmlSales;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $coreRegistry, $data);
    }

    /**
     * Initialize Grid
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('source_rviewed');
        if ($this->_getStore()) {
            $this->setHeaderText(
                __('Recently Viewed Products (%1)', $this->getItemsCount())
            );
        }
    }

    /**
     * Prepare customer wishlist product collection
     *
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    public function getItemsCollection()
    {
        if (!$this->hasData('items_collection')) {
            $collection = \Mage::getModel('Magento\Reports\Model\Event')
                ->getCollection()
                ->addStoreFilter($this->_getStore()->getWebsite()->getStoreIds())
                ->addRecentlyFiler(\Magento\Reports\Model\Event::EVENT_PRODUCT_VIEW, $this->_getCustomer()->getId(), 0);
            $productIds = array();
            foreach ($collection as $event) {
                $productIds[] = $event->getObjectId();
            }

            $productCollection = parent::getItemsCollection();
            if ($productIds) {
                $attributes = \Mage::getSingleton('Magento\Catalog\Model\Config')->getProductAttributes();
                $productCollection = \Mage::getModel('Magento\Catalog\Model\Product')->getCollection()
                    ->setStoreId($this->_getStore()->getId())
                    ->addStoreFilter($this->_getStore()->getId())
                    ->addAttributeToSelect($attributes)
                    ->addIdFilter($productIds)
                    ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Status::STATUS_ENABLED);

                \Mage::getSingleton('Magento\CatalogInventory\Model\Stock\Status')
                    ->addIsInStockFilterToCollection($productCollection);
                $productCollection = $this->_adminhtmlSales
                    ->applySalableProductTypesFilter($productCollection);
                $productCollection->addOptionsToResult();
            }
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
        return $this->getUrl('*/*/viewRecentlyViewed', array('_current'=>true));
    }

}
