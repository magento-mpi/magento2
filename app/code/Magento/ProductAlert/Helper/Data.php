<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ProductAlert\Helper;

use Magento\Store\Model\Store;
use Magento\Customer\Model\Session;

/**
 * ProductAlert data helper
 *
 * @category   Magento
 * @package    Magento_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
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
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Core store config
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

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
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Registry $coreRegistry
     * @param \Magento\View\LayoutInterface $layout
     * @param \Magento\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Model\Session $session
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Registry $coreRegistry,
        \Magento\View\LayoutInterface $layout,
        \Magento\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $session
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_layout = $layout;
        $this->_scopeConfig = $scopeConfig;
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

    /**
     * @return Session
     */
    public function getCustomer()
    {
        return $this->_session;
    }

    /**
     * @return Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * @param string $type
     * @return string
     */
    public function getSaveUrl($type)
    {
        return $this->_getUrl(
            'productalert/add/' . $type,
            array(
                'product_id' => $this->getProduct()->getId(),
                \Magento\App\Action\Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
            )
        );
    }

    /**
     * Create block instance
     *
     * @param string|\Magento\View\Element\AbstractBlock $block
     * @return \Magento\View\Element\AbstractBlock
     * @throws \Magento\Model\Exception
     */
    public function createBlock($block)
    {
        if (is_string($block)) {
            if (class_exists($block)) {
                $block = $this->_layout->createBlock($block);
            }
        }
        if (!$block instanceof \Magento\View\Element\AbstractBlock) {
            throw new \Magento\Model\Exception(__('Invalid block type: %1', $block));
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
        return $this->_scopeConfig->isSetFlag(\Magento\ProductAlert\Model\Observer::XML_PATH_STOCK_ALLOW, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check whether price alert is allowed
     *
     * @return bool
     */
    public function isPriceAlertAllowed()
    {
        return $this->_scopeConfig->isSetFlag(\Magento\ProductAlert\Model\Observer::XML_PATH_PRICE_ALLOW, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
