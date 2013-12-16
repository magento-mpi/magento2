<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pci
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Encryption key changer controller
 *
 */
namespace Magento\Pci\Controller\Adminhtml\Crypt;

class Key extends \Magento\Backend\App\Action
{
    /**
     * Check whether local.xml is writeable
     *
     * @return bool
     */
    protected function _checkIsLocalXmlWriteable()
    {
        /** @var \Magento\Filesystem\Directory\Write $configDirectory */
        $configDirectory = $this->_objectManager->get('Magento\Filesystem')
            ->getDirectoryWrite(\Magento\Filesystem::CONFIG);
        if (!$configDirectory->isWritable('local.xml')) {
            $this->messageManager->addError(
                __('To enable a key change this file must be writable: %1.',
                    $configDirectory->getAbsolutePath('local.xml'))
            );
            return false;
        }
        return true;
    }

    /**
     * Render main page with form
     *
     */
    public function indexAction()
    {
        $this->_title->add(__('Encryption Key'));

        $this->_checkIsLocalXmlWriteable();
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Pci::system_crypt_key');

        if (($formBlock = $this->_view->getLayout()->getBlock('pci.crypt.key.form'))
            && $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true)) {
            /* @var \Magento\Pci\Block\Adminhtml\Crypt\Key\Form $formBlock */
            $formBlock->setFormData($data);
        }

        $this->_view->renderLayout();
    }

    /**
     * Process saving new encryption key
     *
     */
    public function saveAction()
    {
        try {
            $key = null;
            if (!$this->_checkIsLocalXmlWriteable()) {
                throw new \Exception('');
            }
            if (0 == $this->getRequest()->getPost('generate_random')) {
                $key = $this->getRequest()->getPost('crypt_key');
                if (empty($key)) {
                    throw new \Exception(__('Please enter an encryption key.'));
                }
                $this->_objectManager->get('Magento\Core\Helper\Data')->validateKey($key);
            }

            $newKey = $this->_objectManager->get('Magento\Pci\Model\Resource\Key\Change')
                ->changeEncryptionKey($key);
            $this->_objectManager->get('Magento\Backend\Model\Session')
                    ->addSuccess(
                __('The encryption key has been changed.')
            );

            if (!$key) {
                $this->messageManager->addNotice(
                    __('This is your new encryption key: <span style="font-family:monospace;">%1</span>. Be sure to write it down and take good care of it!', $newKey)
                );
            }
            $this->_objectManager->get('Magento\Core\Model\App')->cleanCache();
        }
        catch (\Exception $e) {
            if ($message = $e->getMessage()) {
                $this->messageManager->addError($e->getMessage());
            }
            $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(array('crypt_key' => $key));
        }
        $this->_redirect('adminhtml/*/');
    }

    /**
     * Check whether current administrator session allows this controller
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Pci::crypt_key');
    }
}
