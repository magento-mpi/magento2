<?php
/**
 * Product controller.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Catalog_ProductController
    extends Mage_Core_Controller_Front_Action
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

        $params = new Varien_Object();
        $params->setCategoryId($categoryId);

        return Mage::helper('Mage_Catalog_Helper_Product')->initProduct($productId, $this, $params);
    }

    /**
     * Initialize product view layout
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Catalog_ProductController
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
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $productId  = (int) $this->getRequest()->getParam('id');
        $specifyOptions = $this->getRequest()->getParam('options');

        // Prepare helper and params
        $viewHelper = Mage::helper('Mage_Catalog_Helper_Product_View');

        $params = new Varien_Object();
        $params->setCategoryId($categoryId);
        $params->setSpecifyOptions($specifyOptions);

        // Render page
        try {
            $viewHelper->prepareAndRender($productId, $this, $params);
        } catch (Exception $e) {
            if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
                if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
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

    /**
     * Test action for product service
     * @todo remove
     */
    public function serviceAction()
    {
        /** @var $manager Mage_Core_Service_Manager */
        $manager = Mage::getObjectManager()->create('Mage_Core_Service_Manager');

        try {
            $product = $manager->call('Mage_Catalog_Service_Product', 'item', array('entity_id' => 1));
        } catch (Exception $e) {
            echo $e;d();
        }


        die(nl2br(var_dump($product)));
    }

}
