<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Controller\Wizard;

class DownloadManual extends \Magento\Install\Controller\Wizard
{
    /**
     * Download manual action
     *
     * @return void
     */
    public function execute()
    {
        $step = $this->_getWizard()->getStepByName('download');
        $this->getResponse()->setRedirect($step->getNextUrl());
    }
}
