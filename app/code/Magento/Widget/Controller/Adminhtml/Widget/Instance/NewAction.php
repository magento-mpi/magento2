<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Widget\Controller\Adminhtml\Widget\Instance;

class NewAction extends \Magento\Widget\Controller\Adminhtml\Widget\Instance
{
    /**
     * New widget instance action (forward to edit action)
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
