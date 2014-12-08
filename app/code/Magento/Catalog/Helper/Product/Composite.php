<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Helper\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Product;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Model\Converter;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ViewInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\StoreManagerInterface;

/**
 * Adminhtml catalog product composite helper
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Composite extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * Catalog product
     *
     * @var Product
     */
    protected $_catalogProduct = null;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ViewInterface
     */
    protected $_view;

    /**
     * @var Converter
     */
    protected $_converter;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param Product $catalogProduct
     * @param Registry $coreRegistry
     * @param ViewInterface $view
     * @param Converter $converter
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Product $catalogProduct,
        Registry $coreRegistry,
        ViewInterface $view,
        Converter $converter,
        ProductRepositoryInterface $productRepository
    ) {
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        $this->_catalogProduct = $catalogProduct;
        $this->_view = $view;
        $this->_converter = $converter;
        $this->productRepository = $productRepository;
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
     * @param \Magento\Framework\Object $updateResult
     * @return void
     */
    public function renderUpdateResult(\Magento\Framework\Object $updateResult)
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
            $update->addHandle(
                'CATALOG_PRODUCT_COMPOSITE_CONFIGURE'
            )->addHandle(
                'catalog_product_view_type_' . $productType
            );
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
     * The $configureResult variable holds either:
     *  - 'ok' = true, and 'product_id', 'buy_request', 'current_store_id', 'current_customer_id'
     *  - 'error' = true, and 'message' to show
     *
     * @param \Magento\Framework\Object $configureResult
     * @return void
     */
    public function renderConfigureResult(\Magento\Framework\Object $configureResult)
    {
        try {
            if (!$configureResult->getOk()) {
                throw new \Magento\Framework\Model\Exception($configureResult->getMessage());
            }

            $currentStoreId = (int)$configureResult->getCurrentStoreId();
            if (!$currentStoreId) {
                $currentStoreId = $this->_storeManager->getStore()->getId();
            }

            try {
                $product = $this->productRepository->getById($configureResult->getProductId(), false, $currentStoreId);
            } catch (NoSuchEntityException $e) {
                throw new \Magento\Framework\Model\Exception(__('The product is not loaded.'), 0, $e);
            }
            $this->_coreRegistry->register('current_product', $product);
            $this->_coreRegistry->register('product', $product);

            // Register customer we're working with
            $customerId = (int)$configureResult->getCurrentCustomerId();
            // TODO: Remove the customer model from the registry once all readers are refactored
            $customerModel = $this->_converter->loadCustomerModel($customerId);
            $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER, $customerModel);
            $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customerId);

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
