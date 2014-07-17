<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Connect\Controller\Adminhtml\Extension\Custom;

class Reset extends \Magento\Connect\Controller\Adminhtml\Extension\Custom
{
    /**
     * Reset Extension Package form data
     *
     * @return void
     */
    public function execute()
    {
        $this->_objectManager->get('Magento\Connect\Model\Session')->unsCustomExtensionPackageFormData();
        $this->_redirect('adminhtml/*/edit');
    }
}
