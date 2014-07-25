<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Address\Attribute;

class Index extends \Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Address\Attribute
{
    /**
     * Attributes grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Customer Address Attributes'));
        $this->_initAction();
        $this->_view->renderLayout();
    }
}
