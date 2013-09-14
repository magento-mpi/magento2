<?php
/**
 * Test XML Renderer for REST.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Rest_Response_Renderer_XmlTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Webapi_Controller_Rest_Response_Renderer_Xml */
    protected $_restXmlRenderer;

    protected function setUp()
    {
        /** Prepare object for SUT constructor. */
        $xmlGenerator = new Magento_Xml_Generator();
        /** Initialize SUT. */
        $this->_restXmlRenderer = new Magento_Webapi_Controller_Rest_Response_Renderer_Xml($xmlGenerator);
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_restXmlRenderer);
        parent::tearDown();
    }

    /**
     * Test render method.
     *
     * @dataProvider providerXmlRender
     */
    public function testRender($dataToRender, $expectedXml, $message)
    {
        $actualXml = $this->_restXmlRenderer->render($dataToRender);
        $this->assertXmlStringEqualsXmlString($expectedXml, $actualXml, $message);
    }

    /**
     * Test GetMimeType method.
     */
    public function testGetMimeType()
    {
        $exceptedMimeType = 'application/xml';
        $this->assertEquals($exceptedMimeType, $this->_restXmlRenderer->getMimeType(), 'Unexpected mime type.');
    }

    /**
     * Data provider for render method testing.
     *
     * @return array
     */
    public function providerXmlRender()
    {
        return array(
            //Each array consists of data to render, expected XML and assert message
            array(
                array('value1', 'value2'),
                '<?xml version="1.0"?><response><item>value1</item><item>value2</item></response>',
                'Invalid XML render of unassociated array data.'
            ),
            array(
                array('key1' => 'value1', 'key2' => 'value2'),
                '<?xml version="1.0"?><response><key1>value1</key1><key2>value2</key2></response>',
                'Invalid XML render of associated array data.'
            ),
            array(
                (object)array('key' => 'value'),
                '<?xml version="1.0"?><response><key>value</key></response>',
                'Invalid XML render of object data.'
            ),
            array(
                array('7key' => 'value'),
                '<?xml version="1.0"?><response><item_7key>value</item_7key></response>',
                'Invalid XML render with numeric symbol in data index.'
            ),
            array(
                array('.key' => 'value'),
                '<?xml version="1.0"?><response><item_.key>value</item_.key></response>',
                'Invalid XML render with "." symbol in data index.'
            ),
            array(
                array('-key' => 'value'),
                '<?xml version="1.0"?><response><item_-key>value</item_-key></response>',
                'Invalid XML render with "-" symbol in data index.'
            ),
            array(
                array(' prefix key:' => 'value'),
                '<?xml version="1.0"?><response><prefix_key>value</prefix_key></response>',
                'Invalid XML render with data key trimming.'
            ),
            array(
                'data',
                '<?xml version="1.0"?><response><item>data</item></response>',
                'Invalid XML render with simple data.'
            ),
            array(
                new Magento_Object(array('key' => 'value')),
                '<?xml version="1.0"?><response><key>value</key></response>',
                'Invalid XML render with Magento_Object data.'
            ),
        );
    }
}
