<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerSegment\Test\Block\Adminhtml\Customersegment\Edit\Tab;

use Magento\Backend\Test\Block\Widget\Tab;
use Mtf\Client\Element;

/**
 * Class MatchedCustomers
 * Matched customers tab
 */
class MatchedCustomers extends Tab
{
    /**
     * Customer grid mapping
     *
     * @var string
     */
    protected $gridPath = '#segmentGrid';

    /**
     * Get Customer Segment edit form
     *
     * @return \Magento\CustomerSegment\Test\Block\Adminhtml\Report\Customer\Segment\DetailGrid
     */
    public function getCustomersGrid()
    {
        return $this->blockFactory->create(
            'Magento\CustomerSegment\Test\Block\Adminhtml\Report\Customer\Segment\DetailGrid',
            ['element' => $this->_rootElement->find($this->gridPath)]
        );
    }
}
