<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Widget\Controller\Adminhtml\Widget\Instance;

class Index extends \Magento\Widget\Controller\Adminhtml\Widget\Instance
{
    /**
     * Widget Instances Grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Frontend Apps'));
        $this->_view->renderLayout();
    }
}
