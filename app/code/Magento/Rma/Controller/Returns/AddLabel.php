<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Returns;

use \Magento\Rma\Model\Rma;

class AddLabel extends \Magento\Rma\Controller\Returns
{
    /**
     * Add Tracking Number action
     *
     * @return void
     */
    public function execute()
    {
        if ($this->_loadValidRma()) {
            try {
                $rma = $this->_coreRegistry->registry('current_rma');

                if (!$rma->isAvailableForPrintLabel()) {
                    throw new \Magento\Framework\Model\Exception(__('Shipping Labels are not allowed.'));
                }

                $response = false;
                $number = $this->getRequest()->getPost('number');
                $number = trim(strip_tags($number));
                $carrier = $this->getRequest()->getPost('carrier');
                $carriers = $this->_objectManager->get(
                    'Magento\Rma\Helper\Data'
                )->getShippingCarriers(
                    $rma->getStoreId()
                );

                if (!isset($carriers[$carrier])) {
                    throw new \Magento\Framework\Model\Exception(__('Please select a valid carrier.'));
                }

                if (empty($number)) {
                    throw new \Magento\Framework\Model\Exception(__('Please enter a valid tracking number.'));
                }

                /** @var $rmaShipping \Magento\Rma\Model\Shipping */
                $rmaShipping = $this->_objectManager->create('Magento\Rma\Model\Shipping');
                $rmaShipping->setRmaEntityId(
                    $rma->getEntityId()
                )->setTrackNumber(
                    $number
                )->setCarrierCode(
                    $carrier
                )->setCarrierTitle(
                    $carriers[$carrier]
                )->save();
            } catch (\Magento\Framework\Model\Exception $e) {
                $response = array('error' => true, 'message' => $e->getMessage());
            } catch (\Exception $e) {
                $response = array('error' => true, 'message' => __('We cannot add a label.'));
            }
        } else {
            $response = array('error' => true, 'message' => __('The wrong RMA was selected.'));
        }
        if (is_array($response)) {
            $this->_objectManager->get('Magento\Framework\Session\Generic')->setErrorMessage($response['message']);
        }

        $this->_view->addPageLayoutHandles();
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
        return;
    }
}
