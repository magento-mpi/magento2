<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Create;

use Mtf\Block\Block;

/**
 * Class CustomerActivities
 * Customer's Activities block
 */
class CustomerActivities extends Block
{
    /**
     * 'Update Changes' button
     *
     * @var string
     */
    protected $updateChanges = '.action-.scalable';

    /**
     * Order sidebar reorder css selector
     *
     * @var string
     */
    protected $orderSidebar = '#order-sidebar_reorder';

    /**
     * Get last ordered items block
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Create\CustomerActivities\LastOrderedItems
     */
    public function getLastOrderedItemsBlock()
    {
        return $this->blockFactory->create(
            'Magento\Sales\Test\Block\Adminhtml\Order\Create\CustomerActivities\LastOrderedItems',
            ['element' => $this->_rootElement->find($this->orderSidebar)]
        );
    }

    /**
     * Click 'Update Changes' button
     *
     * @return void
     */
    public function updateChanges()
    {
        $this->_rootElement->find($this->updateChanges)->click();
    }
}
