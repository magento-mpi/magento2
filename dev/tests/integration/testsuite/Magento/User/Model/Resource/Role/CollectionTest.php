<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Role collection test
 * @magentoAppArea adminhtml
 */
class Magento_User_Model_Resource_Role_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_User_Model_Resource_Role_Collection
     */
    protected $_collection;

    protected function setUp()
    {
        $this->_collection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_User_Model_Resource_Role_Collection');
    }

    public function testSetUserFilter()
    {
        $user = Mage::getModel('Magento_User_Model_User');
        $user->loadByUsername(Magento_TestFramework_Bootstrap::ADMIN_NAME);
        $this->_collection->setUserFilter($user->getId());

        $selectQueryStr = $this->_collection->getSelect()->__toString();

        $this->assertContains('user_id', $selectQueryStr);
        $this->assertContains('role_type', $selectQueryStr);
    }

    public function testSetRolesFilter()
    {
        $this->_collection->setRolesFilter();

        $this->assertContains('role_type', $this->_collection->getSelect()->__toString());
    }

    public function testToOptionArray()
    {
        $this->assertNotEmpty($this->_collection->toOptionArray());

        foreach ($this->_collection->toOptionArray() as $item) {
            $this->assertArrayHasKey('value', $item);
            $this->assertArrayHasKey('label', $item);
        }
    }
}
