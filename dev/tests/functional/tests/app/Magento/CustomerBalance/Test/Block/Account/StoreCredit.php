<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerBalance\Test\Block\Account;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class StoreCredit
 * Store credit block on customer account page
 */
class StoreCredit extends Block
{
    /**
     * Redeem button
     *
     * @var string
     */
    protected $balanceChange = '//*[contains(@class,"change")]/span[contains(.,"%s")]';

    /**
     * Fill gift card redeem
     *
     * @param string $value
     * @return bool
     */
    public function isBalanceChangeVisible($value)
    {
        return $this->_rootElement->find(sprintf($this->balanceChange, $value), Locator::SELECTOR_XPATH)->isVisible();
    }
}
