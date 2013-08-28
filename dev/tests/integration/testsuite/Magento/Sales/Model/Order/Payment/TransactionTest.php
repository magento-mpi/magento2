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
 * @see Magento_Sales_Model_Order_Payment_Transaction
 * @magentoDataFixture Magento/Sales/_files/transactions.php
 */
class Magento_Sales_Model_Order_Payment_TransactionTest extends PHPUnit_Framework_TestCase
{
    public function testLoadByTxnId()
    {
        $order = Mage::getModel('Magento_Sales_Model_Order');
        $order->loadByIncrementId('100000001');

        $model = Mage::getModel('Magento_Sales_Model_Order_Payment_Transaction');
        $model->setOrderPaymentObject($order->getPayment())
            ->loadByTxnId('invalid_transaction_id');

        $this->assertNull($model->getId());

        $model->loadByTxnId('trx1');
        $this->assertNotNull($model->getId());
    }
}
