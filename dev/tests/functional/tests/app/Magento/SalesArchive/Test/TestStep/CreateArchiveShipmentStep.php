<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Test\TestStep;

use Magento\Sales\Test\TestStep\CreateShipmentStep;
use Mtf\TestStep\TestStepInterface;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\SalesArchive\Test\Page\Adminhtml\ArchiveOrders;

/**
 * Class CreateArchiveShipmentStep
 * Create shipment from archived order on backend
 */
class CreateArchiveShipmentStep extends CreateShipmentStep implements TestStepInterface
{
    /**
     * Orders Page
     *
     * @var ArchiveOrders
     */
    protected $archiveOrders;

    /**
     * @construct
     * @param ArchiveOrders $archiveOrders
     */
    public function __construct(
        ArchiveOrders $archiveOrders
    ) {
        $this->archiveOrders = $archiveOrders;
    }

    /**
     * Create shipping for archive order on backend
     *
     * @return array
     */
    public function run()
    {
        $this->archiveOrders->open();
        $this->archiveOrders->getSalesOrderGrid()->searchAndOpen(['id' => $this->order->getId()]);
        $this->orderView->getPageActions()->ship();
        if (!empty($this->data)) {
            $this->orderShipmentNew->getCreateBlock()->fill($this->data, $this->order->getEntityId()['products']);
        }
        $this->orderShipmentNew->getShipItemsBlock()->submit();
        if (!empty($this->data)) {
            $successMessage = $this->orderView->getMessagesBlock()->getSuccessMessages();
        }

        return [
            'shipmentIds' => $this->getShipmentIds(),
            'successMessage' => isset($successMessage) ? $successMessage : null
        ];
    }
}
