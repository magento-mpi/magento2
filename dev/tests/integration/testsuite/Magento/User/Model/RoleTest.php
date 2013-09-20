<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
namespace Magento\User\Model;

class RoleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\User\Model\Role
     */
    protected $_model = null;

    protected function setUp()
    {
        $this->_model = \Mage::getModel('Magento\User\Model\Role');
    }

    public function testGetUsersCollection()
    {
        $this->assertInstanceOf('Magento\User\Model\Resource\Role\User\Collection',
            $this->_model->getUsersCollection());
    }

    public function testGetRoleUsers()
    {
        $this->assertEmpty($this->_model->getRoleUsers());

        $this->_model->load(\Magento\TestFramework\Bootstrap::ADMIN_ROLE_NAME, 'role_name');
        $this->assertNotEmpty($this->_model->getRoleUsers());
    }
}
