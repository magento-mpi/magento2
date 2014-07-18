<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Category;

class Delete extends \Magento\Catalog\Controller\Adminhtml\Category
{
    /**
     * Delete category action
     *
     * @return void
     */
    public function execute()
    {
        $categoryId = (int)$this->getRequest()->getParam('id');
        if ($categoryId) {
            try {
                $category = $this->_objectManager->create('Magento\Catalog\Model\Category')->load($categoryId);
                $this->_eventManager->dispatch('catalog_controller_category_delete', array('category' => $category));

                $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->setDeletedPath($category->getPath());

                $category->delete();
                $this->messageManager->addSuccess(__('You deleted the category.'));
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->getResponse()->setRedirect($this->getUrl('catalog/*/edit', array('_current' => true)));
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong while trying to delete the category.'));
                $this->getResponse()->setRedirect($this->getUrl('catalog/*/edit', array('_current' => true)));
                return;
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('catalog/*/', array('_current' => true, 'id' => null)));
    }
}
