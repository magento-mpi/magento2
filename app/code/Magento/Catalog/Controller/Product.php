<?php
/**
 * Product controller.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Controller;

class Product
    extends \Magento\Core\Controller\Front\Action
    implements \Magento\Catalog\Controller\Product\View\ViewInterface
{
    /**
     * Initialize requested product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    protected function _initProduct()
    {
        $categoryId = (int)$this->getRequest()->getParam('category', false);
        $productId  = (int)$this->getRequest()->getParam('id');

        $params = new \Magento\Object();
        $params->setCategoryId($categoryId);

        return $this->_objectManager->get('Magento\Catalog\Helper\Product')->initProduct($productId, $this, $params);
    }

    /**
     * Initialize product view layout
     *
     * @param   \Magento\Catalog\Model\Product $product
     * @return  \Magento\Catalog\Controller\Product
     */
    protected function _initProductLayout($product)
    {
        $this->_objectManager->get('Magento\Catalog\Helper\Product\View')->initProductLayout($product, $this);
        return $this;
    }

    /**
     * Product view action
     */
    public function viewAction()
    {
        // Get initial data from request
        $categoryId = (int)$this->getRequest()->getParam('category', false);
        $productId  = (int)$this->getRequest()->getParam('id');
        $specifyOptions = $this->getRequest()->getParam('options');

        // Prepare helper and params
        /** @var \Magento\Catalog\Helper\Product\View $viewHelper */
        $viewHelper = $this->_objectManager->get('Magento\Catalog\Helper\Product\View');

        $params = new \Magento\Object();
        $params->setCategoryId($categoryId);
        $params->setSpecifyOptions($specifyOptions);

        // Render page
        try {
            $viewHelper->prepareAndRender($productId, $this, $params);
        } catch (\Exception $e) {
            if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
                if (isset($_GET['store']) && !$this->getResponse()->isRedirect()) {
                    $this->_redirect('');
                } elseif (!$this->getResponse()->isRedirect()) {
                    $this->_forward('noRoute');
                }
            } else {
                $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
                $this->_forward('noRoute');
            }
        }
    }

    /**
     * View product gallery action
     */
    public function galleryAction()
    {
        if (!$this->_initProduct()) {
            if (isset($_GET['store']) && !$this->getResponse()->isRedirect()) {
                $this->_redirect('');
            } elseif (!$this->getResponse()->isRedirect()) {
                $this->_forward('noRoute');
            }
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }
}
