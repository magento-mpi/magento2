<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Controller\Adminhtml\Rule;

use \Magento\Backend\App\Action;

class NewAction extends \Magento\Tax\Controller\Adminhtml\Rule
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
