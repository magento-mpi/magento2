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
        $this->_title->add(__('Frontend Apps'));

        $this->_initAction();
        $this->_view->renderLayout();
    }
}
