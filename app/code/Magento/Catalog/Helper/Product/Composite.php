<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Helper\Product;

/**
 * Adminhtml catalog product composite helper
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Composite extends \Magento\App\Helper\AbstractHelper
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
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
     * @var \Magento\App\ViewInterface
     */
    protected $_view;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param \Magento\Registry $coreRegistry
     * @param \Magento\App\ViewInterface $view
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Registry $coreRegistry,
        \Magento\App\ViewInterface $view
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_productFactory = $productFactory;
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        $this->_catalogProduct = $catalogProduct;
        $this->_view = $view;
        parent::__construct($context);
    }

    /**
     * Init layout of product configuration update result
     *
     * @return $this
     */
    protected function _initUpdateResultLayout()
    {
        $this->_view->getLayout()->getUpdate()->addHandle('CATALOG_PRODUCT_COMPOSITE_UPDATE_RESULT');
        $this->_view->loadLayoutUpdates();
        $this->_view->generateLayoutXml();
        $this->_view->generateLayoutBlocks();
        return $this;
    }

    /**
     * Prepares and render result of composite product configuration update for a case
     * when single configuration submitted
     *
     * @param \Magento\Object $updateResult
     * @return void
     */
    public function renderUpdateResult(\Magento\Object $updateResult)
    {
        $this->_coreRegistry->register('composite_update_result', $updateResult);

        $this->_initUpdateResultLayout();
        $this->_view->renderLayout();
    }

    /**
     * Init composite product configuration layout
     *
     * $isOk - true or false, whether action was completed nicely or with some error
     * If $isOk is FALSE (some error during configuration), so $productType must be null
     *
     * @param bool $isOk
     * @param string $productType
     * @return $this
     */
    protected function _initConfigureResultLayout($isOk, $productType)
    {
        $update = $this->_view->getLayout()->getUpdate();
        if ($isOk) {
            $update->addHandle('CATALOG_PRODUCT_COMPOSITE_CONFIGURE')
                ->addHandle('catalog_product_view_type_' . $productType);
        } else {
            $update->addHandle('CATALOG_PRODUCT_COMPOSITE_CONFIGURE_ERROR');
        }
        $this->_view->loadLayoutUpdates();
        $this->_view->generateLayoutXml();
        $this->_view->generateLayoutBlocks();
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
     * @return void
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
        $this->_view->renderLayout();
    }
}
