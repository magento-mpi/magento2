<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Cache_Frontend_Decorator_TagMarkerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Cache_Frontend_Decorator_TagMarker
     */
    protected $_object;

    /**
     * @var Magento_Cache_FrontendInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_frontend;

    /**
     * @var string
     */
    protected $_tagForTests = 'custom_tag';

    public function setUp()
    {
        $this->_frontend = $this->getMock('Magento_Cache_FrontendInterface');
        $this->_object = new Magento_Cache_Frontend_Decorator_TagMarker($this->_frontend, $this->_tagForTests);
    }

    public function testGetTag()
    {
        $this->assertEquals($this->_tagForTests, $this->_object->getTag());
    }

    public function testSave()
    {
        $this->_frontend->expects($this->once())
            ->method('save')
            ->with('record_value', 'record_id', array('passed_tag', $this->_tagForTests), 111)
            ->will($this->returnValue(true));

        $result = $this->_object->save('record_value', 'record_id', array('passed_tag'), 111);
        $this->assertTrue($result);
    }
}
