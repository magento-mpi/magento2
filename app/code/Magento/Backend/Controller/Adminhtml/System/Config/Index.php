<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System\Config;

class Index extends \Magento\Backend\Controller\Adminhtml\System\AbstractConfig
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
