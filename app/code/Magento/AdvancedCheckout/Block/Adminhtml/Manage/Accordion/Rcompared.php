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
class Magento_AdvancedCheckout_Block_Adminhtml_Manage_Accordion_Rcompared
    extends Magento_AdvancedCheckout_Block_Adminhtml_Manage_Accordion_Abstract
{
    /**
     * Javascript list type name for this grid
     */
    protected $_listType = 'rcompared';

    /**
     * @var Magento_Adminhtml_Helper_Sales
     */
    protected $_adminhtmlSales;

    /**
     * @var Magento_Catalog_Model_Product_Compare_ListFactory
     */
    protected $_compareListFactory;

    /**
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_productFactory;

    /**
     * @param Magento_Adminhtml_Helper_Sales $adminhtmlSales
     * @param Magento_Data_CollectionFactory $collectionFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Catalog_Model_Product_Compare_ListFactory $compareListFactory
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
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Catalog_Model_Product_Compare_ListFactory $compareListFactory,
        array $data = array()
    ) {
        parent::__construct($collectionFactory, $coreData, $context, $storeManager, $urlModel, $coreRegistry, $data);
        $this->_adminhtmlSales = $adminhtmlSales;
        $this->_productFactory = $productFactory;
        $this->_compareListFactory = $compareListFactory;
    }

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
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    public function getItemsCollection()
    {
        if (!$this->hasData('items_collection')) {
            $skipProducts = array();
            $collection = $this->_compareListFactory->create()
                ->getItemCollection()
                ->useProductItem(true)
                ->setStoreId($this->_getStore()->getId())
                ->addStoreFilter($this->_getStore()->getId())
                ->setCustomerId($this->_getCustomer()->getId());
            foreach ($collection as $_item) {
                $skipProducts[] = $_item->getProductId();
            }

            // prepare products collection and apply visitors log to it
            $attributes = Mage::getSingleton('Magento_Catalog_Model_Config')->getProductAttributes();
            if (!in_array('status', $attributes)) {
                // Status attribute is required even if it is not used in product listings
                array_push($attributes, 'status');
            }
            $productCollection = $this->_productFactory->create()->getCollection()
                ->setStoreId($this->_getStore()->getId())
                ->addStoreFilter($this->_getStore()->getId())
                ->addAttributeToSelect($attributes);
            Mage::getResourceSingleton('Magento_Reports_Model_Resource_Event')->applyLogToCollection(
                $productCollection,
                Magento_Reports_Model_Event::EVENT_PRODUCT_COMPARE,
                $this->_getCustomer()->getId(),
                0,
                $skipProducts
            );
            $productCollection = $this->_adminhtmlSales->applySalableProductTypesFilter($productCollection);
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
