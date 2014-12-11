<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
