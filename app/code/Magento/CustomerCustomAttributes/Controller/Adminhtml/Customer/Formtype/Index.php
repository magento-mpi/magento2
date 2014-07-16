<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Formtype;

class Index extends \Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Formtype
{
    /**
     * View form types grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->renderLayout();
    }
}
