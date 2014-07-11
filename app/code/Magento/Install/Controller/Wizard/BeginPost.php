<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Controller\Wizard;

class BeginPost extends \Magento\Install\Controller\Wizard
{
    /**
     * Process begin step POST data
     *
     * @return void
     */
    public function execute()
    {
        $this->_checkIfInstalled();

        $agree = $this->getRequest()->getPost('agree');
        if ($agree && ($step = $this->_getWizard()->getStepByName('begin'))) {
            $this->getResponse()->setRedirect($step->getNextUrl());
        } else {
            $this->_redirect('install');
        }
    }
}
