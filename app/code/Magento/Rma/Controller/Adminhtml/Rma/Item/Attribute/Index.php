<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml\Rma\Item\Attribute;

class Index extends \Magento\Rma\Controller\Adminhtml\Rma\Item\Attribute
{
    /**
     * Attributes grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Returns Attributes'));
        $this->_initAction();
        $this->_view->renderLayout();
    }
}
