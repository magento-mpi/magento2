<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Category;

class Tree extends \Magento\Catalog\Controller\Adminhtml\Category
{
    /**
     * Tree Action
     * Retrieve category tree
     *
     * @return void
     */
    public function execute()
    {
        $storeId = (int)$this->getRequest()->getParam('store');
        $categoryId = (int)$this->getRequest()->getParam('id');

        if ($storeId) {
            if (!$categoryId) {
                $store = $this->_objectManager->get('Magento\Framework\StoreManagerInterface')->getStore($storeId);
                $rootId = $store->getRootCategoryId();
                $this->getRequest()->setParam('id', $rootId);
            }
        }

        $category = $this->_initCategory(true);

        $block = $this->_view->getLayout()->createBlock('Magento\Catalog\Block\Adminhtml\Category\Tree');
        $root = $block->getRoot();
        $this->getResponse()->representJson(
            $this->_objectManager->get(
                'Magento\Core\Helper\Data'
            )->jsonEncode(
                array(
                    'data' => $block->getTree(),
                    'parameters' => array(
                        'text' => $block->buildNodeName($root),
                        'draggable' => false,
                        'allowDrop' => (bool)$root->getIsVisible(),
                        'id' => (int)$root->getId(),
                        'expanded' => (int)$block->getIsWasExpanded(),
                        'store_id' => (int)$block->getStore()->getId(),
                        'category_id' => (int)$category->getId(),
                        'root_visible' => (int)$root->getIsVisible()
                    )
                )
            )
        );
    }
}
