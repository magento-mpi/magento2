<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerBalance\Model\Cart\SalesModel;

/**
 * CustomerBalance adapter for \Magento\Sales\Model\Quote sales model
 */
class Quote extends \Magento\Payment\Model\Cart\SalesModel\Quote
{
    /**
     * Overwrite for specific data key
     *
     * @param string $key
     * @param mixed $args
     * @return mixed
     */
    public function getDataUsingMethod($key, $args = null)
    {
        if ($key == 'customer_balance_base_amount') {
            $key = 'base_customer_bal_amount_used';
        }
        return parent::getDataUsingMethod($key, $args);
    }
}
