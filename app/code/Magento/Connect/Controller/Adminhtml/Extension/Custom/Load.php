<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Connect\Controller\Adminhtml\Extension\Custom;

class Load extends \Magento\Connect\Controller\Adminhtml\Extension\Custom
{
    /**
     * Load Local Extension Package
     *
     * @return void
     */
    public function execute()
    {
        $packageName = base64_decode(strtr($this->getRequest()->getParam('id'), '-_,', '+/='));
        if ($packageName) {
            $session = $this->_objectManager->get('Magento\Connect\Model\Session');
            try {
                $data = $this->_objectManager->get('Magento\Connect\Helper\Data')->loadLocalPackage($packageName);
                if (!$data) {
                    throw new \Magento\Framework\Model\Exception(__('Something went wrong loading the package data.'));
                }
                $data = array_merge($data, array('file_name' => $packageName));
                $session->setCustomExtensionPackageFormData($data);
                $this->messageManager->addSuccess(__('The package %1 data has been loaded.', $packageName));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('adminhtml/*/edit');
    }
}
