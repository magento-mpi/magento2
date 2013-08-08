<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product controller
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Controller_Product
    extends Magento_Core_Controller_Front_Action
    implements Mage_Catalog_Controller_Product_View_Interface
{
    /**
     * Initialize requested product object
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _initProduct()
    {
        $categoryId = (int)$this->getRequest()->getParam('category', false);
        $productId  = (int)$this->getRequest()->getParam('id');

        $params = new Magento_Object();
        $params->setCategoryId($categoryId);

        return Mage::helper('Mage_Catalog_Helper_Product')->initProduct($productId, $this, $params);
    }

    /**
     * Initialize product view layout
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Catalog_Controller_Product
     */
    protected function _initProductLayout($product)
    {
        Mage::helper('Mage_Catalog_Helper_Product_View')->initProductLayout($product, $this);
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
        /** @var Mage_Catalog_Helper_Product_View $viewHelper */
        $viewHelper = Mage::helper('Mage_Catalog_Helper_Product_View');

        $params = new Magento_Object();
        $params->setCategoryId($categoryId);
        $params->setSpecifyOptions($specifyOptions);

        // Render page
        try {
            $viewHelper->prepareAndRender($productId, $this, $params);
        } catch (Exception $e) {
            if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
                if (isset($_GET['store']) && !$this->getResponse()->isRedirect()) {
                    $this->_redirect('');
                } elseif (!$this->getResponse()->isRedirect()) {
                    $this->_forward('noRoute');
                }
            } else {
                Mage::logException($e);
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
