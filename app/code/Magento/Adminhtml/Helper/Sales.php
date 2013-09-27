<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Helper_Sales extends Magento_Core_Helper_Abstract
{
    /**
     * @var Magento_Sales_Model_Config
     */
    protected $_salesConfig;

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Sales_Model_Config $salesConfig
     */
    public function __construct(
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Helper_Context $context,
        Magento_Sales_Model_Config $salesConfig
    ) {
        $this->_storeManager = $storeManager;
        $this->_salesConfig = $salesConfig;
        parent::__construct($context);
    }

    /**
     * Display price attribute value in base order currency and in place order currency
     *
     * @param   Magento_Object $dataObject
     * @param   string $code
     * @param   bool $strong
     * @param   string $separator
     * @return  string
     */
    public function displayPriceAttribute($dataObject, $code, $strong = false, $separator = '<br/>')
    {
        return $this->displayPrices(
            $dataObject,
            $dataObject->getData('base_'.$code),
            $dataObject->getData($code),
            $strong,
            $separator
        );
    }

    /**
     * Get "double" prices html (block with base and place currency)
     *
     * @param   Magento_Object $dataObject
     * @param   float $basePrice
     * @param   float $price
     * @param   bool $strong
     * @param   string $separator
     * @return  string
     */
    public function displayPrices($dataObject, $basePrice, $price, $strong = false, $separator = '<br/>')
    {
        $order = false;
        if ($dataObject instanceof Magento_Sales_Model_Order) {
            $order = $dataObject;
        } else {
            $order = $dataObject->getOrder();
        }

        if ($order && $order->isCurrencyDifferent()) {
            $res = '<strong>';
            $res.= $order->formatBasePrice($basePrice);
            $res.= '</strong>'.$separator;
            $res.= '['.$order->formatPrice($price).']';
        } elseif ($order) {
            $res = $order->formatPrice($price);
            if ($strong) {
                $res = '<strong>'.$res.'</strong>';
            }
        } else {
            $res = $this->_storeManager->getStore()->formatPrice($price);
            if ($strong) {
                $res = '<strong>'.$res.'</strong>';
            }
        }
        return $res;
    }

    /**
     * Filter collection by removing not available product types
     *
     * @param Magento_Core_Model_Resource_Db_Collection_Abstract $collection
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    public function applySalableProductTypesFilter($collection)
    {
        $productTypes = $this->_salesConfig->getAvailableProductTypes();
        foreach($collection->getItems() as $key => $item) {
            if ($item instanceof Magento_Catalog_Model_Product) {
                $type = $item->getTypeId();
            } else if ($item instanceof Magento_Sales_Model_Order_Item) {
                $type = $item->getProductType();
            } else if ($item instanceof Magento_Sales_Model_Quote_Item) {
                $type = $item->getProductType();
            } else {
                $type = '';
            }
            if (!in_array($type, $productTypes)) {
                $collection->removeItemByKey($key);
            }
        }
        return $collection;
    }
}
