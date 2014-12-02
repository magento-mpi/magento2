<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Attribute;

class Index extends \Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Attribute
{
    /**
     * Attributes grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Customer Attributes'));
        $this->_view->renderLayout();
    }
}
