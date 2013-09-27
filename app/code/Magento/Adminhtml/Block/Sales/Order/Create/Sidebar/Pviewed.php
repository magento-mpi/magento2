<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order create sidebar recently view block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Sales_Order_Create_Sidebar_Pviewed extends Magento_Adminhtml_Block_Sales_Order_Create_Sidebar_Abstract
{
    /**
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_productFactory;

    /**
     * @var Magento_Reports_Model_EventFactory
     */
    protected $_eventFactory;

    /**
     * @param Magento_Reports_Model_EventFactory $eventFactory
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Adminhtml_Model_Session_Quote $sessionQuote
     * @param Magento_Adminhtml_Model_Sales_Order_Create $orderCreate
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Config $coreConfig
     * @param array $data
     */
    public function __construct(
        Magento_Reports_Model_EventFactory $eventFactory,
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Adminhtml_Model_Session_Quote $sessionQuote,
        Magento_Adminhtml_Model_Sales_Order_Create $orderCreate,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Config $coreConfig,
        array $data = array()
    ) {
        $this->_eventFactory = $eventFactory;
        $this->_productFactory = $productFactory;
        parent::__construct($sessionQuote, $orderCreate, $coreData, $context, $coreConfig, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_sidebar_pviewed');
        $this->setDataId('pviewed');
    }

    public function getHeaderText()
    {
        return __('Recently Viewed Products');
    }

    /**
     * Retrieve item collection
     *
     * @return mixed
     */
    public function getItemCollection()
    {
        $productCollection = $this->getData('item_collection');
        if (is_null($productCollection)) {
            $stores = array();
            $website = $this->_storeManager->getStore($this->getStoreId())->getWebsite();
            foreach ($website->getStores() as $store) {
                $stores[] = $store->getId();
            }

            $collection = $this->_eventFactory->create()
                ->getCollection()
                ->addStoreFilter($stores)
                ->addRecentlyFiler(Magento_Reports_Model_Event::EVENT_PRODUCT_VIEW, $this->getCustomerId(), 0);
            $productIds = array();
            foreach ($collection as $event) {
                $productIds[] = $event->getObjectId();
            }

            $productCollection = null;
            if ($productIds) {
                $productCollection = $this->_productFactory->create()
                    ->getCollection()
                    ->setStoreId($this->getQuote()->getStoreId())
                    ->addStoreFilter($this->getQuote()->getStoreId())
                    ->addAttributeToSelect('name')
                    ->addAttributeToSelect('price')
                    ->addAttributeToSelect('small_image')
                    ->addIdFilter($productIds)
                    ->load();
            }
            $this->setData('item_collection', $productCollection);
        }
        return $productCollection;
    }

    /**
     * Retrieve availability removing items in block
     *
     * @return bool
     */
    public function canRemoveItems()
    {
        return false;
    }

    /**
     * Retrieve identifier of block item
     *
     * @param Magento_Object $item
     * @return int
     */
    public function getIdentifierId($item)
    {
        return $item->getId();
    }
}
