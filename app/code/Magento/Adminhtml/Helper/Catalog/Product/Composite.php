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
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Helper_Catalog_Product_Composite extends Magento_Core_Helper_Abstract
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;
    
     /**
      * Catalog product
      *
      * @var Magento_Catalog_Helper_Product
      */
    protected $_catalogProduct = null;

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_productFactory;

    /**
     * @var Magento_Customer_Model_CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @param Magento_Customer_Model_CustomerFactory $customerFactory
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Catalog_Helper_Product $catalogProduct
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Customer_Model_CustomerFactory $customerFactory,
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Catalog_Helper_Product $catalogProduct,
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_productFactory = $productFactory;
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        $this->_catalogProduct = $catalogProduct;
        parent::__construct($context);
    }

    /**
     * Init layout of product configuration update result
     *
     * @param Magento_Adminhtml_Controller_Action $controller
     * @return Magento_Adminhtml_Helper_Catalog_Product_Composite
     */
    protected function _initUpdateResultLayout($controller)
    {
        $controller->getLayout()->getUpdate()
            ->addHandle('ADMINHTML_CATALOG_PRODUCT_COMPOSITE_UPDATE_RESULT');
        $controller->loadLayoutUpdates()->generateLayoutXml()->generateLayoutBlocks();
        return $this;
    }

    /**
     * Prepares and render result of composite product configuration update for a case
     * when single configuration submitted
     *
     * @param Magento_Adminhtml_Controller_Action $controller
     * @param Magento_Object $updateResult
     * @return Magento_Adminhtml_Helper_Catalog_Product_Composite
     */
    public function renderUpdateResult($controller, Magento_Object $updateResult)
    {
        $this->_coreRegistry->register('composite_update_result', $updateResult);

        $this->_initUpdateResultLayout($controller);
        $controller->renderLayout();
    }

     /**
      * Init composite product configuration layout
      *
      * $isOk - true or false, whether action was completed nicely or with some error
      * If $isOk is FALSE (some error during configuration), so $productType must be null
      *
      * @param Magento_Adminhtml_Controller_Action $controller
      * @param bool $isOk
      * @param string $productType
      * @return Magento_Adminhtml_Helper_Catalog_Product_Composite
      */
    protected function _initConfigureResultLayout($controller, $isOk, $productType)
    {
        $update = $controller->getLayout()->getUpdate();
        if ($isOk) {
            $update->addHandle('ADMINHTML_CATALOG_PRODUCT_COMPOSITE_CONFIGURE')
                ->addHandle('catalog_product_view_type_' . $productType);
        } else {
            $update->addHandle('ADMINHTML_CATALOG_PRODUCT_COMPOSITE_CONFIGURE_ERROR');
        }
        $controller->loadLayoutUpdates()->generateLayoutXml()->generateLayoutBlocks();
        return $this;
    }

    /**
     * Prepares and render result of composite product configuration request
     *
     * $configureResult holds either:
     *  - 'ok' = true, and 'product_id', 'buy_request', 'current_store_id', 'current_customer' or 'current_customer_id'
     *  - 'error' = true, and 'message' to show
     *
     * @param Magento_Adminhtml_Controller_Action $controller
     * @param Magento_Object $configureResult
     * @return Magento_Adminhtml_Helper_Catalog_Product_Composite
     */
    public function renderConfigureResult($controller, Magento_Object $configureResult)
    {
        try {
            if (!$configureResult->getOk()) {
                throw new Magento_Core_Exception($configureResult->getMessage());
            };

            $currentStoreId = (int) $configureResult->getCurrentStoreId();
            if (!$currentStoreId) {
                $currentStoreId = $this->_storeManager->getStore()->getId();
            }

            $product = $this->_productFactory->create()
                ->setStoreId($currentStoreId)
                ->load($configureResult->getProductId());
            if (!$product->getId()) {
                throw new Magento_Core_Exception(__('The product is not loaded.'));
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
        } catch (Exception $e) {
            $isOk = false;
            $productType = null;
            $this->_coreRegistry->register('composite_configure_result_error_message', $e->getMessage());
        }

        $this->_initConfigureResultLayout($controller, $isOk, $productType);
        $controller->renderLayout();
    }
}
