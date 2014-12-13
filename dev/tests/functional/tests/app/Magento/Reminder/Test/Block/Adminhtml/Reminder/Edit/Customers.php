<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Reminder\Test\Block\Adminhtml\Reminder\Edit;

use Magento\Reminder\Test\Block\Adminhtml\Reminder\Edit\Customers\CustomersGrid;

/**
 * "Matched Customers" tab.
 */
class Customers extends \Magento\Backend\Test\Block\Widget\Tab
{
    /**
     * Locator for customers grid.
     *
     * @var string
     */
    protected $customersGrid = '#Matched_Customers';

    /**
     * Get customers grid.
     *
     * @return CustomersGrid
     */
    public function getCusromersGrid()
    {
        return $this->blockFactory->create(
            '\Magento\Reminder\Test\Block\Adminhtml\Reminder\Edit\Customers\CustomersGrid',
            ['element' => $this->_rootElement->find($this->customersGrid)]
        );
    }
}
