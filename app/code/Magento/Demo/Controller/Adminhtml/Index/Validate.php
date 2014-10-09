<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Demo\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Validate extends Action
{
    public function execute()
    {
        $this->getResponse()->appendBody(__('Validate action was performed successfully.'));
    }
}