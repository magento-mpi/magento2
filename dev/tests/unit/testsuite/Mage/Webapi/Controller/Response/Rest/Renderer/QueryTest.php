<?php
/**
 * Test Query Renderer for REST.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Response_Rest_Renderer_QueryTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Controller_Response_Rest_Renderer_Query */
    protected $_restQueryRenderer;

    protected function setUp()
    {
        $this->_restQueryRenderer = new Mage_Webapi_Controller_Response_Rest_Renderer_Query();
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_restQueryRenderer);
        parent::tearDown();
    }

    /**
     * Test Render method
     *
     * @dataProvider providerQueryRender
     */
    public function testRender($dataToRender, $expectedData)
    {
        $this->assertEquals($expectedData, $this->_restQueryRenderer->render($dataToRender), 'Wrong rendering.');
    }

    /**
     * Test GetMimeType method
     */
    public function testGetMimeType()
    {
        $expectedMimeType = 'text/plain';
        $this->assertEquals($expectedMimeType, $this->_restQueryRenderer->getMimeType(), 'Unexpected mime type.');
    }

    public function providerQueryRender()
    {
        return array(
            array(array('key 1' => 'value 1'), 'key+1=value+1'),
            array((object)array('key 1' => array('data')), 'key+1%5B0%5D=data')
        );
    }
}
