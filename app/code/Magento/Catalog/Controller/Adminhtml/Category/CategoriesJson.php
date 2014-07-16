<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Category;

class CategoriesJson extends \Magento\Catalog\Controller\Adminhtml\Category
{
    /**
     * Get tree node (Ajax version)
     *
     * @return void
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('expand_all')) {
            $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->setIsTreeWasExpanded(true);
        } else {
            $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->setIsTreeWasExpanded(false);
        }
        $categoryId = (int)$this->getRequest()->getPost('id');
        if ($categoryId) {
            $this->getRequest()->setParam('id', $categoryId);

            if (!($category = $this->_initCategory())) {
                return;
            }
            $this->getResponse()->representJson(
                $this->_view->getLayout()->createBlock(
                    'Magento\Catalog\Block\Adminhtml\Category\Tree'
                )->getTreeJson(
                    $category
                )
            );
        }
    }
}
