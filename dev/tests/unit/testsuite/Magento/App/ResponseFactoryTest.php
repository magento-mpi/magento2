<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

class ResponseFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\ResponseFactory
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var \Magento\App\ResponseInterface
     */
    protected $_expectedObject;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager');
        $this->_model = new \Magento\App\ResponseFactory($this->_objectManagerMock);
    }

    public function testCreate()
    {
        $this->_expectedObject = $this->getMockBuilder('\Magento\App\ResponseInterface')->getMock();
        $arguments = array(array('property' => 'value'));
        $this->_objectManagerMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            'Magento\App\ResponseInterface',
            $arguments
        )->will(
            $this->returnValue($this->_expectedObject)
        );

        $this->assertEquals($this->_expectedObject, $this->_model->create($arguments));
    }
}
