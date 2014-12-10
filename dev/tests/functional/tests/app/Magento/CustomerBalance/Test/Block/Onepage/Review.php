<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerBalance\Test\Block\Onepage;


/**
 * Class Review
 * One page checkout order review block
 */
class Review extends \Magento\Checkout\Test\Block\Onepage\Review
{
    /**
     * Remove store credit
     *
     * @var string
     */
    protected $removeButton = '.ballance .action.delete';

    /**
     * Click on 'Remove Store Credit' link
     *
     * @return void
     */
    public function clickRemoveStoreCredit()
    {
        $this->_rootElement->find($this->removeButton)->click();
    }
}
