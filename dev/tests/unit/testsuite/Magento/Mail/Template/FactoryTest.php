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
     * @var \Magento\Mail\Template\Factory
     */
    protected $_factory;

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

        $this->_factory = new \Magento\Mail\Template\Factory(
            $this->_objectManagerMock, '\Magento\Email\Model\Template'
        );
    }

    /**
     * @covers \Magento\Mail\Template\Factory::get
     */
    public function testGet()
    {
        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with('\Magento\Email\Model\Template', array('data' => array('template_id' => 'identifier')))
            ->will($this->returnValue($this->_templateMock));

        $this->assertInstanceOf('\Magento\Mail\TemplateInterface', $this->_factory->get('identifier'));
    }
}
