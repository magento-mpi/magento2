<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

class CreateLabel extends \Magento\Rma\Controller\Adminhtml\Rma
{
    /**
     * Create shipping label action for specific shipment
     *
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    public function execute()
    {
        $response = new \Magento\Framework\Object();
        try {
            $rmaModel = $this->_initModel();
            if ($this->labelService->createShippingLabel($rmaModel, $this->getRequest()->getPost())) {
                $rmaModel->save();
                $this->messageManager->addSuccess(__('You created a shipping label.'));
                $response->setOk(true);
            }
        } catch (\Magento\Framework\Model\Exception $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            $response->setError(true);
            $response->setMessage(__('Something went wrong creating a shipping label.'));
        }

        $this->getResponse()->representJson($response->toJson());
        return;
    }
}
