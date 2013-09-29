<?php
/**
 * Test class for \Magento\Webapi\Model\Acl\User\Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Acl\User;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Webapi\Model\Acl\User\Factory
     */
    protected $_model;

    /**
     * @var \Magento\ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var \Magento\Webapi\Model\Acl\User
     */
    protected $_expectedObject;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_objectManager = $this->getMockBuilder('Magento\ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMockForAbstractClass();

        $this->_expectedObject = $this->getMockBuilder('Magento\Webapi\Model\Acl\User')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_model = $helper->getObject('Magento\Webapi\Model\Acl\User\Factory', array(
            'objectManager' => $this->_objectManager,
        ));
    }

    /**
     * Test create method.
     */
    public function testCreate()
    {
        $arguments = array('property' => 'value');
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('Magento\Webapi\Model\Acl\User', $arguments)
            ->will($this->returnValue($this->_expectedObject));
        $this->assertEquals($this->_expectedObject, $this->_model->create($arguments));
    }
}
