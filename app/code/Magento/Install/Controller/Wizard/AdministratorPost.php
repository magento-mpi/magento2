<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Controller\Wizard;

class AdministratorPost extends \Magento\Install\Controller\Wizard
{
    /**
     * Process administrator installation POST data
     *
     * @return void
     */
    public function execute()
    {
        $this->_checkIfInstalled();

        $step = $this->_wizard->getStepByName('administrator');
        $adminData = $this->getRequest()->getPost('admin');
        $encryptionKey = $this->getRequest()->getPost('encryption_key');

        try {
            $encryptionKey = $this->_getInstaller()->getValidEncryptionKey($encryptionKey);
            $this->_getInstaller()->createAdministrator($adminData);
            $this->_getInstaller()->installEncryptionKey($encryptionKey);
            $this->getResponse()->setRedirect($step->getNextUrl());
        } catch (\Exception $e) {
            $this->_session->setAdminData($adminData);
            if ($e instanceof \Magento\Framework\Model\Exception) {
                $this->messageManager->addMessages($e->getMessages());
            } else {
                $this->messageManager->addError($e->getMessage());
            }
            $this->getResponse()->setRedirect($step->getUrl());
        }
    }
}
