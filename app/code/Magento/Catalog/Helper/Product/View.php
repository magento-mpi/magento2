<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog category helper
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Helper\Product;

class View extends \Magento\Core\Helper\AbstractHelper
{
    // List of exceptions throwable during prepareAndRender() method
    public $ERR_NO_PRODUCT_LOADED = 1;
    public $ERR_BAD_CONTROLLER_INTERFACE = 2;

    /**
     * Path to list of session models to get messages
     */
    const XML_PATH_SESSION_MESSAGE_MODELS = 'global/session/catalog/product/message_models';

    /**
     * General config object
     *
     * @var \Magento\Core\Model\Config
     */
    protected $_config;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Config $config
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Config $config
    ) {
        parent::__construct($context);
        $this->_config = $config;
    }

    /**
     * Inits layout for viewing product page
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Core\Controller\Front\Action $controller
     *
     * @return \Magento\Catalog\Helper\Product\View
     */
    public function initProductLayout($product, $controller)
    {
        $design = \Mage::getSingleton('Magento\Catalog\Model\Design');
        $settings = $design->getDesignSettings($product);

        if ($settings->getCustomDesign()) {
            $design->applyCustomDesign($settings->getCustomDesign());
        }

        $update = $controller->getLayout()->getUpdate();
        $controller->addPageLayoutHandles(
            array('id' => $product->getId(), 'sku' => $product->getSku(), 'type' => $product->getTypeId())
        );
        $controller->loadLayoutUpdates();
        // Apply custom layout update once layout is loaded
        $layoutUpdates = $settings->getLayoutUpdates();
        if ($layoutUpdates) {
            if (is_array($layoutUpdates)) {
                foreach ($layoutUpdates as $layoutUpdate) {
                    $update->addUpdate($layoutUpdate);
                }
            }
        }

        $controller->generateLayoutXml()->generateLayoutBlocks();

        // Apply custom layout (page) template once the blocks are generated
        if ($settings->getPageLayout()) {
            $controller->getLayout()->helper('Magento\Page\Helper\Layout')->applyTemplate($settings->getPageLayout());
        }

        $currentCategory = \Mage::registry('current_category');
        $root = $controller->getLayout()->getBlock('root');
        if ($root) {
            $controllerClass = $controller->getFullActionName();
            if ($controllerClass != 'catalog-product-view') {
                $root->addBodyClass('catalog-product-view');
            }
            $root->addBodyClass('product-' . $product->getUrlKey());
            if ($currentCategory instanceof \Magento\Catalog\Model\Category) {
                $root->addBodyClass('categorypath-' . $currentCategory->getUrlPath())
                    ->addBodyClass('category-' . $currentCategory->getUrlKey());
            }
        }

        return $this;
    }

    /**
     * Prepares product view page - inits layout and all needed stuff
     *
     * $params can have all values as $params in \Magento\Catalog\Helper\Product - initProduct().
     * Plus following keys:
     *   - 'buy_request' - \Magento\Object holding buyRequest to configure product
     *   - 'specify_options' - boolean, whether to show 'Specify options' message
     *   - 'configure_mode' - boolean, whether we're in Configure-mode to edit product configuration
     *
     * @param int $productId
     * @param \Magento\Core\Controller\Front\Action $controller
     * @param null|\Magento\Object $params
     *
     * @return \Magento\Catalog\Helper\Product\View
     * @throws \Magento\Core\Exception
     */
    public function prepareAndRender($productId, $controller, $params = null)
    {
        // Prepare data
        $productHelper = \Mage::helper('Magento\Catalog\Helper\Product');
        if (!$params) {
            $params = new \Magento\Object();
        }

        // Standard algorithm to prepare and rendern product view page
        $product = $productHelper->initProduct($productId, $controller, $params);
        if (!$product) {
            throw new \Magento\Core\Exception(__('Product is not loaded'), $this->ERR_NO_PRODUCT_LOADED);
        }

        $buyRequest = $params->getBuyRequest();
        if ($buyRequest) {
            $productHelper->prepareProductOptions($product, $buyRequest);
        }

        if ($params->hasConfigureMode()) {
            $product->setConfigureMode($params->getConfigureMode());
        }

        \Mage::dispatchEvent('catalog_controller_product_view', array('product' => $product));

        if ($params->getSpecifyOptions()) {
            $notice = $product->getTypeInstance()->getSpecifyOptionMessage();
            \Mage::getSingleton('Magento\Catalog\Model\Session')->addNotice($notice);
        }

        \Mage::getSingleton('Magento\Catalog\Model\Session')->setLastViewedProductId($product->getId());

        $this->initProductLayout($product, $controller);

        if ($controller instanceof \Magento\Catalog\Controller\Product\View\ViewInterface) {
            foreach ($this->_getSessionMessageModels() as $sessionModel) {
                $controller->initLayoutMessages($sessionModel);
            }
        } else {
            throw new \Magento\Core\Exception(
                __('Bad controller interface for showing product'),
                $this->ERR_BAD_CONTROLLER_INTERFACE
            );
        }
        $controller->renderLayout();

        return $this;
    }

    /**
     * Get list of session models with messages
     *
     * @return array
     */
    protected function _getSessionMessageModels()
    {
        $messageModels = $this->_config->getNode(self::XML_PATH_SESSION_MESSAGE_MODELS)
            ->asArray();
        return array_values($messageModels);
    }
}
