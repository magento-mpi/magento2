<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Connect\Controller\Adminhtml\Extension\Custom;

class Edit extends \Magento\Connect\Controller\Adminhtml\Extension\Custom
{
    /**
     * Edit Extension Package Form
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Extension'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Connect::system_extensions_custom');
        $this->_view->renderLayout();
    }
}
