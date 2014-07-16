<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Theme\Controller\Adminhtml\System\Design\Theme;

class Grid extends \Magento\Theme\Controller\Adminhtml\System\Design\Theme
{
    /**
     * Grid ajax action
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
