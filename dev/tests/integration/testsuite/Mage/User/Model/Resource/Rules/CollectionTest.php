<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_User
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_User
 */
class Mage_User_Model_Resource_Rules_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_User_Model_Resource_Rules_Collection
     */
    protected $_collection;

    protected function setUp()
    {
        $this->_collection = new Mage_User_Model_Resource_Rules_Collection();
    }

    protected function tearDown()
    {
        $this->_collection = null;
    }

    public function testGetByRoles()
    {
        $user = new Mage_User_Model_User;
        $user->loadByUsername(Magento_Test_Bootstrap::ADMIN_NAME);
        $this->_collection->getByRoles($user->getRole()->getId());

        $where = $this->_collection->getSelect()->getPart(Zend_Db_Select::WHERE);
        $this->assertContains("(`role_id` = '" . $user->getRole()->getId()."')", $where);
    }

    public function testAddSortByLength()
    {
        $this->_collection->addSortByLength();

        $order = $this->_collection->getSelect()->getPart(Zend_Db_Select::ORDER);
        $this->assertContains(array('length', 'DESC'), $order);
    }
}
