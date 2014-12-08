<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Controller\Adminhtml\Rule;


class Index extends \Magento\Tax\Controller\Adminhtml\Rule
{
    /**
     * @return $this
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Tax Rules'));
        $this->_view->renderLayout();
    }
}
