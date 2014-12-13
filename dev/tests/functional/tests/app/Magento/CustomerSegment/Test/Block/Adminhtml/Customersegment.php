<?php
/**
 * @spi
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerSegment\Test\Block\Adminhtml;

use Mtf\Block\Block;

/**
 * Class Customersegment
 * Customer Segment actions block
 */
class Customersegment extends Block
{
    /**
     * Click save and continue button on form
     *
     * @return void
     */
    public function clickSaveAndContinue()
    {
        $this->_rootElement->find('#save_and_continue_edit')->click();
    }
}
