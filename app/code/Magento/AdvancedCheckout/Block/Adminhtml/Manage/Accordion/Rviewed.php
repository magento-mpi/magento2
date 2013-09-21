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
class Magento_AdvancedCheckout_Block_Adminhtml_Manage_Accordion_Rviewed
    extends Magento_AdvancedCheckout_Block_Adminhtml_Manage_Accordion_Abstract
{
    /**
     * Javascript list type name for this grid
     */
    protected $_listType = 'rviewed';

    /**
     * Adminhtml sales
     *
     * @var Magento_Adminhtml_Helper_Sales
     */
    protected $_adminhtmlSales = null;

    /**
     * @param Magento_Adminhtml_Helper_Sales $adminhtmlSales
     * @param Magento_Data_CollectionFactory $collectionFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Magento_Adminhtml_Helper_Sales $adminhtmlSales,
        Magento_Data_CollectionFactory $collectionFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_Core_Model_Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_adminhtmlSales = $adminhtmlSales;
        parent::__construct($collectionFactory, $coreData, $context, $storeManager, $urlModel, $coreRegistry, $data);
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
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    public function getItemsCollection()
    {
        if (!$this->hasData('items_collection')) {
            $collection = Mage::getModel('Magento_Reports_Model_Event')
                ->getCollection()
                ->addStoreFilter($this->_getStore()->getWebsite()->getStoreIds())
                ->addRecentlyFiler(Magento_Reports_Model_Event::EVENT_PRODUCT_VIEW, $this->_getCustomer()->getId(), 0);
            $productIds = array();
            foreach ($collection as $event) {
                $productIds[] = $event->getObjectId();
            }

            $productCollection = parent::getItemsCollection();
            if ($productIds) {
                $attributes = Mage::getSingleton('Magento_Catalog_Model_Config')->getProductAttributes();
                $productCollection = Mage::getModel('Magento_Catalog_Model_Product')->getCollection()
                    ->setStoreId($this->_getStore()->getId())
                    ->addStoreFilter($this->_getStore()->getId())
                    ->addAttributeToSelect($attributes)
                    ->addIdFilter($productIds)
                    ->addAttributeToFilter('status', Magento_Catalog_Model_Product_Status::STATUS_ENABLED);

                Mage::getSingleton('Magento_CatalogInventory_Model_Stock_Status')
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
