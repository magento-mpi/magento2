<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Controller\Wizard;

class LocalePost extends \Magento\Install\Controller\Wizard
{
    /**
     * Saving localization settings
     *
     * @return void
     */
    public function execute()
    {
        $this->_checkIfInstalled();
        $step = $this->_getWizard()->getStepByName('locale');

        $data = $this->getRequest()->getPost('config');
        if ($data) {
            $this->_session->setLocaleData($data);
        }

        $this->getResponse()->setRedirect($step->getNextUrl());
    }
}
