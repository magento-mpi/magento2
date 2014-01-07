<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Data
     */
    private $_helper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_emailTemplate;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_translator;

    protected function setUp()
    {
        $this->_translator = $this->getMock('Magento\Core\Model\Translate', array(), array(), '', false);
        $context = $this->getMock('\Magento\App\Helper\Context', array(), array(), '', false);
        $context->expects($this->any())->method('getTranslator')->will($this->returnValue($this->_translator));

        $storeConfig = $this->getMock('\Magento\Core\Model\Store\Config', array(), array(), '', false);
        $storeConfig->expects($this->any())->method('getConfig')->will($this->returnValueMap(array(
            array('checkout/payment_failed/template',       8, 'fixture_email_template_payment_failed'),
            array('checkout/payment_failed/receiver',       8, 'sysadmin'),
            array('trans_email/ident_sysadmin/email',       8, 'sysadmin@example.com'),
            array('trans_email/ident_sysadmin/name',        8, 'System Administrator'),
            array('checkout/payment_failed/identity',       8, 'noreply@example.com'),
            array('carriers/ground/title',                  null, 'Ground Shipping'),
            array('payment/fixture-payment-method/title',   null, 'Check Money Order'),
        )));

        $storeManager = $this->getMock('\Magento\Core\Model\StoreManagerInterface', array(), array(), '', false);

        $checkoutSession = $this->getMock('\Magento\Checkout\Model\Session', array(), array(), '', false);

        $locale = $this->getMock('\Magento\Core\Model\LocaleInterface', array(), array(), '', false);
        $locale->expects($this->any())->method('date')->will($this->returnValue('Oct 02, 2013'));

        $collectionFactory = $this->getMock(
            '\Magento\Checkout\Model\Resource\Agreement\CollectionFactory', array(), array(), '', false
        );

        $this->_emailTemplate = $this->getMock('\Magento\Email\Model\Template', array(), array(), '', false);
        $emailTplFactory = $this->getMock(
            '\Magento\Email\Model\TemplateFactory', array('create'), array(), '', false
        );
        $emailTplFactory->expects($this->once())->method('create')->will($this->returnValue($this->_emailTemplate));

        $this->_helper = new Data(
            $context, $storeConfig, $storeManager, $checkoutSession,
            $locale, $collectionFactory, $emailTplFactory
        );
    }

    public function testSendPaymentFailedEmail()
    {
        $shippingAddress = new \Magento\Object(array('shipping_method' => 'ground_transportation'));
        $billingAddress = new \Magento\Object(array('street' => 'Fixture St'));

        $this->_emailTemplate
            ->expects($this->once())
            ->method('setDesignConfig')
            ->with(array('area' => \Magento\Core\Model\App\Area::AREA_FRONTEND, 'store' => 8))
            ->will($this->returnSelf())
        ;
        $this->_emailTemplate->expects($this->once())->method('sendTransactional')->with(
            'fixture_email_template_payment_failed',
            'noreply@example.com',
            'sysadmin@example.com',
            'System Administrator',
            $this->identicalTo(array(
                'reason'            => 'test message',
                'checkoutType'      => 'onepage',
                'dateAndTime'       => 'Oct 02, 2013',
                'customer'          => 'John Doe',
                'customerEmail'     => 'john.doe@example.com',
                'billingAddress'    => $billingAddress,
                'shippingAddress'   => $shippingAddress,
                'shippingMethod'    => 'Ground Shipping',
                'paymentMethod'     => 'Check Money Order',
                'items'             => "Product One  x 2  USD 10<br />\nProduct Two  x 3  USD 60<br />\n",
                'total'             => 'USD 70',
            ))
        );

        $this->_translator->expects($this->at(0))->method('setTranslateInline')->with(false);
        $this->_translator->expects($this->at(1))->method('setTranslateInline')->with(true);

        $productOne = $this->getMock('\Magento\Catalog\Model\Product', array(), array(), '', false);
        $productOne->expects($this->once())->method('getName')->will($this->returnValue('Product One'));
        $productOne->expects($this->once())->method('getFinalPrice')->with(2)->will($this->returnValue(10));

        $productTwo = $this->getMock('\Magento\Catalog\Model\Product', array(), array(), '', false);
        $productTwo->expects($this->once())->method('getName')->will($this->returnValue('Product Two'));
        $productTwo->expects($this->once())->method('getFinalPrice')->with(3)->will($this->returnValue(60));

        $quote = new \Magento\Object(array(
            'store_id'              => 8,
            'store_currency_code'   => 'USD',
            'grand_total'           => 70,
            'customer_firstname'    => 'John',
            'customer_lastname'     => 'Doe',
            'customer_email'        => 'john.doe@example.com',
            'billing_address'       => $billingAddress,
            'shipping_address'      => $shippingAddress,
            'payment'               => new \Magento\Object(array('method' => 'fixture-payment-method')),
            'all_visible_items'     => array(
                new \Magento\Object(array('product' => $productOne, 'qty' => 2)),
                new \Magento\Object(array('product' => $productTwo, 'qty' => 3)),
            ),
        ));
        $this->assertSame($this->_helper, $this->_helper->sendPaymentFailedEmail($quote, 'test message'));
    }
}
