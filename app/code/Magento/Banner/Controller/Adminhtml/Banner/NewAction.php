<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Banner\Controller\Adminhtml\Banner;

class NewAction extends \Magento\Banner\Controller\Adminhtml\Banner
{
    /**
     * Create new banner
     *
     * @return void
     */
    public function execute()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }
}
