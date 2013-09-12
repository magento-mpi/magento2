<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * ProductAlert data helper
 *
 * @category   Magento
 * @package    Magento_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ProductAlert_Helper_Data extends Magento_Core_Helper_Url
{
    /**
     * Current product instance (override registry one)
     *
     * @var null|Magento_Catalog_Model_Product
     */
    protected $_product = null;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Get current product instance
     *
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!is_null($this->_product)) {
            return $this->_product;
        }
        return $this->_coreRegistry->registry('product');
    }

    /**
     * Set current product instance
     *
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_ProductAlert_Helper_Data
     */
    public function setProduct($product)
    {
        $this->_product = $product;
        return $this;
    }

    public function getCustomer()
    {
        return Mage::getSingleton('Magento_Customer_Model_Session');
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
            Mage::throwException(__('Invalid block type: %1', $block));
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
        return Mage::getStoreConfigFlag(Magento_ProductAlert_Model_Observer::XML_PATH_STOCK_ALLOW);
    }

    /**
     * Check whether price alert is allowed
     *
     * @return bool
     */
    public function isPriceAlertAllowed()
    {
        return Mage::getStoreConfigFlag(Magento_ProductAlert_Model_Observer::XML_PATH_PRICE_ALLOW);
    }
}
