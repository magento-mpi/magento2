<?php
namespace Magento\Webapi\Model\Acl;

/**
 * Test for \Magento\Webapi\Model\Acl\Role model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @magentoDataFixture Magento/Webapi/_files/role.php
 */
class RoleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Webapi\Model\Acl\Role
     */
    protected $_model;

    /**
     * Initialize model.
     */
    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_model = $this->_objectManager->create('Magento\Webapi\Model\Acl\Role');
    }

    /**
     * Test Web API Role CRUD.
     */
    public function testCRUD()
    {
        $this->_model->setRoleName('Test Role Name');
        $crud = new \Magento\TestFramework\Entity($this->_model, array('role_name' => '_Role_Name_'));
        $crud->testCrud();
    }
}
