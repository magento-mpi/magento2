<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Connect\Controller\Adminhtml\Extension\Custom;

class Create extends \Magento\Connect\Controller\Adminhtml\Extension\Custom
{
    /**
     * Create new Extension Package
     *
     * @return void
     */
    public function execute()
    {
        $session = $this->_objectManager->get('Magento\Connect\Model\Session');
        try {
            $post = $this->getRequest()->getPost();
            $session->setCustomExtensionPackageFormData($post);
            $ext = $this->_objectManager->create('Magento\Connect\Model\Extension');
            $ext->setData($post);
            $packageVersion = $this->getRequest()->getPost('version_ids');
            if (is_array($packageVersion)) {
                if (in_array(\Magento\Framework\Connect\Package::PACKAGE_VERSION_2X, $packageVersion)) {
                    $ext->createPackage();
                }
                if (in_array(\Magento\Framework\Connect\Package::PACKAGE_VERSION_1X, $packageVersion)) {
                    $ext->createPackageV1x();
                }
            }
            $this->_redirect('adminhtml/*');
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('adminhtml/*');
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong creating the package.'));
            $this->_redirect('adminhtml/*');
        }
    }
}
