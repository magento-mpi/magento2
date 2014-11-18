<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pci\Controller\Adminhtml\Crypt\Key;

class Index extends \Magento\Pci\Controller\Adminhtml\Crypt\Key
{
    /**
     * Render main page with form
     *
     * @return void
     */
    public function execute()
    {
        $this->_checkIsLocalXmlWriteable();
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Pci::system_crypt_key');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Encryption Key'));

        if (($formBlock = $this->_view->getLayout()->getBlock(
            'pci.crypt.key.form'
        )) && ($data = $this->_objectManager->get(
            'Magento\Backend\Model\Session'
        )->getFormData(
            true
        ))
        ) {
            /* @var \Magento\Pci\Block\Adminhtml\Crypt\Key\Form $formBlock */
            $formBlock->setFormData($data);
        }

        $this->_view->renderLayout();
    }
}
