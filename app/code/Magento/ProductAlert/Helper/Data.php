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
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @var Magento_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_session;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_Layout $layout
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Customer_Model_Session $session
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_Layout $layout,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Customer_Model_Session $session
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_layout = $layout;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_session = $session;
        parent::__construct($context, $storeManager);
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
        return $this->_session;
    }

    public function getStore()
    {
        return $this->_storeManager->getStore();
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
     * @throws Magento_Core_Exception
     */
    public function createBlock($block)
    {
        if (is_string($block)) {
            if (class_exists($block)) {
                $block = $this->_layout->createBlock($block);
            }
        }
        if (!$block instanceof Magento_Core_Block_Abstract) {
            throw new Magento_Core_Exception(__('Invalid block type: %1', $block));
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
        return $this->_coreStoreConfig->getConfigFlag(Magento_ProductAlert_Model_Observer::XML_PATH_STOCK_ALLOW);
    }

    /**
     * Check whether price alert is allowed
     *
     * @return bool
     */
    public function isPriceAlertAllowed()
    {
        return $this->_coreStoreConfig->getConfigFlag(Magento_ProductAlert_Model_Observer::XML_PATH_PRICE_ALLOW);
    }
}
