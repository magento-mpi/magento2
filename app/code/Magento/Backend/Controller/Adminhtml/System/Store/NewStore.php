<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System\Store;

class NewStore extends \Magento\Backend\Controller\Adminhtml\System\Store
{
    /**
     * @return void
     */
    public function execute()
    {
        if (!$this->_coreRegistry->registry('store_type')) {
            $this->_coreRegistry->register('store_type', 'store');
        }
        $this->_coreRegistry->register('store_action', 'add');
        $this->_forward('editStore');
    }
}
