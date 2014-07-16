<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backup\Controller\Adminhtml\Index;

class Grid extends \Magento\Backup\Controller\Adminhtml\Index
{
    /**
     * Backup list action
     *
     * @return void
     */
    public function execute()
    {
        $this->renderLayot(false);
        $this->_view->renderLayout();
    }
}
