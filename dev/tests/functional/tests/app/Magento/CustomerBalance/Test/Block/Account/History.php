<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerBalance\Test\Block\Account;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class History
 * Store credit block on customer account page
 */
class History extends Block
{
    /**
     * Redeem button
     *
     * @var string
     */
    protected $balanceChange = '//*[contains(@class,"change")]/span[contains(.,"%s")]';

    /**
     * Check store credit balance history
     *
     * @param string $value
     * @return bool
     */
    public function isBalanceChangeVisible($value)
    {
        return $this->_rootElement->find(sprintf($this->balanceChange, $value), Locator::SELECTOR_XPATH)->isVisible();
    }
}
