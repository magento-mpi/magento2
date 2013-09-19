<?php
/**
 * Test class for \Magento\Acl\ResourceFactory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Acl;

class ResourceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Acl\ResourceFactory
     */
    protected $_model;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Acl\Resource
     */
    protected $_expectedObject;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_objectManager = $this->getMockForAbstractClass('Magento\ObjectManager', array(), '', true, true, true,
            array('create'));

        $this->_expectedObject = $this->getMock('Magento\Acl\Resource', array(), array(), '', false);

        $this->_model = $helper->getObject('Magento\Acl\ResourceFactory', array(
            'objectManager' => $this->_objectManager,
        ));
    }

    public function testCreateResource()
    {
        $arguments = array('5', '6');
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('Magento\Acl\Resource', $arguments)
            ->will($this->returnValue($this->_expectedObject));
        $this->assertEquals($this->_expectedObject, $this->_model->createResource($arguments));
    }
}
