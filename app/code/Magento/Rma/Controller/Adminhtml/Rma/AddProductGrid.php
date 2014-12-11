<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

class AddProductGrid extends \Magento\Rma\Controller\Adminhtml\Rma
{
    /**
     * Generate RMA items grid for ajax request from selecting product grid during RMA creation
     *
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    public function execute()
    {
        try {
            $this->_initModel();
            $order = $this->_coreRegistry->registry('current_order');
            if (!$order) {
                throw new \Magento\Framework\Model\Exception(__('Invalid order'));
            }
            $this->_view->loadLayout();
            $response = $this->_view->getLayout()->getBlock('add_product_grid')->toHtml();
        } catch (\Magento\Framework\Model\Exception $e) {
            $response = ['error' => true, 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            $response = ['error' => true, 'message' => __('Something went wrong retrieving the product list.')];
        }
        if (is_array($response)) {
            $response = $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($response);
            $this->getResponse()->representJson($response);
        } else {
            $this->getResponse()->setBody($response);
        }
    }
}
