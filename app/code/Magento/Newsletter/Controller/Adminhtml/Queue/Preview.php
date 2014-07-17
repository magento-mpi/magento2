<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Controller\Adminhtml\Queue;

class Preview extends \Magento\Newsletter\Controller\Adminhtml\Queue
{
    /**
     * Preview Newsletter queue template
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $data = $this->getRequest()->getParams();
        if (empty($data) || !isset($data['id'])) {
            $this->_forward('noroute');
            return;
        }

        // set default value for selected store
        $data['preview_store_id'] = $this->_objectManager->get(
            'Magento\Store\Model\StoreManager'
        )->getDefaultStoreView()->getId();

        $this->_view->getLayout()->getBlock('preview_form')->setFormData($data);
        $this->_view->renderLayout();
    }
}
