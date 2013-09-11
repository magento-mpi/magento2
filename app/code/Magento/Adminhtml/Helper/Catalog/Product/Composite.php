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
namespace Magento\Adminhtml\Helper\Catalog\Product;

class Composite extends \Magento\Core\Helper\AbstractHelper
{
     /**
     * Init layout of product configuration update result
     *
     * @param \Magento\Adminhtml\Controller\Action $controller
     * @return \Magento\Adminhtml\Helper\Catalog\Product\Composite
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
     * @param \Magento\Adminhtml\Controller\Action $controller
     * @param \Magento\Object $updateResult
     * @return \Magento\Adminhtml\Helper\Catalog\Product\Composite
     */
    public function renderUpdateResult($controller, \Magento\Object $updateResult)
    {
        \Mage::register('composite_update_result', $updateResult);

        $this->_initUpdateResultLayout($controller);
        $controller->renderLayout();
    }

     /**
     * Init composite product configuration layout
     *
     * $isOk - true or false, whether action was completed nicely or with some error
     * If $isOk is FALSE (some error during configuration), so $productType must be null
     *
     * @param \Magento\Adminhtml\Controller\Action $controller
     * @param bool $isOk
     * @param string $productType
     * @return \Magento\Adminhtml\Helper\Catalog\Product\Composite
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
     * @param \Magento\Adminhtml\Controller\Action $controller
     * @param \Magento\Object $configureResult
     * @return \Magento\Adminhtml\Helper\Catalog\Product\Composite
     */
    public function renderConfigureResult($controller, \Magento\Object $configureResult)
    {
        try {
            if (!$configureResult->getOk()) {
                \Mage::throwException($configureResult->getMessage());
            };

            $currentStoreId = (int) $configureResult->getCurrentStoreId();
            if (!$currentStoreId) {
                $currentStoreId = \Mage::app()->getStore()->getId();
            }

            $product = \Mage::getModel('Magento\Catalog\Model\Product')
                ->setStoreId($currentStoreId)
                ->load($configureResult->getProductId());
            if (!$product->getId()) {
                \Mage::throwException(__('The product is not loaded.'));
            }
            \Mage::register('current_product', $product);
            \Mage::register('product', $product);

            // Register customer we're working with
            $currentCustomer = $configureResult->getCurrentCustomer();
            if (!$currentCustomer) {
                $currentCustomerId = (int) $configureResult->getCurrentCustomerId();
                if ($currentCustomerId) {
                    $currentCustomer = \Mage::getModel('Magento\Customer\Model\Customer')
                        ->load($currentCustomerId);
                }
            }
            if ($currentCustomer) {
                \Mage::register('current_customer', $currentCustomer);
            }

            // Prepare buy request values
            $buyRequest = $configureResult->getBuyRequest();
            if ($buyRequest) {
                \Mage::helper('Magento\Catalog\Helper\Product')->prepareProductOptions($product, $buyRequest);
            }

            $isOk = true;
            $productType = $product->getTypeId();
        } catch (\Exception $e) {
            $isOk = false;
            $productType = null;
            \Mage::register('composite_configure_result_error_message', $e->getMessage());
        }

        $this->_initConfigureResultLayout($controller, $isOk, $productType);
        $controller->renderLayout();
    }
}
