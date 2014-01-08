<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Usa\Model\Shipping\Carrier;

class UspsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\Usa\Model\Shipping\Carrier\Usps
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_httpResponse;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_helper;

    public function setUp()
    {
        $this->_helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $coreStoreConfig = $this->getMockBuilder('\Magento\Core\Model\Store\Config')
            ->setMethods(array('getConfigFlag', 'getConfig'))
            ->disableOriginalConstructor()
            ->getMock();
        $coreStoreConfig->expects($this->any())
            ->method('getConfigFlag')
            ->will($this->returnValue(true));
        $coreStoreConfig->expects($this->any())
            ->method('getConfig')
            ->will($this->returnCallback(array($this, 'coreStoreConfigGetConfig')));

        // xml element factory
        $xmlElFactory = $this->getMockBuilder('\Magento\Usa\Model\Simplexml\ElementFactory')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();
        $xmlElFactory->expects($this->any())
            ->method('create')
            ->will(
                $this->returnCallback(
                    function ($data) {
                        $oM = new \Magento\TestFramework\Helper\ObjectManager($this);
                        return  $oM->getObject('\Magento\Usa\Model\Simplexml\Element', array('data' => $data['data']));
                    }
                )
            );

        // rate factory
        $rateFactory = $this->getMockBuilder('\Magento\Shipping\Model\Rate\ResultFactory')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();
        $rateResult = $this->getMockBuilder('\Magento\Shipping\Model\Rate\Result')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
        $rateFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($rateResult));

        // rate method factory
        $rateMethodFactory = $this->getMockBuilder('\Magento\Shipping\Model\Rate\Result\MethodFactory')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();
        $rateMethod = $this->getMockBuilder('Magento\Shipping\Model\Rate\Result\Method')
            ->disableOriginalConstructor()
            ->setMethods(array('setPrice'))
            ->getMock();
        $rateMethod->expects($this->any())
            ->method('setPrice')
            ->will($this->returnSelf());

        $rateMethodFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($rateMethod));

        // http client
        $this->_httpResponse = $this->getMockBuilder('\Zend_Http_Response')
            ->disableOriginalConstructor()
            ->setMethods(array('getBody'))
            ->getMock();

        $httpClient = $this->getMockBuilder('\Zend_Http_Client')
            ->disableOriginalConstructor()
            ->setMethods(array('request'))
            ->getMock();
        $httpClient->expects($this->any())
            ->method('request')
            ->will($this->returnValue($this->_httpResponse));

        $httpClientFactory = $this->getMockBuilder('\Zend_Http_ClientFactory')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();
        $httpClientFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($httpClient));

        $data = array(
            'id' => 'usps',
            'store' => '1'
        );

        $arguments = array(
            'coreStoreConfig' => $coreStoreConfig,
            'xmlElFactory' => $xmlElFactory,
            'rateFactory' => $rateFactory,
            'rateMethodFactory' => $rateMethodFactory,
            'httpClientFactory' => $httpClientFactory,
            'data' => $data
        );

        $this->_model = $this->_helper->getObject('\Magento\Usa\Model\Shipping\Carrier\Usps', $arguments);
    }

    /**
     * @dataProvider codeDataProvider
     */
    public function testGetCodeArray($code)
    {
        $this->assertNotEmpty($this->_model->getCode($code));
    }

    public function testGetCodeBool()
    {
        $this->assertFalse($this->_model->getCode('test_code'));
    }

    public function testCollectRates()
    {
        $this->_httpResponse->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue(file_get_contents(__DIR__ . '/_files/success_usps_response_rates.xml')));
        // for setRequest
        $request_params = include __DIR__ . '/_files/rates_request_data.php';
        $request = $this->_helper->getObject('Magento\Shipping\Model\Rate\Request', $request_params);

        $this->assertNotEmpty($this->_model->collectRates($request)->getAllRates());
    }

    public function testReturnOfShipment()
    {
        $this->_httpResponse->expects($this->any())
            ->method('getBody')
            ->will(
                $this->returnValue(
                    file_get_contents(__DIR__ . '/_files/success_usps_response_return_shipment.xml')
                )
            );
        $request_params = include __DIR__ . '/_files/return_shipment_request_data.php';
        $request = $this->_helper->getObject('Magento\Shipping\Model\Shipment\ReturnShipment', $request_params);
        $this->assertNotEmpty($this->_model->returnOfShipment($request)->getInfo()[0]['tracking_number']);

    }

    /**
     * Callback function, emulates getConfig function
     * @param $path
     * @return null|string
     */
    public function coreStoreConfigGetConfig($path)
    {
        switch ($path) {
            case 'carriers/usps/allowed_methods':
                return '0_FCLE,0_FCL,0_FCP,1,2,3,4,6,7,13,16,17,22,23,25,27,28,33,34,35,36,37,42,43,53,'
                    . '55,56,57,61,INT_1,INT_2,INT_4,INT_6,INT_7,INT_8,INT_9,INT_10,INT_11,INT_12,INT_13,INT_14,'
                    . 'INT_15,INT_16,INT_20,INT_26';
            default:
                return null;
        }
    }

    /**
     * @return array
     */
    public function codeDataProvider()
    {
        return array(
            array('container'),
            array('machinable'),
            array('method'),
            array('size')
        );
    }

}
