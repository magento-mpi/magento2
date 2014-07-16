<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute;

class Edit extends \Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute
{
    /**
     * @return void
     */
    public function execute()
    {
        if (!$this->_validateProducts()) {
            return;
        }

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
