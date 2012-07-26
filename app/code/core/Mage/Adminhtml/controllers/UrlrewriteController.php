<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Urlrewrites adminhtml controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_UrlrewriteController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Instantiate registry
     *
     * @return Mage_Adminhtml_UrlrewriteController
     */
    protected function _initRegistry()
    {
        $this->_initUrlrewriteRegistry($this->getRequest());
        $this->_initCatalogRegistry($this->getRequest());
        $this->_initCmsPageRegistry($this->getRequest());

        return $this;
    }

    /**
     * @param Mage_Core_Controller_Request_Http $request
     */
    protected function _initUrlrewriteRegistry($request)
    {
        // initialize urlrewrite
        Mage::register('current_urlrewrite', Mage::getModel('Mage_Core_Model_Url_Rewrite')
            ->load((int) $request->getParam('id', 0))
        );
    }

    /**
     * Load catalog product and category entities and put them into registry
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return Mage_Adminhtml_UrlrewriteController
     */
    protected function _initCatalogRegistry($request)
    {
        $productId  = $request->getParam('product', 0);
        $categoryId = $request->getParam('category', 0);
        if (Mage::registry('current_urlrewrite')->getId()) {
            $productId  = Mage::registry('current_urlrewrite')->getProductId();
            $categoryId = Mage::registry('current_urlrewrite')->getCategoryId();
        }

        Mage::register('current_product', Mage::getModel('Mage_Catalog_Model_Product')->load($productId));
        Mage::register('current_category', Mage::getModel('Mage_Catalog_Model_Category')->load($categoryId));

        return $this;
    }

    /**
     * Load cms page entity and put it into registry
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return Mage_Adminhtml_UrlrewriteController
     */
    protected function _initCmsPageRegistry($request)
    {
        $cmsPageId = (int) $request->getParam('cms_page', 0);
        if (Mage::registry('current_urlrewrite')->getId()) {
            $urlRewriteId = Mage::registry('current_urlrewrite')->getId();
            /** @var $cmsRewrite Mage_Cms_Model_Page_Urlrewrite */
            $cmsRewrite = Mage::getModel('Mage_Cms_Model_Page_Urlrewrite');
            $cmsRewrite->load($urlRewriteId, 'url_rewrite_id');
            $cmsPageId = $cmsRewrite->getCmsPageId();
        }

        Mage::register('current_cms_page', Mage::getModel('Mage_Cms_Model_Page')->load($cmsPageId));

        return $this;
    }

    /**
     * Show urlrewrites index page
     *
     */
    public function indexAction()
    {
        $this->_title($this->__('Rewrite Rules'));

        $this->loadLayout();
        $this->_setActiveMenu('Mage_Catalog::catalog_urlrewrite');
        $this->_addContent(
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Urlrewrite')
        );
        $this->renderLayout();
    }

    /**
     * Show urlrewrite edit/create page
     *
     */
    public function editAction()
    {
        $this->_title($this->__('URL Rewrite'));

        $this->_initRegistry();

        $this->loadLayout();
        $this->_setActiveMenu('Mage_Catalog::catalog_urlrewrite');
        $this->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_Urlrewrite_Edit'));
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    /**
     * Ajax products grid action
     *
     */
    public function productGridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Urlrewrite_Product_Grid')->toHtml()
        );
    }

    /**
     * Ajax categories tree loader action
     *
     */
    public function categoriesJsonAction()
    {
        $categoryId = $this->getRequest()->getParam('id', null);
        $this->getResponse()->setBody(Mage::getBlockSingleton('Mage_Adminhtml_Block_Urlrewrite_Category_Tree')
            ->getTreeArray($categoryId, true, 1)
        );
    }

    /**
     * Urlrewrite save action
     *
     */
    public function saveAction()
    {
        $this->_initRegistry();

        if ($data = $this->getRequest()->getPost()) {
            /** @var $session Mage_Adminhtml_Model_Session */
            $session = Mage::getSingleton('Mage_Adminhtml_Model_Session');
            try {
                // set basic urlrewrite data
                /** @var $model Mage_Core_Model_Url_Rewrite */
                $model = Mage::registry('current_urlrewrite');

                // Validate request path
                $requestPath = $this->getRequest()->getParam('request_path');
                Mage::helper('Mage_Core_Helper_Url_Rewrite')->validateRequestPath($requestPath);

                // Proceed and save request
                $model->setIdPath($this->getRequest()->getParam('id_path'))
                    ->setTargetPath($this->getRequest()->getParam('target_path'))
                    ->setOptions($this->getRequest()->getParam('options'))
                    ->setDescription($this->getRequest()->getParam('description'))
                    ->setRequestPath($requestPath);

                if (!$model->getId()) {
                    $model->setIsSystem(0);
                }
                if (!$model->getIsSystem()) {
                    $model->setStoreId($this->getRequest()->getParam('store_id', 0));
                }

                $this->_onUrlrewriteSaveBefore($model);

                // save and redirect
                $model->save();

                $this->_onUrlrewriteSaveAfter($model);

                $session->addSuccess(Mage::helper('Mage_Adminhtml_Helper_Data')->__('The URL Rewrite has been saved.'));
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage())
                    ->setUrlrewriteData($data);
            } catch (Exception $e) {
                $session->addException($e,
                    Mage::helper('Mage_Adminhtml_Helper_Data')->__('An error occurred while saving URL Rewrite.'))
                    ->setUrlrewriteData($data);
            }
        }
        $this->_redirectReferer();
    }

    /**
     * Call before save urlrewrite handlers
     *
     * @param Mage_Core_Model_Url_Rewrite $model
     */
    protected function _onUrlrewriteSaveBefore($model)
    {
        $this->_handleCatalogUrlrewrite($model);
        $this->_handleCmsPageUrlrewrite($model);
    }

    /**
     * Call after save urlrewrite handlers
     *
     * @param Mage_Core_Model_Url_Rewrite $model
     */
    protected function _onUrlrewriteSaveAfter($model)
    {
        $this->_handleCmsPageUrlrewriteSave($model);
    }

    /**
     * @param Mage_Core_Model_Url_Rewrite $model
     */
    protected function _handleCatalogUrlrewrite($model)
    {
        // override urlrewrite data, basing on current catalog registry combination
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::registry('current_product');
        if ($product && $product->getId()) {
            $model->setProductId($product->getId());
        } else {
            $product = null;
        }

        /** @var $category Mage_Catalog_Model_Category */
        $category = Mage::registry('current_category');
        if ($category && $category->getId()) {
            $model->setCategoryId($category->getId());
        } else {
            $category = null;
        }

        if ($product || $category) {
            /** @var $catalogUrlModel Mage_Catalog_Model_Url */
            $catalogUrlModel = Mage::getSingleton('Mage_Catalog_Model_Url');
            $idPath = $catalogUrlModel->generatePath('id', $product, $category);
            $model->setIdPath($idPath);

            // if redirect specified try to find friendly URL
            $generateTarget = true;
            if ($this->_hasRedirectOptions($model)) {
                /** @var $rewriteResource Mage_Catalog_Model_Resource_Url */
                $rewriteResource = Mage::getResourceModel('Mage_Catalog_Model_Resource_Url');
                /** @var $rewrite Mage_Core_Model_Url_Rewrite */
                $rewrite = $rewriteResource->getRewriteByIdPath($idPath, $model->getStoreId());
                if (!$rewrite) {
                    if ($product) {
                        Mage::throwException(Mage::helper('Mage_Adminhtml_Helper_Data')
                            ->__('Chosen product does not associated with the chosen store or category.'));
                    } else {
                        Mage::throwException(Mage::helper('Mage_Adminhtml_Helper_Data')
                            ->__('Chosen category does not associated with the chosen store.'));
                    }
                } elseif ($rewrite->getId() && $rewrite->getId() != $model->getId()) {
                    $model->setTargetPath($rewrite->getRequestPath());
                    $generateTarget = false;
                }
            }
            if ($generateTarget) {
                $model->setTargetPath($catalogUrlModel->generatePath('target', $product, $category));
            }
        }
    }

    /**
     * @param Mage_Core_Model_Url_Rewrite $model
     */
    protected function _handleCmsPageUrlrewrite($model)
    {
        /** @var $cmsPage Mage_Cms_Model_Page */
        $cmsPage = Mage::registry('current_cms_page');
        if ($cmsPage && $cmsPage->getId()) {
            /** @var $cmsPageUrlrewrite Mage_Cms_Model_Page_Urlrewrite */
            $cmsPageUrlrewrite = Mage::getModel('Mage_Cms_Model_Page_Urlrewrite');
            $idPath = $cmsPageUrlrewrite->generateIdPath($cmsPage);
            $model->setIdPath($idPath);

           // if redirect specified try to find friendly URL
           $generateTarget = true;
           if ($this->_hasRedirectOptions($model)) {
               /** @var $rewriteResource Mage_Catalog_Model_Resource_Url */
               $rewriteResource = Mage::getResourceModel('Mage_Catalog_Model_Resource_Url');
               /** @var $rewrite Mage_Core_Model_Url_Rewrite */
               $rewrite = $rewriteResource->getRewriteByIdPath($idPath, $model->getStoreId());
               if (!$rewrite) {
                   Mage::throwException(Mage::helper('Mage_Adminhtml_Helper_Data')
                       ->__('Chosen cms page does not associated with the chosen store.'));
               } elseif ($rewrite->getId() && $rewrite->getId() != $model->getId()) {
                   $model->setTargetPath($rewrite->getRequestPath());
                   $generateTarget = false;
               }
           }
           if ($generateTarget) {
               $model->setTargetPath($cmsPageUrlrewrite->generateTargetPath($cmsPage));
           }
        }
    }

    /**
     * Has redirect options set
     *
     * @param Mage_Core_Model_Url_Rewrite $model
     * @return bool
     */
    protected function _hasRedirectOptions($model)
    {
        /** @var $options Mage_Core_Model_Source_Urlrewrite_Options */
        $options = Mage::getModel('Mage_Core_Model_Source_Urlrewrite_Options');
        return in_array($model->getOptions(), $options->getRedirectOptions());
    }

    /**
     * @param Mage_Core_Model_Url_Rewrite $model
     */
    protected function _handleCmsPageUrlrewriteSave($model)
    {
        /** @var $cmsPage Mage_Cms_Model_Page */
        $cmsPage = Mage::registry('current_cms_page');
        if ($cmsPage && $cmsPage->getId()) {
            /** @var $cmsRewrite Mage_Cms_Model_Page_Urlrewrite */
            $cmsRewrite = Mage::getModel('Mage_Cms_Model_Page_Urlrewrite');
            $cmsRewrite->load($model->getId(), 'url_rewrite_id');
            if (!$cmsRewrite->getId()) {
                $cmsRewrite->setUrlRewriteId($model->getId());
                $cmsRewrite->setCmsPageId($cmsPage->getId());
                $cmsRewrite->save();
            }
        }
    }

    /**
     * Urlrewrite delete action
     *
     */
    public function deleteAction()
    {
        $this->_initUrlrewriteRegistry($this->getRequest());

        if (Mage::registry('current_urlrewrite')->getId()) {
            try {
                Mage::registry('current_urlrewrite')->delete();
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(
                    Mage::helper('Mage_Adminhtml_Helper_Data')->__('The URL Rewrite has been deleted.')
                );
            } catch (Exception $e) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')
                    ->addException($e, Mage::helper('Mage_Adminhtml_Helper_Data')->__('An error occurred while deleting URL Rewrite.'));
                $this->_redirect('*/*/edit/', array('id'=>Mage::registry('current_urlrewrite')->getId()));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Check whether this contoller is allowed in admin permissions
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('catalog/urlrewrite');
    }
}
