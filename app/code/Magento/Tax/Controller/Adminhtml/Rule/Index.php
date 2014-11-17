<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Controller\Adminhtml\Rule;

use \Magento\Backend\App\Action;

class Index extends \Magento\Tax\Controller\Adminhtml\Rule
{
    /**
     * @return $this
     */
    public function execute()
    {
        $this->_title->add(__('Tax Rules'));
        $this->_initAction();
        $this->_view->renderLayout();
    }
}
