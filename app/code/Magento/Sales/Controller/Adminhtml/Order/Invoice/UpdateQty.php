<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order\Invoice;

use \Magento\Framework\Model\Exception;
use Magento\Backend\App\Action;

class UpdateQty extends \Magento\Sales\Controller\Adminhtml\Invoice\AbstractInvoice\View
{
    /**
     * Update items qty action
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->_title->add(__('Invoices'));
            $invoiceData = $this->getRequest()->getParam('invoice', []);
            $invoice = $this->getInvoice();
            // Save invoice comment text in current invoice object in order to display it in corresponding view
            $invoiceRawCommentText = $invoiceData['comment_text'];
            $invoice->setCommentText($invoiceRawCommentText);

            $this->_view->loadLayout();
            $response = $this->_view->getLayout()->getBlock('order_items')->toHtml();
        } catch (Exception $e) {
            $response = array('error' => true, 'message' => $e->getMessage());
        } catch (\Exception $e) {
            $response = array('error' => true, 'message' => __('Cannot update item quantity.'));
        }
        if (is_array($response)) {
            $response = $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($response);
            $this->getResponse()->representJson($response);
        } else {
            $this->getResponse()->setBody($response);
        }
    }
}
