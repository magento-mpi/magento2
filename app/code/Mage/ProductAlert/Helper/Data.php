<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ProductAlert
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * ProductAlert data helper
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ProductAlert_Helper_Data extends Magento_Core_Helper_Url
{
    /**
     * Current product instance (override registry one)
     *
     * @var null|Mage_Catalog_Model_Product
     */
    protected $_product = null;

    /**
     * Get current product instance
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!is_null($this->_product)) {
            return $this->_product;
        }
        return Mage::registry('product');
    }

    /**
     * Set current product instance
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_ProductAlert_Helper_Data
     */
    public function setProduct($product)
    {
        $this->_product = $product;
        return $this;
    }

    public function getCustomer()
    {
        return Mage::getSingleton('Mage_Customer_Model_Session');
    }

    public function getStore()
    {
        return Mage::app()->getStore();
    }

    public function getSaveUrl($type)
    {
        return $this->_getUrl('productalert/add/' . $type, array(
            'product_id'    => $this->getProduct()->getId(),
            Magento_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
        ));
    }

    /**
     * Create block instance
     *
     * @param string|Magento_Core_Block_Abstract $block
     * @return Magento_Core_Block_Abstract
     */
    public function createBlock($block)
    {
        if (is_string($block)) {
            if (class_exists($block)) {
                $block = Mage::getObjectManager()->create($block);
            }
        }
        if (!$block instanceof Magento_Core_Block_Abstract) {
            Mage::throwException(Mage::helper('Magento_Core_Helper_Data')->__('Invalid block type: %s', $block));
        }
        return $block;
    }

    /**
     * Check whether stock alert is allowed
     *
     * @return bool
     */
    public function isStockAlertAllowed()
    {
        return Mage::getStoreConfigFlag(Mage_ProductAlert_Model_Observer::XML_PATH_STOCK_ALLOW);
    }

    /**
     * Check whether price alert is allowed
     *
     * @return bool
     */
    public function isPriceAlertAllowed()
    {
        return Mage::getStoreConfigFlag(Mage_ProductAlert_Model_Observer::XML_PATH_PRICE_ALLOW);
    }
}
