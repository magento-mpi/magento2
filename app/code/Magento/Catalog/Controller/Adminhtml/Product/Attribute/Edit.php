<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product\Attribute;

class Edit extends \Magento\Catalog\Controller\Adminhtml\Product\Attribute
{
    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('attribute_id');
        /** @var $model \Magento\Catalog\Model\Resource\Eav\Attribute */
        $model = $this->_objectManager->create(
            'Magento\Catalog\Model\Resource\Eav\Attribute'
        )->setEntityTypeId(
            $this->_entityTypeId
        );
        if ($id) {
            $model->load($id);

            if (!$model->getId()) {
                $this->messageManager->addError(__('This attribute no longer exists.'));
                $this->_redirect('catalog/*/');
                return;
            }

            // entity type check
            if ($model->getEntityTypeId() != $this->_entityTypeId) {
                $this->messageManager->addError(__('This attribute cannot be edited.'));
                $this->_redirect('catalog/*/');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getAttributeData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $attributeData = $this->getRequest()->getParam('attribute');
        if (!empty($attributeData) && $id === null) {
            $model->addData($attributeData);
        }

        $this->_coreRegistry->register('entity_attribute', $model);

        $this->_initAction();

        $this->_title->add($id ? $model->getName() : __('New Product Attribute'));

        $item = $id ? __('Edit Product Attribute') : __('New Product Attribute');

        $this->_addBreadcrumb($item, $item);

        $this->_view->getLayout()->getBlock(
            'attribute_edit_js'
        )->setIsPopup(
            (bool)$this->getRequest()->getParam('popup')
        );

        $this->_view->renderLayout();
    }
}
