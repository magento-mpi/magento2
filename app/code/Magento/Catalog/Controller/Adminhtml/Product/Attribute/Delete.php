<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product\Attribute;

class Delete extends \Magento\Catalog\Controller\Adminhtml\Product\Attribute
{
    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('attribute_id');
        if ($id) {
            $model = $this->_objectManager->create('Magento\Catalog\Model\Resource\Eav\Attribute');

            // entity type check
            $model->load($id);
            if ($model->getEntityTypeId() != $this->_entityTypeId) {
                $this->messageManager->addError(__('This attribute cannot be deleted.'));
                $this->_redirect('catalog/*/');
                return;
            }

            try {
                $model->delete();
                $this->messageManager->addSuccess(__('The product attribute has been deleted.'));
                $this->_redirect('catalog/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect(
                    'catalog/*/edit',
                    array('attribute_id' => $this->getRequest()->getParam('attribute_id'))
                );
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find an attribute to delete.'));
        $this->_redirect('catalog/*/');
    }
}
