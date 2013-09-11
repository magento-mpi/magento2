<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Credit memo API
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Order\Creditmemo;

class Api extends \Magento\Sales\Model\Api\Resource
{

    /**
     * Initialize attributes mapping
     */
    public function __construct(\Magento\Api\Helper\Data $apiHelper)
    {
        parent::__construct($apiHelper);
        $this->_attributesMap = array(
            'creditmemo' => array('creditmemo_id' => 'entity_id'),
            'creditmemo_item' => array('item_id' => 'entity_id'),
            'creditmemo_comment' => array('comment_id' => 'entity_id')
        );
    }

    /**
     * Retrieve credit memos list. Filtration could be applied
     *
     * @param null|object|array $filters
     * @return array
     */
    public function items($filters = null)
    {
        $creditmemos = array();
        /** @var $apiHelper \Magento\Api\Helper\Data */
        $apiHelper = \Mage::helper('Magento\Api\Helper\Data');
        $filters = $apiHelper->parseFilters($filters, $this->_attributesMap['creditmemo']);
        /** @var $creditmemoModel \Magento\Sales\Model\Order\Creditmemo */
        $creditmemoModel = \Mage::getModel('Magento\Sales\Model\Order\Creditmemo');
        try {
            $creditMemoCollection = $creditmemoModel->getFilteredCollectionItems($filters);
            foreach ($creditMemoCollection as $creditmemo) {
                $creditmemos[] = $this->_getAttributes($creditmemo, 'creditmemo');
            }
        } catch (\Exception $e) {
            $this->_fault('invalid_filter', $e->getMessage());
        }
        return $creditmemos;
    }

    /**
     * Retrieve credit memo information
     *
     * @param string $creditmemoIncrementId
     * @return array
     */
    public function info($creditmemoIncrementId)
    {
        $creditmemo = $this->_getCreditmemo($creditmemoIncrementId);
        // get credit memo attributes with entity_id' => 'creditmemo_id' mapping
        $result = $this->_getAttributes($creditmemo, 'creditmemo');
        $result['order_increment_id'] = $creditmemo->getOrder()->load($creditmemo->getOrderId())->getIncrementId();
        // items refunded
        $result['items'] = array();
        foreach ($creditmemo->getAllItems() as $item) {
            $result['items'][] = $this->_getAttributes($item, 'creditmemo_item');
        }
        // credit memo comments
        $result['comments'] = array();
        foreach ($creditmemo->getCommentsCollection() as $comment) {
            $result['comments'][] = $this->_getAttributes($comment, 'creditmemo_comment');
        }

        return $result;
    }

