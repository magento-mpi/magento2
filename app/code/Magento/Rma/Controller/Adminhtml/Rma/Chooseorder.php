<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
        $this->_title->add(__('New Return'));
        $this->_view->renderLayout();
    }
}
