<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Connect\Controller\Adminhtml\Extension\Custom;

class Save extends \Magento\Connect\Controller\Adminhtml\Extension\Custom
{
    /**
     * Save Extension Package
     *
     * @return void
     */
    public function execute()
    {
        $session = $this->_objectManager->get('Magento\Connect\Model\Session');
        $p = $this->getRequest()->getPost();

        if (!empty($p['_create'])) {
            $create = true;
            unset($p['_create']);
        }

        if ($p['file_name'] == '') {
            $p['file_name'] = $p['name'];
        }

        $session->setCustomExtensionPackageFormData($p);
        try {
            $ext = $this->_objectManager->create('Magento\Connect\Model\Extension');
            /** @var $ext \Magento\Connect\Model\Extension */
            $ext->setData($p);
            if ($ext->savePackage()) {
                $this->messageManager->addSuccess(__('The package data has been saved.'));
            } else {
                $this->messageManager->addError(__('Something went wrong saving the package data.'));
                $this->_redirect('adminhtml/*/edit');
            }
            if (empty($create)) {
                $this->_redirect('adminhtml/*/edit');
            } else {
                $this->_forward('create');
            }
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('adminhtml/*');
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong saving the package.'));
            $this->_redirect('adminhtml/*');
        }
    }
}
