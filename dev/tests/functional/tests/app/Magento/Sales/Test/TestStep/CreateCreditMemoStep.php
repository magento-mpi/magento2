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
 * Class CreateCreditMemoStep
 * Create credit memo from order on backend
 */
class CreateCreditMemoStep implements TestStepInterface
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

        return ['creditMemoIds' => $this->getCreditMemoIds()];
    }

    /**
     * Get credit memo ids
     *
     * @return array
     */
    protected function getCreditMemoIds()
    {
        $this->orderView->getOrderForm()->openTab('creditmemos');
        return $this->orderView->getOrderForm()->getTabElement('creditmemos')->getGridBlock()->getIds();
    }
}
