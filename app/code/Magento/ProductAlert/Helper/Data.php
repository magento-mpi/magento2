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
namespace Magento\ProductAlert\Helper;

class Data extends \Magento\Core\Helper\Url
{
    /**
     * Current product instance (override registry one)
     *
     * @var null|\Magento\Catalog\Model\Product
     */
    protected $_product = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Core\Model\Layout
     */
    protected $_layout;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Core\Model\Layout $layout
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Core\Model\Layout $layout,
        \Magento\Core\Model\Store\Config $coreStoreConfig
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_layout = $layout;
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context);
    }

    /**
     * Get current product instance
     *
     * @return \Magento\Catalog\Model\Product
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
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\ProductAlert\Helper\Data
     */
    public function setProduct($product)
    {
        $this->_product = $product;
        return $this;
    }

    public function getCustomer()
    {
        return \Mage::getSingleton('Magento\Customer\Model\Session');
    }

    public function getStore()
    {
        return \Mage::app()->getStore();
    }

    public function getSaveUrl($type)
    {
        return $this->_getUrl('productalert/add/' . $type, array(
            'product_id'    => $this->getProduct()->getId(),
            \Magento\Core\Controller\Front\Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
        ));
    }

    /**
     * Create block instance
     *
     * @param string|\Magento\Core\Block\AbstractBlock $block
     * @return \Magento\Core\Block\AbstractBlock
     */
    public function createBlock($block)
    {
        if (is_string($block)) {
            if (class_exists($block)) {
                $block = $this->_layout->createBlock($block);
            }
        }
        if (!$block instanceof \Magento\Core\Block\AbstractBlock) {
            \Mage::throwException(__('Invalid block type: %1', $block));
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
        return $this->_coreStoreConfig->getConfigFlag(\Magento\ProductAlert\Model\Observer::XML_PATH_STOCK_ALLOW);
    }

    /**
     * Check whether price alert is allowed
     *
     * @return bool
     */
    public function isPriceAlertAllowed()
    {
        return $this->_coreStoreConfig->getConfigFlag(\Magento\ProductAlert\Model\Observer::XML_PATH_PRICE_ALLOW);
    }
}
