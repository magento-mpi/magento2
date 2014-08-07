<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authorization\Model;

/**
 * @magentoAppArea adminhtml
 */
class RoleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Authorization\Model\Role
     */
    protected $_model = null;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Authorization\Model\Role');
    }

    public function testGetRoleUsers()
    {
        $this->assertEmpty($this->_model->getRoleUsers());

        $this->_model->load(\Magento\TestFramework\Bootstrap::ADMIN_ROLE_NAME, 'role_name');
        $this->assertNotEmpty($this->_model->getRoleUsers());
    }
}
