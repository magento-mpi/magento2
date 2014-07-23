<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Data;

class CreditmemoTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAdjustment()
    {
        $data = ['adjustment' => 'test_value_adjustment'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_adjustment', $object->getAdjustment());
    }

    public function testGetAdjustmentNegative()
    {
        $data = ['adjustment_negative' => 'test_value_adjustment_negative'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_adjustment_negative', $object->getAdjustmentNegative());
    }

    public function testGetAdjustmentPositive()
    {
        $data = ['adjustment_positive' => 'test_value_adjustment_positive'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_adjustment_positive', $object->getAdjustmentPositive());
    }

    public function testGetBaseAdjustment()
    {
        $data = ['base_adjustment' => 'test_value_base_adjustment'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_base_adjustment', $object->getBaseAdjustment());
    }

    public function testGetBaseAdjustmentNegative()
    {
        $data = ['base_adjustment_negative' => 'test_value_base_adjustment_negative'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_base_adjustment_negative', $object->getBaseAdjustmentNegative());
    }

    public function testGetBaseAdjustmentPositive()
    {
        $data = ['base_adjustment_positive' => 'test_value_base_adjustment_positive'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_base_adjustment_positive', $object->getBaseAdjustmentPositive());
    }

    public function testGetBaseCurrencyCode()
    {
        $data = ['base_currency_code' => 'test_value_base_currency_code'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_base_currency_code', $object->getBaseCurrencyCode());
    }

    public function testGetBaseCustomerBalanceAmount()
    {
        $data = ['base_customer_balance_amount' => 'test_value_base_customer_balance_amount'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_base_customer_balance_amount', $object->getBaseCustomerBalanceAmount());
    }

    public function testGetBaseDiscountAmount()
    {
        $data = ['base_discount_amount' => 'test_value_base_discount_amount'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_base_discount_amount', $object->getBaseDiscountAmount());
    }

    public function testGetBaseGiftCardsAmount()
    {
        $data = ['base_gift_cards_amount' => 'test_value_base_gift_cards_amount'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_base_gift_cards_amount', $object->getBaseGiftCardsAmount());
    }

    public function testGetBaseGrandTotal()
    {
        $data = ['base_grand_total' => 'test_value_base_grand_total'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_base_grand_total', $object->getBaseGrandTotal());
    }

    public function testGetBaseHiddenTaxAmount()
    {
        $data = ['base_hidden_tax_amount' => 'test_value_base_hidden_tax_amount'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_base_hidden_tax_amount', $object->getBaseHiddenTaxAmount());
    }

    public function testGetBaseRewardCurrencyAmount()
    {
        $data = ['base_reward_currency_amount' => 'test_value_base_reward_currency_amount'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_base_reward_currency_amount', $object->getBaseRewardCurrencyAmount());
    }

    public function testGetBaseShippingAmount()
    {
        $data = ['base_shipping_amount' => 'test_value_base_shipping_amount'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_base_shipping_amount', $object->getBaseShippingAmount());
    }

    public function testGetBaseShippingHiddenTaxAmnt()
    {
        $data = ['base_shipping_hidden_tax_amnt' => 'test_value_base_shipping_hidden_tax_amnt'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_base_shipping_hidden_tax_amnt', $object->getBaseShippingHiddenTaxAmnt());
    }

    public function testGetBaseShippingInclTax()
    {
        $data = ['base_shipping_incl_tax' => 'test_value_base_shipping_incl_tax'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_base_shipping_incl_tax', $object->getBaseShippingInclTax());
    }

    public function testGetBaseShippingTaxAmount()
    {
        $data = ['base_shipping_tax_amount' => 'test_value_base_shipping_tax_amount'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_base_shipping_tax_amount', $object->getBaseShippingTaxAmount());
    }

    public function testGetBaseSubtotal()
    {
        $data = ['base_subtotal' => 'test_value_base_subtotal'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_base_subtotal', $object->getBaseSubtotal());
    }

    public function testGetBaseSubtotalInclTax()
    {
        $data = ['base_subtotal_incl_tax' => 'test_value_base_subtotal_incl_tax'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_base_subtotal_incl_tax', $object->getBaseSubtotalInclTax());
    }

    public function testGetBaseTaxAmount()
    {
        $data = ['base_tax_amount' => 'test_value_base_tax_amount'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_base_tax_amount', $object->getBaseTaxAmount());
    }

    public function testGetBaseToGlobalRate()
    {
        $data = ['base_to_global_rate' => 'test_value_base_to_global_rate'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_base_to_global_rate', $object->getBaseToGlobalRate());
    }

    public function testGetBaseToOrderRate()
    {
        $data = ['base_to_order_rate' => 'test_value_base_to_order_rate'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_base_to_order_rate', $object->getBaseToOrderRate());
    }

    public function testGetBillingAddressId()
    {
        $data = ['billing_address_id' => 'test_value_billing_address_id'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_billing_address_id', $object->getBillingAddressId());
    }

    public function testGetBsCustomerBalTotalRefunded()
    {
        $data = ['bs_customer_bal_total_refunded' => 'test_value_bs_customer_bal_total_refunded'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_bs_customer_bal_total_refunded', $object->getBsCustomerBalTotalRefunded());
    }

    public function testGetCreatedAt()
    {
        $data = ['created_at' => 'test_value_created_at'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_created_at', $object->getCreatedAt());
    }

    public function testGetCreditmemoStatus()
    {
        $data = ['creditmemo_status' => 'test_value_creditmemo_status'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_creditmemo_status', $object->getCreditmemoStatus());
    }

    public function testGetCustomerBalanceAmount()
    {
        $data = ['customer_balance_amount' => 'test_value_customer_balance_amount'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_customer_balance_amount', $object->getCustomerBalanceAmount());
    }

    public function testGetCustomerBalTotalRefunded()
    {
        $data = ['customer_bal_total_refunded' => 'test_value_customer_bal_total_refunded'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_customer_bal_total_refunded', $object->getCustomerBalTotalRefunded());
    }

    public function testGetDiscountAmount()
    {
        $data = ['discount_amount' => 'test_value_discount_amount'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_discount_amount', $object->getDiscountAmount());
    }

    public function testGetDiscountDescription()
    {
        $data = ['discount_description' => 'test_value_discount_description'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_discount_description', $object->getDiscountDescription());
    }

    public function testGetEmailSent()
    {
        $data = ['email_sent' => 'test_value_email_sent'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_email_sent', $object->getEmailSent());
    }

    public function testGetEntityId()
    {
        $data = ['entity_id' => 'test_value_entity_id'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_entity_id', $object->getEntityId());
    }

    public function testGetGiftCardsAmount()
    {
        $data = ['gift_cards_amount' => 'test_value_gift_cards_amount'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_gift_cards_amount', $object->getGiftCardsAmount());
    }

    public function testGetGlobalCurrencyCode()
    {
        $data = ['global_currency_code' => 'test_value_global_currency_code'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_global_currency_code', $object->getGlobalCurrencyCode());
    }

    public function testGetGrandTotal()
    {
        $data = ['grand_total' => 'test_value_grand_total'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_grand_total', $object->getGrandTotal());
    }

    public function testGetGwBasePrice()
    {
        $data = ['gw_base_price' => 'test_value_gw_base_price'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_gw_base_price', $object->getGwBasePrice());
    }

    public function testGetGwBaseTaxAmount()
    {
        $data = ['gw_base_tax_amount' => 'test_value_gw_base_tax_amount'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_gw_base_tax_amount', $object->getGwBaseTaxAmount());
    }

    public function testGetGwCardBasePrice()
    {
        $data = ['gw_card_base_price' => 'test_value_gw_card_base_price'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_gw_card_base_price', $object->getGwCardBasePrice());
    }

    public function testGetGwCardBaseTaxAmount()
    {
        $data = ['gw_card_base_tax_amount' => 'test_value_gw_card_base_tax_amount'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_gw_card_base_tax_amount', $object->getGwCardBaseTaxAmount());
    }

    public function testGetGwCardPrice()
    {
        $data = ['gw_card_price' => 'test_value_gw_card_price'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_gw_card_price', $object->getGwCardPrice());
    }

    public function testGetGwCardTaxAmount()
    {
        $data = ['gw_card_tax_amount' => 'test_value_gw_card_tax_amount'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_gw_card_tax_amount', $object->getGwCardTaxAmount());
    }

    public function testGetGwItemsBasePrice()
    {
        $data = ['gw_items_base_price' => 'test_value_gw_items_base_price'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_gw_items_base_price', $object->getGwItemsBasePrice());
    }

    public function testGetGwItemsBaseTaxAmount()
    {
        $data = ['gw_items_base_tax_amount' => 'test_value_gw_items_base_tax_amount'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_gw_items_base_tax_amount', $object->getGwItemsBaseTaxAmount());
    }

    public function testGetGwItemsPrice()
    {
        $data = ['gw_items_price' => 'test_value_gw_items_price'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_gw_items_price', $object->getGwItemsPrice());
    }

    public function testGetGwItemsTaxAmount()
    {
        $data = ['gw_items_tax_amount' => 'test_value_gw_items_tax_amount'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_gw_items_tax_amount', $object->getGwItemsTaxAmount());
    }

    public function testGetGwPrice()
    {
        $data = ['gw_price' => 'test_value_gw_price'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_gw_price', $object->getGwPrice());
    }

    public function testGetGwTaxAmount()
    {
        $data = ['gw_tax_amount' => 'test_value_gw_tax_amount'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_gw_tax_amount', $object->getGwTaxAmount());
    }

    public function testGetHiddenTaxAmount()
    {
        $data = ['hidden_tax_amount' => 'test_value_hidden_tax_amount'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_hidden_tax_amount', $object->getHiddenTaxAmount());
    }

    public function testGetIncrementId()
    {
        $data = ['increment_id' => 'test_value_increment_id'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_increment_id', $object->getIncrementId());
    }

    public function testGetInvoiceId()
    {
        $data = ['invoice_id' => 'test_value_invoice_id'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_invoice_id', $object->getInvoiceId());
    }

    public function testGetOrderCurrencyCode()
    {
        $data = ['order_currency_code' => 'test_value_order_currency_code'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_order_currency_code', $object->getOrderCurrencyCode());
    }

    public function testGetOrderId()
    {
        $data = ['order_id' => 'test_value_order_id'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_order_id', $object->getOrderId());
    }

    public function testGetRewardCurrencyAmount()
    {
        $data = ['reward_currency_amount' => 'test_value_reward_currency_amount'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_reward_currency_amount', $object->getRewardCurrencyAmount());
    }

    public function testGetRewardPointsBalance()
    {
        $data = ['reward_points_balance' => 'test_value_reward_points_balance'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_reward_points_balance', $object->getRewardPointsBalance());
    }

    public function testGetRewardPointsBalanceRefund()
    {
        $data = ['reward_points_balance_refund' => 'test_value_reward_points_balance_refund'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_reward_points_balance_refund', $object->getRewardPointsBalanceRefund());
    }

    public function testGetShippingAddressId()
    {
        $data = ['shipping_address_id' => 'test_value_shipping_address_id'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_shipping_address_id', $object->getShippingAddressId());
    }

    public function testGetShippingAmount()
    {
        $data = ['shipping_amount' => 'test_value_shipping_amount'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_shipping_amount', $object->getShippingAmount());
    }

    public function testGetShippingHiddenTaxAmount()
    {
        $data = ['shipping_hidden_tax_amount' => 'test_value_shipping_hidden_tax_amount'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_shipping_hidden_tax_amount', $object->getShippingHiddenTaxAmount());
    }

    public function testGetShippingInclTax()
    {
        $data = ['shipping_incl_tax' => 'test_value_shipping_incl_tax'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_shipping_incl_tax', $object->getShippingInclTax());
    }

    public function testGetShippingTaxAmount()
    {
        $data = ['shipping_tax_amount' => 'test_value_shipping_tax_amount'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_shipping_tax_amount', $object->getShippingTaxAmount());
    }

    public function testGetState()
    {
        $data = ['state' => 'test_value_state'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_state', $object->getState());
    }

    public function testGetStoreCurrencyCode()
    {
        $data = ['store_currency_code' => 'test_value_store_currency_code'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_store_currency_code', $object->getStoreCurrencyCode());
    }

    public function testGetStoreId()
    {
        $data = ['store_id' => 'test_value_store_id'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_store_id', $object->getStoreId());
    }

    public function testGetStoreToBaseRate()
    {
        $data = ['store_to_base_rate' => 'test_value_store_to_base_rate'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_store_to_base_rate', $object->getStoreToBaseRate());
    }

    public function testGetStoreToOrderRate()
    {
        $data = ['store_to_order_rate' => 'test_value_store_to_order_rate'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_store_to_order_rate', $object->getStoreToOrderRate());
    }

    public function testGetSubtotal()
    {
        $data = ['subtotal' => 'test_value_subtotal'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_subtotal', $object->getSubtotal());
    }

    public function testGetSubtotalInclTax()
    {
        $data = ['subtotal_incl_tax' => 'test_value_subtotal_incl_tax'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_subtotal_incl_tax', $object->getSubtotalInclTax());
    }

    public function testGetTaxAmount()
    {
        $data = ['tax_amount' => 'test_value_tax_amount'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_tax_amount', $object->getTaxAmount());
    }

    public function testGetTransactionId()
    {
        $data = ['transaction_id' => 'test_value_transaction_id'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_transaction_id', $object->getTransactionId());
    }

    public function testGetUpdatedAt()
    {
        $data = ['updated_at' => 'test_value_updated_at'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Creditmemo($abstractBuilderMock);

        $this->assertEquals('test_value_updated_at', $object->getUpdatedAt());
    }
}
