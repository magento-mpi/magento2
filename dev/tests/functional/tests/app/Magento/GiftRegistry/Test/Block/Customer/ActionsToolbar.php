<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftRegistry\Test\Block\Customer;

use Mtf\Block\Block;

/**
 * Class ActionsToolbar
 * Gift registry frontend actions block
 */
class ActionsToolbar extends Block
{
    /**
     * "Add New" button
     *
     * @var string
     */
    protected $addNewButton = '.add';

    /**
     * Click on "Add New" button
     *
     * @return void
     */
    public function addNew()
    {
        $this->_rootElement->find($this->addNewButton)->click();
    }
}
