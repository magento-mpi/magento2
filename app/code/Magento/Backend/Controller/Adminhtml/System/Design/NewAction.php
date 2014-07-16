<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System\Design;

class NewAction extends \Magento\Backend\Controller\Adminhtml\System\Design
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
