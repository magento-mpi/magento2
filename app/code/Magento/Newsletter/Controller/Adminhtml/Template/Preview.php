<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Controller\Adminhtml\Template;

class Preview extends \Magento\Newsletter\Controller\Adminhtml\Template
{
    /**
     * Preview Newsletter template
     *
     * @return void|$this
     */
    public function execute()
    {
        $this->_view->loadLayout();

        $data = $this->getRequest()->getParams();
        if (empty($data) || !isset($data['id'])) {
            $this->_forward('noroute');
            return $this;
        }

        // set default value for selected store
        $data['preview_store_id'] = $this->_objectManager->get(
            'Magento\Store\Model\StoreManager'
        )->getDefaultStoreView()->getId();
        $this->_view->getLayout()->getBlock('preview_form')->setFormData($data);
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Newsletter Templates'));
        $this->_view->renderLayout();
    }
}
