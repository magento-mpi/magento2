<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Category;

class Edit extends \Magento\Catalog\Controller\Adminhtml\Category
{
    /**
     * Edit category page
     *
     * @return void
     */
    public function execute()
    {
        $storeId = (int)$this->getRequest()->getParam('store');
        $parentId = (int)$this->getRequest()->getParam('parent');
        $categoryId = (int)$this->getRequest()->getParam('id');

        if ($storeId && !$categoryId && !$parentId) {
            $store = $this->_objectManager->get('Magento\Framework\StoreManagerInterface')->getStore($storeId);
            $this->getRequest()->setParam('id', (int)$store->getRootCategoryId());
        }

        $category = $this->_initCategory(true);
        if (!$category) {
            return;
        }

        $this->_title->add($categoryId ? $category->getName() : __('Categories'));

        /**
         * Check if we have data in session (if during category save was exception)
         */
        $data = $this->_getSession()->getCategoryData(true);
        if (isset($data['general'])) {
            $category->addData($data['general']);
        }

        /**
         * Build response for ajax request
         */
        if ($this->getRequest()->getQuery('isAjax')) {
            // prepare breadcrumbs of selected category, if any
            $breadcrumbsPath = $category->getPath();
            if (empty($breadcrumbsPath)) {
                // but if no category, and it is deleted - prepare breadcrumbs from path, saved in session
                $breadcrumbsPath = $this->_objectManager->get(
                    'Magento\Backend\Model\Auth\Session'
                )->getDeletedPath(
                    true
                );
                if (!empty($breadcrumbsPath)) {
                    $breadcrumbsPath = explode('/', $breadcrumbsPath);
                    // no need to get parent breadcrumbs if deleting category level 1
                    if (count($breadcrumbsPath) <= 1) {
                        $breadcrumbsPath = '';
                    } else {
                        array_pop($breadcrumbsPath);
                        $breadcrumbsPath = implode('/', $breadcrumbsPath);
                    }
                }
            }

            $this->_view->loadLayout();

            $eventResponse = new \Magento\Framework\Object(
                array(
                    'content' => $this->_view->getLayout()->getBlock(
                        'category.edit'
                    )->getFormHtml() . $this->_view->getLayout()->getBlock(
                        'category.tree'
                    )->getBreadcrumbsJavascript(
                        $breadcrumbsPath,
                        'editingCategoryBreadcrumbs'
                    ),
                    'messages' => $this->_view->getLayout()->getMessagesBlock()->getGroupedHtml()
                )
            );
            $this->_eventManager->dispatch(
                'category_prepare_ajax_response',
                array('response' => $eventResponse, 'controller' => $this)
            );
            $this->getResponse()->setHeader('Content-type', 'application/json', true);
            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($eventResponse->getData())
            );
            return;
        }

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Catalog::catalog_categories');
        $this->_view->getLayout()->getBlock('head')->setCanLoadExtJs(true)->setContainerCssClass('catalog-categories');

        $this->_addBreadcrumb(__('Manage Catalog Categories'), __('Manage Categories'));

        $block = $this->_view->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($storeId);
        }

        $this->_view->renderLayout();
    }
}
