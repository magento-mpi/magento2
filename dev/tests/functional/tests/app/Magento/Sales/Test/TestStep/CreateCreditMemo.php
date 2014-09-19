<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestStep;

use Mtf\TestStep\TestStepInterface;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderView;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;
use Magento\Sales\Test\Page\SalesOrderCreditMemoNew;

/**
 * Class CreateCreditMemo
 * Create credit memo from order on backend
 */
class CreateCreditMemo implements TestStepInterface
{
    /**
     * Orders Page
     *
     * @var OrderIndex
     */
    protected $orderIndex;

    /**
     * Order View Page
     *
     * @var OrderView
     */
    protected $orderView;

    /**
     * OrderCreditMemoNew Page
     *
     * @var SalesOrderCreditMemoNew
     */
    protected $orderCreditMemoNew;

    /**
     * OrderInjectable fixture
     *
     * @var OrderInjectable
     */
    protected $order;

    /**
     * @construct
     * @param OrderIndex $orderIndex
     * @param OrderView $orderView
     * @param OrderInjectable $order
     * @param SalesOrderCreditMemoNew $orderCreditMemoNew
     */
    public function __construct(
        OrderIndex $orderIndex,
        OrderView $orderView,
        OrderInjectable $order,
        SalesOrderCreditMemoNew $orderCreditMemoNew
    ) {
        $this->orderIndex = $orderIndex;
        $this->orderView = $orderView;
        $this->order = $order;
        $this->orderCreditMemoNew = $orderCreditMemoNew;
    }

    /**
     * Create credit memo from order on backend
     *
     * @return array
     */
    public function run()
    {
        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->searchAndOpen(['id' => $this->order->getId()]);
        $this->orderView->getPageActions()->orderCreditMemo();
        $this->orderCreditMemoNew->getActionsBlock()->refundOffline();
        $creditMemoId = $this->getCreditMemoId($this->order);

        return ['creditMemoId' => $creditMemoId];
    }

    /**
     * Get credit memo id
     *
     * @param OrderInjectable $order
     * @return null|string
     */
    protected function getCreditMemoId(OrderInjectable $order)
    {
        $this->orderView->getOrderForm()->openTab('creditmemos');
        $amount = $order->getPrice()['grand_invoice_total'];
        $filter = [
            'status' => 'Refunded',
            'amount_from' => $amount,
            'amount_to' => $amount
        ];
        $this->orderView->getOrderForm()->getTabElement('creditmemos')->getGridBlock()->search($filter);
        $creditMemoId = $this->orderView->getOrderForm()->getTabElement('creditmemos')->getGridBlock()
            ->getCreditMemoId();

        return $creditMemoId;
    }
}
