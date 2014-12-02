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
        $this->_title->add(__('Encryption Key'));

        /** @var \Magento\Framework\App\DeploymentConfig\Writer $writer */
        $writer = $this->_objectManager->get('Magento\Framework\App\DeploymentConfig\Writer');
        if (!$writer->checkIfWritable()) {
            $this->messageManager->addError(__('Deployment configuration file is not writable.'));
        }

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Pci::system_crypt_key');

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
