<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Controller\Adminhtml\Promo;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\Category;
use Magento\Registry;

class Widget extends Action
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     */
    public function __construct(Context $context, Registry $coreRegistry)
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Prepare block for chooser
     *
     * @return void
     */
    public function chooserAction()
    {
        $request = $this->getRequest();

        switch ($request->getParam('attribute')) {
            case 'sku':
                $block = $this->_view->getLayout()->createBlock(
                    'Magento\CatalogRule\Block\Adminhtml\Promo\Widget\Chooser\Sku',
                    'promo_widget_chooser_sku',
                    array('data' => array('js_form_object' => $request->getParam('form')))
                );
                break;

            case 'category_ids':
                $ids = $request->getParam('selected', array());
                if (is_array($ids)) {
                    foreach ($ids as $key => &$id) {
                        $id = (int)$id;
                        if ($id <= 0) {
                            unset($ids[$key]);
                        }
                    }

                    $ids = array_unique($ids);
                } else {
                    $ids = array();
                }


                $block = $this->_view->getLayout()->createBlock(
                    'Magento\Catalog\Block\Adminhtml\Category\Checkboxes\Tree',
                    'promo_widget_chooser_category_ids',
                    array('data' => array('js_form_object' => $request->getParam('form')))
                )->setCategoryIds(
                    $ids
                );
                break;

            default:
                $block = false;
                break;
        }

        if ($block) {
            $this->getResponse()->setBody($block->toHtml());
        }
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_CatalogRule::promo_catalog');
    }

    /**
     * Get tree node (Ajax version)
     *
     * @return void
     */
    public function categoriesJsonAction()
    {
        $categoryId = (int)$this->getRequest()->getPost('id');
        if ($categoryId) {
            $this->getRequest()->setParam('id', $categoryId);

            if (!($category = $this->_initCategory())) {
                return;
            }
            $this->getResponse()->setBody(
                $this->_view->getLayout()->createBlock(
                    'Magento\Catalog\Block\Adminhtml\Category\Tree'
                )->getTreeJson(
                    $category
                )
            );
        }
    }

    /**
     * Initialize category object in registry
     *
     * @return Category
     */
    protected function _initCategory()
    {
        $categoryId = (int)$this->getRequest()->getParam('id', false);
        $storeId = (int)$this->getRequest()->getParam('store');

        $category = $this->_objectManager->create('Magento\Catalog\Model\Category');
        $category->setStoreId($storeId);

        if ($categoryId) {
            $category->load($categoryId);
            if ($storeId) {
                $rootId = $this->_objectManager->get(
                    'Magento\Core\Model\StoreManager'
                )->getStore(
                    $storeId
                )->getRootCategoryId();
                if (!in_array($rootId, $category->getPathIds())) {
                    $this->_redirect('catalog/*/', array('_current' => true, 'id' => null));
                    return false;
                }
            }
        }

        $this->_coreRegistry->register('category', $category);
        $this->_coreRegistry->register('current_category', $category);

        return $category;
    }
}
