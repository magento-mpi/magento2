<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Status;

use Magento\Backend\Test\Block\GridPageActions as ParentPageActions;

/**
 * Class GridPageActions
 * Grid page actions block on OrderStatus index page
 */
class GridPageActions extends ParentPageActions
{
    /**
     * "Assign Status To state" button
     *
     * @var string
     */
    protected $assignButton = '#assign';

    /**
     * Click on "Assign Status To State" button
     *
     * @return void
     */
    public function assignStatusToState()
    {
        $this->_rootElement->find($this->assignButton)->click();
    }
}
