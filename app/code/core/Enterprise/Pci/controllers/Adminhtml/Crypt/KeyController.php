<?php
class Enterprise_Pci_Adminhtml_Crypt_KeyController extends Mage_Adminhtml_Controller_Action
{
    protected function _checkIsLocalXmlWriteable()
    {
        $filename = Mage::getRoot() . DS . 'etc' . DS . 'local.xml';
        if (!is_writeable($filename)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('enterprise_pci')->__('To make key change possible, make sure the following file is writeable: %s', realpath($filename))
            );
            return false;
        }
        return true;
    }

    public function indexAction()
    {
        $this->_checkIsLocalXmlWriteable();
        $this->loadLayout();
        $this->_setActiveMenu('system/crypt_key');

        if (($formBlock = $this->getLayout()->getBlock('pci.crypt.key.form'))
            && $data = Mage::getSingleton('adminhtml/session')->getFormData(true)) {
            /* @var Enterprise_Pci_Block_Adminhtml_Crypt_Key_Form $formBlock */
            $formBlock->setFormData($data);
        }

        $this->renderLayout();
    }

    public function saveAction()
    {
        try {
            $key = null;
            if (!$this->_checkIsLocalXmlWriteable()) {
                throw new Exception('');
            }
            if (!$this->getRequest()->getPost('generate_random')) {
                $key = $this->getRequest()->getPost('crypt_key');
                if (empty($key)) {
                    throw new Exception(Mage::helper('enterprise_pci')->__('Enter encryption key'));
                }
                Mage::helper('core')->validateKey($key);
            }
            Mage::getResourceSingleton('enterprise_pci/key_change')->changeEncryptionKey($key);
        }
        catch (Exception $e) {
            if ($message = $e->getMessage()) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            Mage::getSingleton('adminhtml/session')->setFormData(array('crypt_key' => $key));
        }
        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/crypt_key');
    }
}
