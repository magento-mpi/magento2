<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Controller\Wizard;

use \Magento\Framework\App\ResponseInterface;

class ConfigPost extends \Magento\Install\Controller\Wizard
{
    /**
     * Process configuration POST data
     *
     * @return ResponseInterface|void
     */
    public function execute()
    {
        $this->_checkIfInstalled();
        $step = $this->_getWizard()->getStepByName('config');

        $config = $this->getRequest()->getPost('config');
        $connectionConfig = $this->getRequest()->getPost('connection');

        if ($config && $connectionConfig && isset($connectionConfig[$config['db_model']])) {

            $data = array_merge($config, $connectionConfig[$config['db_model']]);

            $this->_session->setConfigData(
                $data
            )->setSkipUrlValidation(
                $this->getRequest()->getPost('skip_url_validation')
            )->setSkipBaseUrlValidation(
                $this->getRequest()->getPost('skip_base_url_validation')
            );
            try {
                $this->_getInstaller()->installConfig($data);
                return $this->_redirect('*/*/installDb');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->getResponse()->setRedirect($step->getUrl());
            }
        }
        $this->getResponse()->setRedirect($step->getUrl());
    }
}
