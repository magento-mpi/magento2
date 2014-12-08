<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Guest;

use Magento\Rma\Model\Rma;

class DelLabel extends \Magento\Rma\Controller\Guest
{
    /**
     * Delete Tracking Number action
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
                $number = intval($this->getRequest()->getPost('number'));

                if (empty($number)) {
                    throw new \Magento\Framework\Model\Exception(__('Please enter a valid tracking number.'));
                }
                /** @var $trackingNumber \Magento\Rma\Model\Shipping */
                $trackingNumber = $this->_objectManager->create('Magento\Rma\Model\Shipping')->load($number);
                if ($trackingNumber->getRmaEntityId() !== $rma->getId()) {
                    throw new \Magento\Framework\Model\Exception(__('The wrong RMA was selected.'));
                }
                $trackingNumber->delete();
            } catch (\Magento\Framework\Model\Exception $e) {
                $response = ['error' => true, 'message' => $e->getMessage()];
            } catch (\Exception $e) {
                $response = ['error' => true, 'message' => __('We cannot delete the label.')];
            }
        } else {
            $response = ['error' => true, 'message' => __('The wrong RMA was selected.')];
        }
        if (is_array($response)) {
            $this->_objectManager->get('Magento\Framework\Session\Generic')->setErrorMessage($response['message']);
        }

        $this->_view->addPageLayoutHandles();
        $this->_view->loadLayout(false)->renderLayout();
        return;
    }
}
