<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\TargetRule\Controller\Adminhtml\Targetrule;

class Grid extends \Magento\TargetRule\Controller\Adminhtml\Targetrule
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
