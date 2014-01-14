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

namespace Magento\Usa\Model\Shipping\Carrier\Dhl;

class InternationalTest extends \PHPUnit_Framework_TestCase
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
        $coreStoreConfig->expects($this->any())->method('getConfigFlag')->will($this->returnValue(true));
        $coreStoreConfig->expects($this->any())->method('getConfig')
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
        $rateFactory = $this->getMockBuilder('\Magento\Shipping\Model\Rate\ResultFactory')->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();
        $rateResult = $this->getMockBuilder('\Magento\Shipping\Model\Rate\Result')->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
        $rateFactory->expects($this->any())->method('create')->will($this->returnValue($rateResult));

        // rate method factory
        $rateMethodFactory = $this->getMockBuilder('\Magento\Shipping\Model\Rate\Result\MethodFactory')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();
        $rateMethod = $this->getMockBuilder('Magento\Shipping\Model\Rate\Result\Method')->disableOriginalConstructor()
            ->setMethods(array('setPrice'))
            ->getMock();
        $rateMethod->expects($this->any())->method('setPrice')->will($this->returnSelf());

        $rateMethodFactory->expects($this->any())->method('create')->will($this->returnValue($rateMethod));

        // http client
        $this->_httpResponse = $this->getMockBuilder('\Zend_Http_Response')->disableOriginalConstructor()
            ->setMethods(array('getBody'))
            ->getMock();

        $httpClient = $this->getMockBuilder('\Zend_Http_Client')->disableOriginalConstructor()
            ->setMethods(array('request'))
            ->getMock();
        $httpClient->expects($this->any())->method('request')->will($this->returnValue($this->_httpResponse));

        $httpClientFactory = $this->getMockBuilder('\Zend_Http_ClientFactory')->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();
        $httpClientFactory->expects($this->any())->method('create')->will($this->returnValue($httpClient));
        $modulesDirectory = $this->getMockBuilder('\Magento\Filesystem\Directory\Read')->disableOriginalConstructor()
            ->setMethods(array('getRelativePath', 'readFile'))
            ->getMock();
        $modulesDirectory->expects($this->any())->method('readFile')
            ->will($this->returnValue(file_get_contents(__DIR__ . '/_files/countries.xml')));
        $filesystem = $this->getMockBuilder('\Magento\Filesystem')->disableOriginalConstructor()
            ->setMethods(array('getDirectoryRead'))
            ->getMock();
        $filesystem->expects($this->any())->method('getDirectoryRead')->will($this->returnValue($modulesDirectory));
        $storeManager = $this->getMockBuilder('\Magento\Core\Model\StoreManager')->disableOriginalConstructor()
            ->setMethods(array('getWebsite'))
            ->getMock();
        $website = $this->getMockBuilder('\Magento\Core\Model\Website')->disableOriginalConstructor()
            ->setMethods(array('getBaseCurrencyCode', '__wakeup'))
            ->getMock();
        $website->expects($this->any())->method('getBaseCurrencyCode')->will($this->returnValue('USD'));
        $storeManager->expects($this->any())->method('getWebsite')->will($this->returnValue($website));

        $arguments = array(
            'coreStoreConfig' => $coreStoreConfig,
            'xmlElFactory' => $xmlElFactory,
            'rateFactory' => $rateFactory,
            'rateMethodFactory' => $rateMethodFactory,
            'httpClientFactory' => $httpClientFactory,
            'filesystem' => $filesystem,
            'storeManager' => $storeManager,
            'data' => array('id' => 'dhlint', 'store' => '1')
        );
        $this->_model = $this->_helper->getObject('Magento\Usa\Model\Shipping\Carrier\Dhl\International', $arguments);
    }

    /**
     * Callback function, emulates getConfig function
     * @param $path
     * @return null|string
     */
    public function coreStoreConfigGetConfig($path)
    {
        $pathMap = array(
            'carriers/dhlint/shipment_days' => 'Mon,Tue,Wed,Thu,Fri,Sat',
            'carriers/dhlint/intl_shipment_days' => 'Mon,Tue,Wed,Thu,Fri,Sat',
            'carriers/dhlint/allowed_methods' => 'IE',
            'carriers/dhlint/international_searvice' => 'IE',
            'carriers/dhlint/gateway_url' => 'https://xmlpi-ea.dhl.com/XMLShippingServlet',
            'carriers/dhlint/id' => 'some ID',
            'carriers/dhlint/password' => 'some password',
            'carriers/dhlint/content_type' => 'N',
            'carriers/dhlint/nondoc_methods' => '1,3,4,8,P,Q,E,F,H,J,M,V,Y'
        );
        return (isset($pathMap[$path])) ? $pathMap[$path] : null;
    }

    public function testPrepareShippingLabelContent()
    {
        $xml = simplexml_load_file(
            __DIR__ . DIRECTORY_SEPARATOR . '_files'
            . DIRECTORY_SEPARATOR . 'response_shipping_label.xml'
        );
        $result = $this->_invokePrepareShippingLabelContent($xml);
        $this->assertEquals(1111, $result->getTrackingNumber());
        $this->assertEquals(base64_decode('OutputImageContent'), $result->getShippingLabelContent());
    }

    /**
     * @dataProvider prepareShippingLabelContentExceptionDataProvider
     * @expectedException \Magento\Core\Exception
     * @expectedExceptionMessage Unable to retrieve shipping label
     */
    public function testPrepareShippingLabelContentException(\SimpleXMLElement $xml)
    {
        $this->_invokePrepareShippingLabelContent($xml);
    }

    /**
     * @return array
     */
    public function prepareShippingLabelContentExceptionDataProvider()
    {
        $filesPath = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
        $empty = $billingNumberOnly = $outputImageOnly = simplexml_load_file(
            $filesPath . 'response_shipping_label.xml'
        );
        unset(
            $empty->AirwayBillNumber, $empty->LabelImage,
            $billingNumberOnly->LabelImage, $outputImageOnly->AirwayBillNumber
        );

        return array(
            array($empty),
            array($billingNumberOnly),
            array($outputImageOnly),
        );
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return \Magento\Object
     */
    protected function _invokePrepareShippingLabelContent(\SimpleXMLElement $xml)
    {
        $model = $this->_helper->getObject('Magento\Usa\Model\Shipping\Carrier\Dhl\International');
        $method = new \ReflectionMethod($model, '_prepareShippingLabelContent');
        $method->setAccessible(true);
        return $method->invoke($model, $xml);
    }

    public function testCollectRates()
    {
        $this->_httpResponse->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue(file_get_contents(__DIR__ . '/_files/success_dhlintl_response_rates.xml')));
        // for setRequest
        $request_params = include __DIR__ . '/_files/rates_request_data_dhlintl.php';
        $request = $this->_helper->getObject('Magento\Shipping\Model\Rate\Request', $request_params);
        $this->assertNotEmpty($this->_model->collectRates($request)->getAllRates());
    }
}
