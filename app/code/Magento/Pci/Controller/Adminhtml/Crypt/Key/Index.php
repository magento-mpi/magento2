<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
        /** @var \Magento\Framework\App\DeploymentConfig\Writer $writer */
        $writer = $this->_objectManager->get('Magento\Framework\App\DeploymentConfig\Writer');
        if (!$writer->checkIfWritable()) {
            $this->messageManager->addError(__('Deployment configuration file is not writable.'));
        }

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
