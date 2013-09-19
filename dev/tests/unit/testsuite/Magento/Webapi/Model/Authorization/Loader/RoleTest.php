<?php
/**
 * Test class for \Magento\Webapi\Model\Authorization\Loader\Role
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Authorization\Loader;

class RoleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Webapi\Model\Resource\Acl\Role
     */
    protected $_resourceModelMock;

    /**
     * @var \Magento\Webapi\Model\Authorization\Loader\Role
     */
    protected $_model;

    /**
     * @var \Magento\Webapi\Model\Authorization\Role\Factory
     */
    protected $_roleFactory;

    /**
     * @var \Magento\Acl
     */
    protected $_acl;

    /**
     * Set up before test.
     */
    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_resourceModelMock = $this->getMock('Magento\Webapi\Model\Resource\Acl\Role',
            array('getRolesIds'), array(), '', false);

        $this->_roleFactory = $this->getMock('Magento\Webapi\Model\Authorization\Role\Factory',
            array('createRole'), array(), '', false);

        $this->_acl = $this->getMock('Magento\Acl', array('addRole', 'deny'), array(), '',
            false);

        $this->_model = $helper->getObject('Magento\Webapi\Model\Authorization\Loader\Role', array(
            'roleResource' => $this->_resourceModelMock,
            'roleFactory' => $this->_roleFactory,
        ));
    }

    /**
     * Test for \Magento\Webapi\Model\Authorization\Loader\Role::populateAcl.
     *
     * Test with existing role IDs.
     */
    public function testPopulateAclWithRoles()
    {
        $roleOne = new \Magento\Webapi\Model\Authorization\Role(3);
        $roleTwo = new \Magento\Webapi\Model\Authorization\Role(4);
        $roleIds = array(3, 4);
        $createRoleMap = array(
            array(array('roleId' => 3), $roleOne),
            array(array('roleId' => 4), $roleTwo),
        );
        $this->_resourceModelMock->expects($this->once())
            ->method('getRolesIds')
            ->will($this->returnValue($roleIds));

        $this->_roleFactory->expects($this->exactly(count($roleIds)))
            ->method('createRole')
            ->will($this->returnValueMap($createRoleMap));

        $this->_acl->expects($this->exactly(count($roleIds)))
            ->method('addRole')
            ->with($this->logicalOr($roleOne, $roleTwo));

        $this->_acl->expects($this->exactly(count($roleIds)))
            ->method('deny')
            ->with($this->logicalOr($roleOne, $roleTwo));

        $this->_model->populateAcl($this->_acl);
    }

    /**
     * Test for \Magento\Webapi\Model\Authorization\Loader\Role::populateAcl.
     *
     * Test with No existing role IDs.
     */
    public function testPopulateAclWithNoRoles()
    {
        $this->_resourceModelMock->expects($this->once())
            ->method('getRolesIds')
            ->will($this->returnValue(array()));

        $this->_roleFactory->expects($this->never())
            ->method('createRole');

        $this->_acl->expects($this->never())
            ->method('addRole');

        $this->_acl->expects($this->never())
            ->method('deny');

        $this->_model->populateAcl($this->_acl);
    }
}
