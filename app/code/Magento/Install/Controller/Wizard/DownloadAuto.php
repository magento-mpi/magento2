<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Controller\Wizard;

class DownloadAuto extends \Magento\Install\Controller\Wizard
{
    /**
     * Download auto action
     *
     * @return void
     */
    public function execute()
    {
        $step = $this->_getWizard()->getStepByName('download');
        $this->getResponse()->setRedirect($step->getNextUrl());
    }
}
