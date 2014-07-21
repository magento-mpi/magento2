<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Connect\Controller\Adminhtml\Extension\Custom;

class Loadtab extends \Magento\Connect\Controller\Adminhtml\Extension\Custom
{
    /**
     * Load Grid with Local Packages
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
