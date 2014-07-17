<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Email\Controller\Adminhtml\Email\Template;

class NewAction extends \Magento\Email\Controller\Adminhtml\Email\Template
{
    /**
     * New transactional email action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
