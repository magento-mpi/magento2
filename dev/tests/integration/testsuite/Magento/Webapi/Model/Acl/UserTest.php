<?php
/**
 * Test for \Magento\Webapi\Model\Acl\User model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @magentoDataFixture Magento/Webapi/_files/role.php
 */
namespace Magento\Webapi\Model\Acl;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Webapi\Model\Acl\User
     */
    protected $_model;

    /**
     * @var \Magento\Webapi\Model\Acl\Role\Factory
     */
    protected $_roleFactory;

    /**
     * Initialize model.
     */
    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_roleFactory = $this->_objectManager->get('Magento\Webapi\Model\Acl\Role\Factory');
        $this->_model = $this->_objectManager->create('Magento\Webapi\Model\Acl\User');
    }

    /**
     * Test Web API User CRUD.
     */
    public function testCRUD()
    {
        $role = $this->_roleFactory->create()->load('test_role', 'role_name');

        $this->_model
            ->setApiKey('Test User Name')
            ->setContactEmail('null@null.com')
            ->setSecret('null@null.com')
            ->setRoleId($role->getId());

        $crud = new \Magento\TestFramework\Entity($this->_model, array('api_key' => '_User_Name_'));
        $crud->testCrud();
    }
}
