<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Demo\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Save extends Action
{
    public function execute()
    {
        $this->messageManager->addSuccess(__('Save action was performed successfully.'));
        $this->_redirect('*/*');
    }
}