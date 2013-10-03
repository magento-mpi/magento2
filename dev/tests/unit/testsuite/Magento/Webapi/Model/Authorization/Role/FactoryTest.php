<?php
/**
 * Test class for \Magento\Webapi\Model\Authorization\Role\Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Authorization\Role;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Webapi\Model\Authorization\Role\Factory
     */
    protected $_model;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Webapi\Model\Authorization\Role
     */
    protected $_expectedObject;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_objectManager = $this->getMockForAbstractClass('Magento\ObjectManager', array(), '',
            true, true, true, array('create'));

        $this->_expectedObject = $this->getMock('Magento\Webapi\Model\Authorization\Role', array(), array(), '', false);

        $this->_model = $helper->getObject('Magento\Webapi\Model\Authorization\Role\Factory', array(
            'objectManager' => $this->_objectManager,
        ));
    }

    public function testCreateRole()
    {
        $arguments = array('5', '6');

        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('Magento\Webapi\Model\Authorization\Role', $arguments)
            ->will($this->returnValue($this->_expectedObject));
        $this->assertEquals($this->_expectedObject, $this->_model->createRole($arguments));
    }
}
