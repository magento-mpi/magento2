<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Guest;

use \Magento\Rma\Model\Rma;

class View extends \Magento\Rma\Controller\Guest
{
    /**
     * View concrete rma
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->_loadValidRma()) {
            $this->_redirect('*/*/returns');
            return;
        }

        $this->_view->loadLayout();
        $this->_objectManager->get('Magento\Sales\Helper\Guest')->getBreadcrumbs();
        $this->_view->getPage()->getConfig()->setTitle(
            __('Return #%1', $this->_coreRegistry->registry('current_rma')->getIncrementId())
        );
        $this->_view->renderLayout();
    }
}
