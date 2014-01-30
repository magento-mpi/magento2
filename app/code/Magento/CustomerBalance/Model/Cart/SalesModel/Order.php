<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * CustomerBalance adapter for \Magento\Sales\Model\Order sales model
 */
namespace Magento\CustomerBalance\Model\Cart\SalesModel;

class Order extends \Magento\Payment\Model\Cart\SalesModel\Order
{
    /**
     * Overwrite for specific data key
     *
     * @param $key
     * @param null $args
     * @return mixed
     */
    public function getDataUsingMethod($key, $args = null)
    {
        if ($key == 'customer_balance_base_amount') {
            $key = 'base_customer_balance_amount';
        }
        return parent::getDataUsingMethod($key, $args);
    }
}
