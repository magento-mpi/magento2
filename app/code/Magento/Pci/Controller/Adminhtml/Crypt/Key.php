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

class Key extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Check whether local.xml is writeable
     *
     * @return bool
     */
    protected function _checkIsLocalXmlWriteable()
    {
        $filename = \Mage::getBaseDir('etc') . DS . 'local.xml';
        if (!is_writeable($filename)) {
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(
                __('To enable a key change this file must be writable: %1.', realpath($filename))
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
        $this->_title(__('Encryption Key'));

        $this->_checkIsLocalXmlWriteable();
        $this->loadLayout();
        $this->_setActiveMenu('Magento_Pci::system_crypt_key');

        if (($formBlock = $this->getLayout()->getBlock('pci.crypt.key.form'))
            && $data = \Mage::getSingleton('Magento\Adminhtml\Model\Session')->getFormData(true)) {
            /* @var \Magento\Pci\Block\Adminhtml\Crypt\Key\Form $formBlock */
            $formBlock->setFormData($data);
        }

        $this->renderLayout();
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
                \Mage::helper('Magento\Core\Helper\Data')->validateKey($key);
            }

            $newKey = \Mage::getResourceSingleton('\Magento\Pci\Model\Resource\Key\Change')
                ->changeEncryptionKey($key);
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')
                    ->addSuccess(
                __('The encryption key has been changed.')
            );

            if (!$key) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addNotice(__('This is your new encryption key: <span style="font-family:monospace;">%1</span>. Be sure to write it down and take good care of it!', $newKey));
            }
            \Mage::app()->cleanCache();
        }
        catch (\Exception $e) {
            if ($message = $e->getMessage()) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
            }
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setFormData(array('crypt_key' => $key));
        }
        $this->_redirect('*/*/');
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
