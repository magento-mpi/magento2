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
 * Product Compare List Model
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_Catalog_Model_Product_Compare_List extends Magento_Object
{
    /**
     * Log visitor
     *
     * @var Magento_Log_Model_Visitor
     */
    protected $_logVisitor;

    /**
     * Customer session
     *
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * Catalog product compare item
     *
     * @var Magento_Catalog_Model_Resource_Product_Compare_Item
     */
    protected $_catalogProductCompareItem;

    /**
     * Item collection factory
     *
     * @var Magento_Catalog_Model_Resource_Product_Compare_Item_CollectionFactory
     */
    protected $_itemCollectionFactory;

    /**
     * Compare item factory
     *
     * @var Magento_Catalog_Model_Product_Compare_ItemFactory
     */
    protected $_compareItemFactory;

    /**
     * Constructor
     *
     * @param Magento_Catalog_Model_Product_Compare_ItemFactory $compareItemFactory
     * @param Magento_Catalog_Model_Resource_Product_Compare_Item_CollectionFactory $itemCollectionFactory
     * @param Magento_Catalog_Model_Resource_Product_Compare_Item $catalogProductCompareItem
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Log_Model_Visitor $logVisitor
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Model_Product_Compare_ItemFactory $compareItemFactory,
        Magento_Catalog_Model_Resource_Product_Compare_Item_CollectionFactory $itemCollectionFactory,
        Magento_Catalog_Model_Resource_Product_Compare_Item $catalogProductCompareItem,
        Magento_Customer_Model_Session $customerSession,
        Magento_Log_Model_Visitor $logVisitor,
        array $data = array()
    ) {
        $this->_compareItemFactory = $compareItemFactory;
        $this->_itemCollectionFactory = $itemCollectionFactory;
        $this->_catalogProductCompareItem = $catalogProductCompareItem;
        $this->_customerSession = $customerSession;
        $this->_logVisitor = $logVisitor;
        parent::__construct($data);
    }

    /**
     * Add product to Compare List
     *
     * @param int|Magento_Catalog_Model_Product $product
     * @return Magento_Catalog_Model_Product_Compare_List
     */
    public function addProduct($product)
    {
        /* @var $item Magento_Catalog_Model_Product_Compare_Item */
        $item = $this->_compareItemFactory->create();
        $this->_addVisitorToItem($item);
        $item->loadByProduct($product);

        if (!$item->getId()) {
            $item->addProductData($product);
            $item->save();
        }

        return $this;
    }

    /**
     * Add products to compare list
     *
     * @param array $productIds
     * @return Magento_Catalog_Model_Product_Compare_List
     */
    public function addProducts($productIds)
    {
        if (is_array($productIds)) {
            foreach ($productIds as $productId) {
                $this->addProduct($productId);
            }
        }
        return $this;
    }

    /**
     * Retrieve Compare Items Collection
     *
     * @return product_compare_item_collection
     */
    public function getItemCollection()
    {
        return $this->_itemCollectionFactory->create();
    }

    /**
     * Remove product from compare list
     *
     * @param int|Magento_Catalog_Model_Product $product
     * @return Magento_Catalog_Model_Product_Compare_List
     */
    public function removeProduct($product)
    {
        /* @var $item Magento_Catalog_Model_Product_Compare_Item */
        $item = $this->_compareItemFactory->create();
        $this->_addVisitorToItem($item);
        $item->loadByProduct($product);

        if ($item->getId()) {
            $item->delete();
        }

        return $this;
    }

    /**
     * Add visitor and customer data to compare item
     *
     * @param Magento_Catalog_Model_Product_Compare_Item $item
     * @return Magento_Catalog_Model_Product_Compare_List
     */
    protected function _addVisitorToItem($item)
    {
        $item->addVisitorId($this->_logVisitor->getId());
        if ($this->_customerSession->isLoggedIn()) {
            $item->addCustomerData($this->_customerSession->getCustomer());
        }

        return $this;
    }

    /**
     * Check has compare items by visitor/customer
     *
     * @param int $customerId
     * @param int $visitorId
     * @return bool
     */
    public function hasItems($customerId, $visitorId)
    {
        return $this->_catalogProductCompareItem
            ->getCount($customerId, $visitorId);
    }
}
