<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\Helper;

class Sales extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Config $coreConfig
    ) {
        parent::__construct(
            $context
        );
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Display price attribute value in base order currency and in place order currency
     *
     * @param   \Magento\Object $dataObject
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
     * @param   \Magento\Object $dataObject
     * @param   float $basePrice
     * @param   float $price
     * @param   bool $strong
     * @param   string $separator
     * @return  string
     */
    public function displayPrices($dataObject, $basePrice, $price, $strong = false, $separator = '<br/>')
    {
        $order = false;
        if ($dataObject instanceof \Magento\Sales\Model\Order) {
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
            $res = \Mage::app()->getStore()->formatPrice($price);
            if ($strong) {
                $res = '<strong>'.$res.'</strong>';
            }
        }
        return $res;
    }

    /**
     * Filter collection by removing not available product types
     *
     * @param \Magento\Core\Model\Resource\Db\Collection\AbstractCollection $collection
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    public function applySalableProductTypesFilter($collection)
    {
        $productTypes = $this->_coreConfig->getNode('adminhtml/sales/order/create/available_product_types')->asArray();
        $productTypes = array_keys($productTypes);
        foreach($collection->getItems() as $key => $item) {
            if ($item instanceof \Magento\Catalog\Model\Product) {
                $type = $item->getTypeId();
            } else if ($item instanceof \Magento\Sales\Model\Order\Item) {
                $type = $item->getProductType();
            } else if ($item instanceof \Magento\Sales\Model\Quote\Item) {
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
