<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Product;

class View extends \Magento\Catalog\Controller\Product
{
    /**
     * Redirect if product failed to load
     *
     * @return void
     */
    protected function noProductRedirect()
    {
        if (isset($_GET['store']) && !$this->getResponse()->isRedirect()) {
            $this->_redirect('');
        } elseif (!$this->getResponse()->isRedirect()) {
            $this->_forward('noroute');
        }
    }

    /**
     * Product view action
     *
     * @return void
     */
    public function execute()
    {
        // Get initial data from request
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $productId = (int) $this->getRequest()->getParam('id');
        $specifyOptions = $this->getRequest()->getParam('options');

        if ($this->getRequest()->isPost() && $this->getRequest()->getParam(self::PARAM_NAME_URL_ENCODED)) {
            $product = $this->_initProduct();
            if (!$product) {
                $this->noProductRedirect();
            }
            if ($specifyOptions) {
                $notice = $product->getTypeInstance()->getSpecifyOptionMessage();
                $this->messageManager->addNotice($notice);
            }
            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
            return;
        }

        // Prepare helper and params
        /** @var \Magento\Catalog\Helper\Product\View $viewHelper */
        $viewHelper = $this->_objectManager->get('Magento\Catalog\Helper\Product\View');

        $params = new \Magento\Framework\Object();
        $params->setCategoryId($categoryId);
        $params->setSpecifyOptions($specifyOptions);

        // Render page
        try {
            $viewHelper->prepareAndRender($productId, $this, $params);
        } catch (\Exception $e) {
            if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
                $this->noProductRedirect();
            } else {
                $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
                $this->_forward('noroute');
            }
        }
    }
}
