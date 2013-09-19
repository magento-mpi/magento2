<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Simplexml;

class ElementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider xmlDataProvider
     */
    public function testUnsetSelf($xmlData)
    {
        /** @var $xml \Magento\Simplexml\Element */
        $xml = simplexml_load_file($xmlData[0], $xmlData[1]);
        $this->assertTrue(isset($xml->node3->node4));
        $xml->node3->unsetSelf();
        $this->assertFalse(isset($xml->node3->node4));
        $this->assertFalse(isset($xml->node3));
        $this->assertTrue(isset($xml->node1));
    }

    /**
     * @dataProvider xmlDataProvider
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Root node could not be unset.
     */
    public function testGetParent($xmlData)
    {
        /** @var $xml \Magento\Simplexml\Element */
        $xml = simplexml_load_file($xmlData[0], $xmlData[1]);
        $this->assertTrue($xml->getName() == 'root');
        $xml->unsetSelf();
    }

    /**
     * Data Provider for testUnsetSelf and testUnsetSelfException
     */
    public static function xmlDataProvider()
    {
        return array(
            array(array(__DIR__ . '/_files/data.xml', 'Magento\Simplexml\Element'))
        );
    }

    public function testAsNiceXmlMixedData()
    {
        $dataFile = file_get_contents(__DIR__ . '/_files/mixed_data.xml');
        /** @var \Magento\Simplexml\Element $xml  */
        $xml = simplexml_load_string($dataFile, 'Magento\Simplexml\Element');

        $expected = <<<XML
<root>
   <node_1 id="1">Value 1
      <node_1_1>Value 1.1
         <node_1_1_1>Value 1.1.1</node_1_1_1>
      </node_1_1>
   </node_1>
   <node_2>
      <node_2_1>Value 2.1</node_2_1>
   </node_2>
</root>

XML;
        $this->assertEquals($expected, $xml->asNiceXml());
    }

}
