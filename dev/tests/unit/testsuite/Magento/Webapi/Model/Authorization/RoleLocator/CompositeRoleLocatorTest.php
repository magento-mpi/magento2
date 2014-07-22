<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Model\Authorization\CompositeRoleLocator;

use Magento\Framework\Authorization\RoleLocator;
use Magento\Webapi\Model\Authorization\CompositeRoleLocator;

class CompositeRoleLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CompositeRoleLocator
     */
    protected $_model;

    /**
     * @var RoleLocator[]
     */
    protected $roleLocators = [];

    protected function setUp()
    {
        $this->_model = new CompositeRoleLocator();
    }

    public function testConstructor()
    {
        $roleLocatorMock = $this->createRoleLocatorMock();
        $model = new CompositeRoleLocator([$roleLocatorMock]);
        $this->verifyRoleLocatorIsAdded($model, $roleLocatorMock);
    }

    public function testAdd()
    {
        $roleLocatorMock = $this->createRoleLocatorMock();
        $this->_model->add($roleLocatorMock);
        $this->verifyRoleLocatorIsAdded($this->_model, $roleLocatorMock);
    }

    public function testGetAclRoleId()
    {
        $expectedRoleId = 'RoleId';
        $roleLocatorMockWithRole = $this->createRoleLocatorMock($expectedRoleId);
        $roleLocatorMockWithoutRole = $this->createRoleLocatorMock('');
        $this->_model
            ->add($roleLocatorMockWithoutRole)
            ->add($roleLocatorMockWithRole)
            ->add($roleLocatorMockWithoutRole);
        $this->assertEquals($expectedRoleId, $this->_model->getAclRoleId(), 'ACL role ID is defined incorrectly.');
    }

    public function testGetAclRoleIdEmptyRoleId()
    {
        $roleLocatorMockWithoutRole = $this->createRoleLocatorMock('');
        $this->_model->add($roleLocatorMockWithoutRole);
        $this->assertEquals('', $this->_model->getAclRoleId(), 'ACL role ID is defined incorrectly.');
    }

    /**
     * @param string|null $roleId
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createRoleLocatorMock($roleId = null)
    {
        $roleLocatorMock = $this
            ->getMockBuilder('Magento\Framework\Authorization\RoleLocator')
            ->disableOriginalConstructor()->setMethods(['getAclRoleId'])->getMock();
        if (!is_null($roleId)){
            $roleLocatorMock->expects($this->once())->method('getAclRoleId')->will($this->returnValue($roleId));
        }
        return $roleLocatorMock;
    }

    /**
     * @param CompositeRoleLocator $model
     * @param RoleLocator $roleLocatorMock
     */
    protected function verifyRoleLocatorIsAdded($model, $roleLocatorMock)
    {
        $roleLocators = new \ReflectionProperty(
            'Magento\Webapi\Model\Authorization\CompositeRoleLocator',
            'roleLocators'
        );
        $roleLocators->setAccessible(true);
        $values = $roleLocators->getValue($model);
        $this->assertCount(1, $values, 'Role locator is not registered.');
        $this->assertEquals($roleLocatorMock, $values[0], 'Role locator is registered incorrectly.');
    }
}
