<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

class Chooseorder extends \Magento\Rma\Controller\Adminhtml\Rma
{
    /**
     * Choose Order action during new RMA creation
     *
     * @return void
     */
    public function execute()
    {
        $this->_initCreateModel();
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('New Return'));
        $this->_view->renderLayout();
    }
}
