<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Role user collection test
 * @magentoAppArea adminhtml
 */
class Mage_User_Model_Resource_Role_User_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_User_Model_Resource_Role_User_Collection
     */
    protected $_collection;

    protected function setUp()
    {
        $this->_collection = Mage::getResourceModel('Mage_User_Model_Resource_Role_User_Collection');
    }

    public function testSelectQueryInitialized()
    {
        $this->assertContains('user_id > 0', $this->_collection->getSelect()->__toString());
    }
}
