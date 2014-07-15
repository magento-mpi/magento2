<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Connect\Controller\Adminhtml\Extension\Custom;

class Index extends \Magento\Connect\Controller\Adminhtml\Extension\Custom
{
    /**
     * Redirect to edit Extension Package action
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Package Extensions'));

        $this->_forward('edit');
    }
}
