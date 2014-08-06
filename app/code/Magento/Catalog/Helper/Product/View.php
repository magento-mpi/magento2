<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Helper\Product;

/**
 * Catalog category helper
 */
class View extends \Magento\Framework\App\Helper\AbstractHelper
{
    // List of exceptions throwable during prepareAndRender() method
    public $ERR_NO_PRODUCT_LOADED = 1;

    public $ERR_BAD_CONTROLLER_INTERFACE = 2;

    /**
     * List of catalog product session message groups
     *
     * @var array
     */
    protected $messageGroups;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Catalog product
     *
     * @var \Magento\Catalog\Helper\Product
     */
    protected $_catalogProduct = null;

    /**
     * Catalog design
     *
     * @var \Magento\Catalog\Model\Design
     */
    protected $_catalogDesign;

    /**
     * Catalog session
     *
     * @var \Magento\Catalog\Model\Session
     */
    protected $_catalogSession;

    /**
     * @var \Magento\Framework\App\ViewInterface
     */
    protected $_view;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Catalog\Model\Design $catalogDesign
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\ViewInterface $view
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param array $messageGroups
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Catalog\Model\Design $catalogDesign,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\ViewInterface $view,
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        array $messageGroups = array()
    ) {
        $this->_catalogSession = $catalogSession;
        $this->_catalogDesign = $catalogDesign;
        $this->_catalogProduct = $catalogProduct;
        $this->_coreRegistry = $coreRegistry;
        $this->_view = $view;
        $this->messageGroups = $messageGroups;
        $this->pageConfig = $pageConfig;
        $this->messageManager = $messageManager;
        parent::__construct($context);
    }

    /**
     * Inits layout for viewing product page
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\App\Action\Action $controller
     * @param null|\Magento\Framework\Object $params
     *
     * @return \Magento\Catalog\Helper\Product\View
     */
    public function initProductLayout($product, $controller, $params = null)
    {
        $settings = $this->_catalogDesign->getDesignSettings($product);

        if ($settings->getCustomDesign()) {
            $this->_catalogDesign->applyCustomDesign($settings->getCustomDesign());
        }

        // Apply custom page layout
        if ($settings->getPageLayout()) {
            $this->pageConfig->setPageLayout($settings->getPageLayout());
        }

        $update = $this->_view->getLayout()->getUpdate();

        if ($params && $params->getBeforeHandles()) {
            foreach ($params->getBeforeHandles() as $handle) {
                $this->_view->addPageLayoutHandles(
                    array('id' => $product->getId(), 'sku' => $product->getSku(), 'type' => $product->getTypeId()),
                    $handle
                );
            }
        }

        $this->_view->addPageLayoutHandles(
            array('id' => $product->getId(), 'sku' => $product->getSku(), 'type' => $product->getTypeId())
        );

        if ($params && $params->getAfterHandles()) {
            foreach ($params->getAfterHandles() as $handle) {
                $this->_view->addPageLayoutHandles(
                    array('id' => $product->getId(), 'sku' => $product->getSku(), 'type' => $product->getTypeId()),
                    $handle
                );
            }
        }

        $this->_view->loadLayoutUpdates();
        // Apply custom layout update once layout is loaded
        $layoutUpdates = $settings->getLayoutUpdates();
        if ($layoutUpdates) {
            if (is_array($layoutUpdates)) {
                foreach ($layoutUpdates as $layoutUpdate) {
                    $update->addUpdate($layoutUpdate);
                }
            }
        }

        $this->_view->generateLayoutXml();
        $this->_view->generateLayoutBlocks();

        $currentCategory = $this->_coreRegistry->registry('current_category');
        $root = $this->_view->getLayout()->getBlock('root');
        if ($root) {
            $controllerClass = $this->_request->getFullActionName();
            if ($controllerClass != 'catalog-product-view') {
                $root->addBodyClass('catalog-product-view');
            }
            $root->addBodyClass('product-' . $product->getUrlKey());
            if ($currentCategory instanceof \Magento\Catalog\Model\Category) {
                $root->addBodyClass(
                    'categorypath-' . $currentCategory->getUrlPath()
                )->addBodyClass(
                    'category-' . $currentCategory->getUrlKey()
                );
            }
        }

        return $this;
    }

    /**
     * Prepares product view page - inits layout and all needed stuff
     *
     * $params can have all values as $params in \Magento\Catalog\Helper\Product - initProduct().
     * Plus following keys:
     *   - 'buy_request' - \Magento\Framework\Object holding buyRequest to configure product
     *   - 'specify_options' - boolean, whether to show 'Specify options' message
     *   - 'configure_mode' - boolean, whether we're in Configure-mode to edit product configuration
     *
     * @param int $productId
     * @param \Magento\Framework\App\Action\Action $controller
     * @param null|\Magento\Framework\Object $params
     *
     * @return \Magento\Catalog\Helper\Product\View
     * @throws \Magento\Framework\Model\Exception
     */
    public function prepareAndRender($productId, $controller, $params = null)
    {
        // Prepare data
        $productHelper = $this->_catalogProduct;
        if (!$params) {
            $params = new \Magento\Framework\Object();
        }

        // Standard algorithm to prepare and render product view page
        $product = $productHelper->initProduct($productId, $controller, $params);
        if (!$product) {
            throw new \Magento\Framework\Model\Exception(__('Product is not loaded'), $this->ERR_NO_PRODUCT_LOADED);
        }

        $buyRequest = $params->getBuyRequest();
        if ($buyRequest) {
            $productHelper->prepareProductOptions($product, $buyRequest);
        }

        if ($params->hasConfigureMode()) {
            $product->setConfigureMode($params->getConfigureMode());
        }

        $this->_eventManager->dispatch('catalog_controller_product_view', array('product' => $product));

        $this->_catalogSession->setLastViewedProductId($product->getId());

        $this->initProductLayout($product, $controller, $params);

        if ($controller instanceof \Magento\Catalog\Controller\Product\View\ViewInterface) {
            $this->_view->getLayout()->initMessages($this->messageGroups);
        } else {
            throw new \Magento\Framework\Model\Exception(
                __('Bad controller interface for showing product'),
                $this->ERR_BAD_CONTROLLER_INTERFACE
            );
        }
        $this->_view->renderLayout();

        return $this;
    }
}
