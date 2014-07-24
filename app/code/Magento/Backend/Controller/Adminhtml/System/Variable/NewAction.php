<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System\Variable;

class NewAction extends \Magento\Backend\Controller\Adminhtml\System\Variable
{
    /**
     * New Action (forward to edit action)
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
