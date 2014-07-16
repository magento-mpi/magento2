<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Shipment\AbstractShipment;

abstract class Index extends \Magento\Backend\App\Action
{
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::shipment');
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Magento_Sales::sales_shipment'
        )->_addBreadcrumb(
            __('Sales'),
            __('Sales')
        )->_addBreadcrumb(
            __('Shipments'),
            __('Shipments')
        );
        return $this;
    }

    /**
     * Shipments grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Shipments'));

        $this->_initAction();
        $this->_view->renderLayout();
    }
}
