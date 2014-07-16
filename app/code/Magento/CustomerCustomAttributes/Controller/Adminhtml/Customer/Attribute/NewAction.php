<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Attribute;

class NewAction extends \Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Attribute
{
    /**
     * Create new attribute action
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->addActionLayoutHandles();
        $this->_forward('edit');
    }
}
