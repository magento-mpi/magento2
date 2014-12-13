<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Controller\Guest;

use Magento\Rma\Model\Rma;

class Returns extends \Magento\Rma\Controller\Guest
{
    /**
     * View all returns
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->_objectManager->get(
            'Magento\Rma\Helper\Data'
        )->isEnabled() || !$this->_objectManager->get(
            'Magento\Sales\Helper\Guest'
        )->loadValidOrder(
            $this->_request,
            $this->_response
        )
        ) {
            $this->_forward('noroute');
            return;
        }
        $this->_view->loadLayout();
        $this->_objectManager->get('Magento\Sales\Helper\Guest')->getBreadcrumbs();
        $this->_view->renderLayout();
    }
}
