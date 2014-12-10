<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\TargetRule\Controller\Adminhtml\Targetrule;

class Index extends \Magento\TargetRule\Controller\Adminhtml\Targetrule
{
    /**
     * Index grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Related Products Rules'));
        $this->_view->renderLayout();
    }
}
