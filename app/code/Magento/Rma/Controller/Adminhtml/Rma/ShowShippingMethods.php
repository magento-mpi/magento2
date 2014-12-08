<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

class ShowShippingMethods extends \Magento\Rma\Controller\Adminhtml\Rma
{
    /**
     * Shows available shipping methods
     *
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    public function execute()
    {
        $response = false;

        try {
            $model = $this->_initModel();
            if (!$model->getId()) {
                throw new \Magento\Framework\Model\Exception(__('This is the wrong RMA ID.'));
            }
        } catch (\Magento\Framework\Model\Exception $e) {
            $response = ['error' => true, 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            $response = ['error' => true, 'message' => __('We cannot display the available shipping methods.')];
        }

        $this->_view->loadLayout();
        $response = $this->_view->getLayout()->getBlock('magento_rma_shipping_available')->toHtml();

        if (is_array($response)) {
            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($response)
            );
        } else {
            $this->getResponse()->setBody($response);
        }
    }
}
