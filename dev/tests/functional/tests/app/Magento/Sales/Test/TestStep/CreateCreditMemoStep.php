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
use Magento\Sales\Test\Page\Adminhtml\OrderCreditMemoNew;

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
     * @var OrderCreditMemoNew
     */
    protected $orderCreditMemoNew;

    /**
     * OrderInjectable fixture
     *
     * @var OrderInjectable
     */
    protected $order;

    /**
     * Credit memo data
     *
     * @var array|null
     */
    protected $data;

    /**
     * @construct
     * @param OrderIndex $orderIndex
     * @param OrderView $orderView
     * @param OrderInjectable $order
     * @param OrderCreditMemoNew $orderCreditMemoNew
     * @param array|null $data [optional]
     */
    public function __construct(
        OrderIndex $orderIndex,
        OrderView $orderView,
        OrderInjectable $order,
        OrderCreditMemoNew $orderCreditMemoNew,
        $data = null
    ) {
        $this->orderIndex = $orderIndex;
        $this->orderView = $orderView;
        $this->order = $order;
        $this->orderCreditMemoNew = $orderCreditMemoNew;
        $this->data = $data;
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
        if (!empty($this->data)) {
            $this->orderCreditMemoNew->getCreateBlock()->fill($this->data, $this->order->getEntityId()['products']);
            $this->orderCreditMemoNew->getCreateBlock()->updateQty();
        }
        $this->orderCreditMemoNew->getCreateBlock()->getFormBlock()->submit();

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
