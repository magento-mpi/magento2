<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Mail\Template;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject
     */
    protected $_templateMock;

    public function setUp()
    {
        $this->_objectManagerMock = $this->getMock('\Magento\ObjectManager');
        $this->_templateMock = $this->getMock('\Magento\Mail\TemplateInterface');
    }

    /**
     * @covers \Magento\Mail\Template\Factory::get
     * @covers \Magento\Mail\Template\Factory::__construct
     */
    public function testGet()
    {
        $model = new \Magento\Mail\Template\Factory($this->_objectManagerMock);

        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento\Mail\TemplateInterface', array('data' => array('template_id' => 'identifier')))
            ->will($this->returnValue($this->_templateMock));

        $this->assertInstanceOf('\Magento\Mail\TemplateInterface', $model->get('identifier'));
    }
}
