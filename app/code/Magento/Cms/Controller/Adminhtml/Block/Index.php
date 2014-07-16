<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Controller\Adminhtml\Block;

class Index extends \Magento\Cms\Controller\Adminhtml\Block
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Blocks'));

        $this->_initAction();
        $this->_view->renderLayout();
    }
}
