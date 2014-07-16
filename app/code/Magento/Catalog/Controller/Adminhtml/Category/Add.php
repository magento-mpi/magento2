<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Category;

class Add extends \Magento\Catalog\Controller\Adminhtml\Category
{
    /**
     * Add new category form
     *
     * @return void
     */
    public function execute()
    {
        $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->unsActiveTabId();
        $this->_forward('edit');
    }
}
