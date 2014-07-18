<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Tracking;

use \Magento\Framework\App\Action\NotFoundException;

class Package extends \Magento\Rma\Controller\Tracking
{
    /**
     * Popup package action
     * Shows package info if it's present, otherwise redirects to 404
     *
     * @return void
     * @throws NotFoundException
     */
    public function execute()
    {
        /** @var $shippingInfoModel \Magento\Rma\Model\Shipping\Info */
        $shippingInfoModel = $this->_objectManager->create('Magento\Rma\Model\Shipping\Info');
        $shippingInfoModel->loadPackage($this->getRequest()->getParam('hash'));

        $this->_coreRegistry->register('rma_package_shipping', $shippingInfoModel);
        if (!$shippingInfoModel->getPackages()) {
            throw new NotFoundException();
        }
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
