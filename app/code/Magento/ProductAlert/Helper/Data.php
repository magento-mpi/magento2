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
     * @var \Magento\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\View\LayoutInterface $layout
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Customer\Model\Session $session
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\View\LayoutInterface $layout,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Customer\Model\Session $session
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
            \Magento\App\Action\Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
        ));
    }

    /**
     * Create block instance
     *
     * @param string|\Magento\View\Element\AbstractBlock $block
     * @return \Magento\View\Element\AbstractBlock
     * @throws \Magento\Core\Exception
     */
    public function createBlock($block)
    {
        if (is_string($block)) {
            if (class_exists($block)) {
                $block = $this->_layout->createBlock($block);
            }
        }
        if (!$block instanceof \Magento\View\Element\AbstractBlock) {
            throw new \Magento\Core\Exception(__('Invalid block type: %1', $block));
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
