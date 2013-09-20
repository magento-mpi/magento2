<?php
/**
 * Test for \Magento\Webapi\Model\Acl\Rule model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @magentoDataFixture Magento/Webapi/_files/role.php
 */
namespace Magento\Webapi\Model\Acl;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Webapi\Model\Acl\Role\Factory
     */
    protected $_roleFactory;

    /**
     * @var \Magento\Webapi\Model\Acl\Rule
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_roleFactory = $this->_objectManager->get('Magento\Webapi\Model\Acl\Role\Factory');
        $this->_model = $this->_objectManager->create('Magento\Webapi\Model\Acl\Rule');
    }

    /**
     * Test Web API Role CRUD.
     */
    public function testCRUD()
    {
        $role = $this->_roleFactory->create()->load('test_role', 'role_name');
        $allowResourceId = 'customer/multiGet';

        $this->_model->setRoleId($role->getId())
            ->setResourceId($allowResourceId);

        $crud = new \Magento\TestFramework\Entity($this->_model, array('resource_id' => 'customer/get'));
        $crud->testCrud();
    }

    /**
     * Test \Magento\Webapi\Model\Acl\Rule::saveResources() method.
     */
    public function testSaveResources()
    {
        $role = $this->_roleFactory->create()->load('test_role', 'role_name');
        $resources = array('customer/create', 'customer/update');

        $this->_model
            ->setRoleId($role->getId())
            ->setResources($resources)
            ->saveResources();

        /** @var $rulesSet \Magento\Webapi\Model\Resource\Acl\Rule\Collection */
        $rulesSet = $this->_objectManager->get('Magento\Webapi\Model\Resource\Acl\Rule\Collection')
            ->getByRole($role->getRoleId())->load();
        $this->assertCount(2, $rulesSet);
    }
}
