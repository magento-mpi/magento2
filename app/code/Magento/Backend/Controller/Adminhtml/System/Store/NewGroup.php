<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System\Store;

class NewGroup extends \Magento\Backend\Controller\Adminhtml\System\Store
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_coreRegistry->register('store_type', 'group');
        $this->_forward('newStore');
    }
}
