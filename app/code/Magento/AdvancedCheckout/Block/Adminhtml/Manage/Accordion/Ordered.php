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
 * Accordion grid for recently ordered products
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdvancedCheckout_Block_Adminhtml_Manage_Accordion_Ordered
    extends Magento_AdvancedCheckout_Block_Adminhtml_Manage_Accordion_Abstract
{
    /**
     * Collection field name for using in controls
     * @var string
     */
    protected $_controlFieldName = 'item_id';

    /**
     * Javascript list type name for this grid
     */
    protected $_listType = 'ordered';

    /**
     * Url to configure this grid's items
     */
    protected $_configureRoute = '*/checkout/configureOrderedItem';

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_Config $coreConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_Config $coreConfig,
        array $data = array()
    ) {
        parent::__construct(
            $coreData,
            $context,
            $storeManager,
            $urlModel,
            $coreRegistry,
            $data
        );
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Initialize Grid
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('source_ordered');
        if ($this->_getStore()) {
            $this->setHeaderText(
                __('Last ordered items (%1)', $this->getItemsCount())
            );
        }
    }

    /**
     * Returns custom last ordered products renderer for price column content
     *
     * @return null|string
     */
    protected function _getPriceRenderer()
    {
        return 'Magento_AdvancedCheckout_Block_Adminhtml_Manage_Grid_Renderer_Ordered_Price';
    }

    /**
     * Prepare customer wishlist product collection
     *
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    public function getItemsCollection()
    {
        if (!$this->hasData('items_collection')) {
            $productIds = array();
            $storeIds = $this->_getStore()->getWebsite()->getStoreIds();

            // Load last order of a customer
            /* @var $collection Magento_Core_Model_Resource_Db_Collection_Abstract */
            $collection = Mage::getResourceModel('Magento_Sales_Model_Resource_Order_Collection')
                ->addAttributeToFilter('customer_id', $this->_getCustomer()->getId())
                ->addAttributeToFilter('store_id', array('in' => $storeIds))
                ->addAttributeToSort('created_at', 'desc')
                ->setPage(1, 1)
                ->load();
            foreach ($collection as $order) {
                break;
            }

            // Add products to order items
            if (isset($order)) {
                $productIds = array();
                $collection = $order->getItemsCollection();
                foreach ($collection as $item) {
                    if ($item->getParentItem()) {
                        $collection->removeItemByKey($item->getId());
                    } else {
                        $productIds[$item->getProductId()] = $item->getProductId();
                    }
                }
                if ($productIds) {
                    // Load products collection
                    $attributes = Mage::getSingleton('Magento_Catalog_Model_Config')->getProductAttributes();
                    $products = Mage::getModel('Magento_Catalog_Model_Product')->getCollection()
                        ->setStore($this->_getStore())
                        ->addAttributeToSelect($attributes)
                        ->addAttributeToSelect('sku')
                        ->addAttributeToFilter('type_id',
                            array_keys(
                                $this->_coreConfig->getNode('adminhtml/sales/order/create/available_product_types')
                                    ->asArray()
                            )
                        )->addAttributeToFilter('status', Magento_Catalog_Model_Product_Status::STATUS_ENABLED)
                        ->addStoreFilter($this->_getStore())
                        ->addIdFilter($productIds);
                    Mage::getSingleton('Magento_CatalogInventory_Model_Stock_Status')->addIsInStockFilterToCollection($products);
                    $products->addOptionsToResult();

                    // Set products to items
                    foreach ($collection as $item) {
                        $productId = $item->getProductId();
                        $product = $products->getItemById($productId);
                        if ($product) {
                            $item->setProduct($product);
                        } else {
                            $collection->removeItemByKey($item->getId());
                        }
                    }
                }
            }
            $this->setData('items_collection', $productIds ? $collection : parent::getItemsCollection());
        }
        return $this->getData('items_collection');
    }

    /**
     * Return grid URL for sorting and filtering
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/viewOrdered', array('_current'=>true));
    }
}