    /**
     * Create new credit memo for order
     *
     * @param string $orderIncrementId
     * @param array $creditmemoData array('qtys' => array('sku1' => qty1, ... , 'skuN' => qtyN),
     *      'shipping_amount' => value, 'adjustment_positive' => value, 'adjustment_negative' => value)
     * @param string|null $comment
     * @param bool $notifyCustomer
     * @param bool $includeComment
     * @param string $refundToStoreCreditAmount
     * @return string $creditmemoIncrementId
     */
    public function create($orderIncrementId, $creditmemoData = null, $comment = null, $notifyCustomer = false,
        $includeComment = false, $refundToStoreCreditAmount = null)
    {
        /** @var $order \Magento\Sales\Model\Order */
        $order = \Mage::getModel('Magento\Sales\Model\Order')->load($orderIncrementId, 'increment_id');
        if (!$order->getId()) {
            $this->_fault('order_not_exists');
        }
        if (!$order->canCreditmemo()) {
            $this->_fault('cannot_create_creditmemo');
        }
        $creditmemoData = $this->_prepareCreateData($creditmemoData);

        /** @var $service \Magento\Sales\Model\Service\Order */
        $service = \Mage::getModel('Magento\Sales\Model\Service\Order', array('order' => $order));
        /** @var $creditmemo \Magento\Sales\Model\Order\Creditmemo */
        $creditmemo = $service->prepareCreditmemo($creditmemoData);

        // refund to Store Credit
        if ($refundToStoreCreditAmount) {
            // check if refund to Store Credit is available
            if ($order->getCustomerIsGuest()) {
                $this->_fault('cannot_refund_to_storecredit');
            }
            $refundToStoreCreditAmount = max(
                0,
                min($creditmemo->getBaseCustomerBalanceReturnMax(), $refundToStoreCreditAmount)
            );
            if ($refundToStoreCreditAmount) {
                $refundToStoreCreditAmount = $creditmemo->getStore()->roundPrice($refundToStoreCreditAmount);
                $creditmemo->setBaseCustomerBalanceTotalRefunded($refundToStoreCreditAmount);
                $refundToStoreCreditAmount = $creditmemo->getStore()->roundPrice(
                    $refundToStoreCreditAmount*$order->getBaseToOrderRate()
                );
                // this field can be used by customer balance observer
                $creditmemo->setBsCustomerBalTotalRefunded($refundToStoreCreditAmount);
                // setting flag to make actual refund to customer balance after credit memo save
                $creditmemo->setCustomerBalanceRefundFlag(true);
            }
        }
        $creditmemo->setPaymentRefundDisallowed(true)->register();
        // add comment to creditmemo
        if (!empty($comment)) {
            $creditmemo->addComment($comment, $notifyCustomer);
        }
        try {
            \Mage::getModel('Magento\Core\Model\Resource\Transaction')
                ->addObject($creditmemo)
                ->addObject($order)
                ->save();
            // send email notification
            $creditmemo->sendEmail($notifyCustomer, ($includeComment ? $comment : ''));
        } catch (\Magento\Core\Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return $creditmemo->getIncrementId();
    }

    /**
     * Add comment to credit memo
     *
     * @param string $creditmemoIncrementId
     * @param string $comment
     * @param boolean $notifyCustomer
     * @param boolean $includeComment
     * @return boolean
     */
    public function addComment($creditmemoIncrementId, $comment, $notifyCustomer = false, $includeComment = false)
    {
        $creditmemo = $this->_getCreditmemo($creditmemoIncrementId);
        try {
            $creditmemo->addComment($comment, $notifyCustomer)->save();
            $creditmemo->sendUpdateEmail($notifyCustomer, ($includeComment ? $comment : ''));
        } catch (\Magento\Core\Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }

    /**
     * Cancel credit memo
     *
     * @param string $creditmemoIncrementId
     * @return boolean
     */
    public function cancel($creditmemoIncrementId)
    {
        $creditmemo = $this->_getCreditmemo($creditmemoIncrementId);

        if (!$creditmemo->canCancel()) {
            $this->_fault('status_not_changed', __('We can\'t cancel the credit memo'));
        }
        try {
            $creditmemo->cancel()->save();
        } catch (\Exception $e) {
            $this->_fault('status_not_changed', __('Something went wrong while canceling the credit memo.'));
        }

        return true;
    }

    /**
     * Hook method, could be replaced in derived classes
     *
     * @param  array $data
     * @return array
     */
    protected function _prepareCreateData($data)
    {
        $data = isset($data) ? $data : array();

        if (isset($data['qtys']) && count($data['qtys'])) {
            $qtysArray = array();
            foreach ($data['qtys'] as $qKey => $qVal) {
                // Save backward compatibility
                if (is_array($qVal)) {
                    if (isset($qVal['order_item_id']) && isset($qVal['qty'])) {
                        $qtysArray[$qVal['order_item_id']] = $qVal['qty'];
                    }
                } else {
                    $qtysArray[$qKey] = $qVal;
                }
            }
            $data['qtys'] = $qtysArray;
        }
        return $data;
    }

    /**
     * Load CreditMemo by IncrementId
     *
     * @param mixed $incrementId
     * @return \Magento\Core\Model\AbstractModel|\Magento\Sales\Model\Order\Creditmemo
     */
    protected function _getCreditmemo($incrementId)
    {
        /** @var $creditmemo \Magento\Sales\Model\Order\Creditmemo */
        $creditmemo = \Mage::getModel('Magento\Sales\Model\Order\Creditmemo')->load($incrementId, 'increment_id');
        if (!$creditmemo->getId()) {
            $this->_fault('not_exists');
        }
        return $creditmemo;
    }

}
