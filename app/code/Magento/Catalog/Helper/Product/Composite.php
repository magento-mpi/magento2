<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml catalog product composite helper
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Helper\Product;

class Composite extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;
    
     /**
      * Catalog product
      *
      * @var \Magento\Catalog\Helper\Product
      */
    protected $_catalogProduct = null;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\View\Action\LayoutServiceInterface
     */
    protected $_layoutServices;

    /**
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\View\Action\LayoutServiceInterface $layoutServices
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\View\Action\LayoutServiceInterface $layoutServices
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_productFactory = $productFactory;
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        $this->_catalogProduct = $catalogProduct;
        $this->_layoutServices = $layoutServices;
        parent::__construct($context);
    }

    /**
     * Init layout of product configuration update result
     *
     * @return \Magento\Catalog\Helper\Product\Composite
     */
    protected function _initUpdateResultLayout()
    {
        $this->_layoutServices->getLayout()->getUpdate()->addHandle('CATALOG_PRODUCT_COMPOSITE_UPDATE_RESULT');
        $this->_layoutServices->loadLayoutUpdates();
        $this->_layoutServices->generateLayoutXml();
        $this->_layoutServices->generateLayoutBlocks();
        return $this;
    }

    /**
     * Prepares and render result of composite product configuration update for a case
     * when single configuration submitted
     *
     * @param \Magento\Object $updateResult
     * @return \Magento\Catalog\Helper\Product\Composite
     */
    public function renderUpdateResult(\Magento\Object $updateResult)
    {
        $this->_coreRegistry->register('composite_update_result', $updateResult);

        $this->_initUpdateResultLayout();
        $this->_layoutServices->renderLayout();
    }

     /**
      * Init composite product configuration layout
      *
      * $isOk - true or false, whether action was completed nicely or with some error
      * If $isOk is FALSE (some error during configuration), so $productType must be null
      *
      * @param bool $isOk
      * @param string $productType
      * @return \Magento\Catalog\Helper\Product\Composite
      */
    protected function _initConfigureResultLayout($isOk, $productType)
    {
        $update = $this->_layoutServices->getLayout()->getUpdate();
        if ($isOk) {
            $update->addHandle('CATALOG_PRODUCT_COMPOSITE_CONFIGURE')
                ->addHandle('catalog_product_view_type_' . $productType);
        } else {
            $update->addHandle('CATALOG_PRODUCT_COMPOSITE_CONFIGURE_ERROR');
        }
        $this->_layoutServices->loadLayoutUpdates();
        $this->_layoutServices->generateLayoutXml();
        $this->_layoutServices->generateLayoutBlocks();
        return $this;
    }

    /**
     * Prepares and render result of composite product configuration request
     *
     * $configureResult holds either:
     *  - 'ok' = true, and 'product_id', 'buy_request', 'current_store_id', 'current_customer' or 'current_customer_id'
     *  - 'error' = true, and 'message' to show
     *
     * @param \Magento\Object $configureResult
     * @return \Magento\Catalog\Helper\Product\Composite
     */
    public function renderConfigureResult(\Magento\Object $configureResult)
    {
        try {
            if (!$configureResult->getOk()) {
                throw new \Magento\Core\Exception($configureResult->getMessage());
            };

            $currentStoreId = (int) $configureResult->getCurrentStoreId();
            if (!$currentStoreId) {
                $currentStoreId = $this->_storeManager->getStore()->getId();
            }

            $product = $this->_productFactory->create()
                ->setStoreId($currentStoreId)
                ->load($configureResult->getProductId());
            if (!$product->getId()) {
                throw new \Magento\Core\Exception(__('The product is not loaded.'));
            }
            $this->_coreRegistry->register('current_product', $product);
            $this->_coreRegistry->register('product', $product);

            // Register customer we're working with
            $currentCustomer = $configureResult->getCurrentCustomer();
            if (!$currentCustomer) {
                $currentCustomerId = (int) $configureResult->getCurrentCustomerId();
                if ($currentCustomerId) {
                    $currentCustomer = $this->_customerFactory->create()->load($currentCustomerId);
                }
            }
            if ($currentCustomer) {
                $this->_coreRegistry->register('current_customer', $currentCustomer);
            }

            // Prepare buy request values
            $buyRequest = $configureResult->getBuyRequest();
            if ($buyRequest) {
                $this->_catalogProduct->prepareProductOptions($product, $buyRequest);
            }

            $isOk = true;
            $productType = $product->getTypeId();
        } catch (\Exception $e) {
            $isOk = false;
            $productType = null;
            $this->_coreRegistry->register('composite_configure_result_error_message', $e->getMessage());
        }

        $this->_initConfigureResultLayout($isOk, $productType);
        $this->_layoutServices->renderLayout();
    }
}
