<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Pbridge\Model\Payment\Method\Payone;

use Magento\Framework\Object;
use Magento\Payment\Model\Method\AbstractMethod;

class GateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Gate|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_scopeConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_url;

    /**
     * setUp
     */
    protected function setUp()
    {
        $this->_url = $this->getMock(
            'Magento\Framework\UrlInterface',
            [
                'getUrl',
                'getUseSession',
                'getBaseUrl',
                'getCurrentUrl',
                'getRouteUrls',
                'addSessionParam',
                'addQueryParams',
                'getRouteUrl',
                'setQueryParam',
                'escape',
                'getDirectUrl',
                'sessionUrlVar',
                'isOwnOriginUrl',
                'getRedirectUrl',
                'setScope'
            ],
            [],
            '',
            false
        );
        $this->_scopeConfig = $this->getMock(
            'Magento\Framework\App\Config\ScopeConfigInterface',
            ['getValue', 'isSetFlag'],
            [],
            '',
            false
        );
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $helper->getObject(
            'Magento\Pbridge\Model\Payment\Method\Payone\Gate',
            [
                'scopeConfig' => $this->_scopeConfig,
                'url' => $this->_url
            ]
        );
    }

    /**
     * @dataProvider isInitializeNeededDataProvider
     */
    public function testIsInitializeNeeded($value, $expected)
    {
        $this->_scopeConfig->expects($this->once())->method('getValue')->with(
            'payment/payone_gate/enable3ds'
        )->will($this->returnValue($value));
        $this->assertEquals($expected, $this->_model->isInitializeNeeded());
    }

    public function isInitializeNeededDataProvider()
    {
        return [
            [1, true], [0, false]
        ];
    }

    /**
     * @dataProvider initializeDataProvider
     */
    public function testInitialize($paymentAction, $expected)
    {
        $stateObject = new Object([]);
        $instance = $this->getMock('Magento\Payment\Model\Info', ['getOrder', '__wakeup'], [], '', false);
        $instance->expects($this->any())->method('getOrder')->will(
            $this->returnValue(new Object(['total_due' => 123, 'base_total_due' => 111]))
        );
        $this->_model->setData('info_instance', $instance);
        $this->_model->initialize($paymentAction, $stateObject);
        $this->assertEquals($expected, $stateObject->getStatus());
    }

    public function initializeDataProvider()
    {
        return [
            [AbstractMethod::ACTION_AUTHORIZE, 'pending_payment'],
            [AbstractMethod::ACTION_AUTHORIZE_CAPTURE, 'pending_payment'],
            ['something else', null]
        ];
    }

    public function testValidate()
    {
        $this->assertTrue($this->_model->validate());
    }

    /**
     * @dataProvider is3dSecureEnabledDataProvider
     */
    public function testGetIsPendingOrderRequired($value, $expected)
    {
        $this->_scopeConfig->expects($this->once())->method('getValue')->with(
            'payment/payone_gate/enable3ds'
        )->will($this->returnValue($value));
        $this->assertEquals($expected, $this->_model->getIsPendingOrderRequired());
    }

    public function is3dSecureEnabledDataProvider()
    {
        return [
            [1, true], [0, false]
        ];
    }

    public function testGetRedirectUrlSuccess()
    {
        $this->_url->expects($this->once())->method('getUrl')->with(
            'magento_pbridge/pbridge/onepagesuccess',
            ['_secure' => true]
        );
        $this->_model->getRedirectUrlSuccess();
    }

    public function testGetRedirectUrlError()
    {
        $this->_url->expects($this->once())->method('getUrl')->with(
            'magento_pbridge/pbridge/cancel',
            ['_secure' => true]
        );
        $this->_model->getRedirectUrlError();
    }
}
