<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping;

class Edit extends \Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping
{
    /**
     * Edit gift wrapping
     *
     * @return void
     */
    public function execute()
    {
        $model = $this->_initModel();
        $this->_initAction();
        $formData = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData();
        if ($formData) {
            $model->addData($formData);
        }
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('%1', $model->getDesign()));
        $this->_view->renderLayout();
    }
}
