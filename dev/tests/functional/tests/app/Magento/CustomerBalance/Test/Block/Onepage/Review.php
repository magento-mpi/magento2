<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerBalance\Test\Block\Onepage;

use Mtf\Client\Element;
use Magento\Checkout\Test\Block\Onepage\Review as ParentBlock;

/**
 * Class Review
 * One page checkout order review block
 */
class Review extends ParentBlock
{
    /**
     * Remove store credit
     *
     * @var string
     */
    protected $removeButton = '.action.delete';

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
