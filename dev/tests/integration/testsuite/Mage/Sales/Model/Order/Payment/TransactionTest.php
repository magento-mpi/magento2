<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests transaction model:
 *
 * @see Mage_Sales_Model_Order_Payment_Transaction
 * @magentoDataFixture Mage/Sales/_files/transactions_commit_workaround.php
 */
class Mage_Sales_Model_Order_Payment_TransactionTest extends PHPUnit_Framework_TestCase
{
    public function testLoadByTxnId()
    {
        $order = new Mage_Sales_Model_Order();
        $order->loadByIncrementId('100000001');

        $model = new Mage_Sales_Model_Order_Payment_Transaction;
        $model->setOrderPaymentObject($order->getPayment())
            ->loadByTxnId('invalid_transaction_id');

        $this->assertNull($model->getId());

        $model->loadByTxnId('trx1');
        $this->assertNotNull($model->getId());
    }
}
