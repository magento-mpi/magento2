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
 * Adminhtml sales order create sidebar block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Order_Create_Sidebar_Abstract extends Magento_Adminhtml_Block_Sales_Order_Create_Abstract
{
    /**
     * Default Storage action on selected item
     *
     * @var string
     */
    protected $_sidebarStorageAction = 'add';

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * @param Magento_Adminhtml_Model_Session_Quote $sessionQuote
     * @param Magento_Adminhtml_Model_Sales_Order_Create $orderCreate
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Config $coreConfig
     * @param array $data
     */
    public function __construct(
        Magento_Adminhtml_Model_Session_Quote $sessionQuote,
        Magento_Adminhtml_Model_Sales_Order_Create $orderCreate,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Config $coreConfig,
        array $data = array()
    ) {
        parent::__construct($sessionQuote, $orderCreate, $coreData, $context, $data);
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Return name of sidebar storage action
     *
     * @return string
     */
    public function getSidebarStorageAction()
    {
        return $this->_sidebarStorageAction;
    }

    /**
     * Retrieve display block availability
     *
     * @return bool
     */
    public function canDisplay()
    {
        return $this->getCustomerId();
    }

    public function canDisplayItemQty()
    {
        return false;
    }

    /**
     * Retrieve availability removing items in block
     *
     * @return bool
     */
    public function canRemoveItems()
    {
        return true;
    }

    /**
     * Retrieve identifier of block item
     *
     * @param   Magento_Object $item
     * @return  int
     */
    public function getIdentifierId($item)
    {
        return $item->getProductId();
    }

    /**
     * Retrieve item identifier of block item
     *
     * @param   mixed $item
     * @return  int
     */
    public function getItemId($item)
    {
        return $item->getId();
    }

    /**
     * Retrieve product identifier linked with item
     *
     * @param   mixed $item
     * @return  int
     */
    public function getProductId($item)
    {
        return $item->getId();
    }

    /**
     * Retreive item count
     *
     * @return int
     */
    public function getItemCount()
    {
        $count = $this->getData('item_count');
        if (is_null($count)) {
            $count = count($this->getItems());
            $this->setData('item_count', $count);
        }
        return $count;
    }

    /**
     * Retrieve all items
     *
     * @return array
     */
    public function getItems()
    {
        $items = array();
        $collection = $this->getItemCollection();
        if ($collection) {
            $productTypes = $this->_coreConfig->getNode('adminhtml/sales/order/create/available_product_types')->asArray();
            if (is_array($collection)) {
                $items = $collection;
            } else {
                $items = $collection->getItems();
            }

            /*
             * Filtering items by allowed product type
             */
            foreach($items as $key => $item) {
                if ($item instanceof Magento_Catalog_Model_Product) {
                    $type = $item->getTypeId();
                } else if ($item instanceof Magento_Sales_Model_Order_Item) {
                    $type = $item->getProductType();
                } else if ($item instanceof Magento_Sales_Model_Quote_Item) {
                    $type = $item->getProductType();
                } else {
                    $type = '';
                    // Maybe some item, that can give us product via getProduct()
                    if (($item instanceof Magento_Object) || method_exists($item, 'getProduct')) {
                        $product = $item->getProduct();
                        if ($product && ($product instanceof Magento_Catalog_Model_Product)) {
                            $type = $product->getTypeId();
                        }
                    }
                }
                if (!isset($productTypes[$type])) {
                    unset($items[$key]);
                }
            }
        }

        return $items;
    }

    /**
     * Retrieve item collection
     *
     * @return mixed
     */
    public function getItemCollection()
    {
        return false;
    }

    public function canDisplayPrice()
    {
        return true;
    }

}
