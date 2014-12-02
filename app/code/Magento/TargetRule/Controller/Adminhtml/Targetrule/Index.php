<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
