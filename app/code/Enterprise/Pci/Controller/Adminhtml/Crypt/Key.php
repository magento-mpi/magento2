<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pci
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Encryption key changer controller
 *
 */
class Enterprise_Pci_Controller_Adminhtml_Crypt_Key extends Magento_Adminhtml_Controller_Action
{
    /**
     * Check whether local.xml is writeable
     *
     * @return bool
     */
    protected function _checkIsLocalXmlWriteable()
    {
        $filename = Mage::getBaseDir('etc') . DS . 'local.xml';
        if (!is_writeable($filename)) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(
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
        $this->_setActiveMenu('Enterprise_Pci::system_crypt_key');

        if (($formBlock = $this->getLayout()->getBlock('pci.crypt.key.form'))
            && $data = Mage::getSingleton('Magento_Adminhtml_Model_Session')->getFormData(true)) {
            /* @var Enterprise_Pci_Block_Adminhtml_Crypt_Key_Form $formBlock */
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
                throw new Exception('');
            }
            if (0 == $this->getRequest()->getPost('generate_random')) {
                $key = $this->getRequest()->getPost('crypt_key');
                if (empty($key)) {
                    throw new Exception(__('Please enter an encryption key.'));
                }
                Mage::helper('Magento_Core_Helper_Data')->validateKey($key);
            }

            $newKey = Mage::getResourceSingleton('Enterprise_Pci_Model_Resource_Key_Change')
                ->changeEncryptionKey($key);
            Mage::getSingleton('Magento_Adminhtml_Model_Session')
                    ->addSuccess(
                __('The encryption key has been changed.')
            );

            if (!$key) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addNotice(__('This is your new encryption key: <span style="font-family:monospace;">%1</span>. Be sure to write it down and take good care of it!', $newKey));
            }
            Mage::app()->cleanCache();
        }
        catch (Exception $e) {
            if ($message = $e->getMessage()) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
            }
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->setFormData(array('crypt_key' => $key));
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
        return $this->_authorization->isAllowed('Enterprise_Pci::crypt_key');
    }
}
