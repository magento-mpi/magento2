<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Usa\Model\Shipping\Carrier;


class DhlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_helper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_httpResponse;

    /**
     * @var \Magento\Usa\Model\Shipping\Carrier\Dhl
     */
    protected $_model;

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
            'id' => 'dhl',
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

        $this->_model = $this->_helper->getObject('Magento\Usa\Model\Shipping\Carrier\Dhl', $arguments);
    }

    /**
     * Callback function, emulates getConfig function
     * @param $path
     * @return null|string
     */
    public function coreStoreConfigGetConfig($path)
    {
        switch ($path) {
            case 'carriers/dhl/shipment_days':
            case 'carriers/dhl/intl_shipment_days':
                return 'Mon,Tue,Wed,Thu,Fri,Sat';
            case 'carriers/dhl/allowed_methods':
                return 'IE';
            case 'carriers/dhl/international_searvice':
                return 'IE';
            default:
                return null;
        }
    }

    public function testCollectRates()
    {
        $this->_httpResponse->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue(file_get_contents(__DIR__ . '/_files/success_dhl_response_rates.xml')));
        // for setRequest
        $request_params = include __DIR__ . '/_files/rates_request_data_dhl.php';
        $request = $this->_helper->getObject('Magento\Shipping\Model\Rate\Request', $request_params);

        $this->assertNotEmpty($this->_model->collectRates($request)->getAllRates());
    }
}
