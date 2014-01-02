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

    public function setUp()
    {
        $this->_helper = new \Magento\TestFramework\Helper\ObjectManager($this);
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
}
