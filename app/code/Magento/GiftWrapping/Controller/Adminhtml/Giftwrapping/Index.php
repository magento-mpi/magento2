<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping;

class Index extends \Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping
{
    /**
     * List of gift wrappings
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->renderLayout();
    }
}
