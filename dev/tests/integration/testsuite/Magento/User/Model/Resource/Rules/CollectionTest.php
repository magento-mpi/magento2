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
class Magento_User_Model_Resource_Rules_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\User\Model\Resource\Rules\Collection
     */
    protected $_collection;

    protected function setUp()
    {
        $this->_collection = Mage::getResourceModel('Magento\User\Model\Resource\Rules\Collection');
    }

    public function testGetByRoles()
    {
        $user = Mage::getModel('Magento\User\Model\User');
        $user->loadByUsername(Magento_TestFramework_Bootstrap::ADMIN_NAME);
        $this->_collection->getByRoles($user->getRole()->getId());

        $where = $this->_collection->getSelect()->getPart(Zend_Db_Select::WHERE);
        /** @var Zend_Db_Adapter_Abstract $adapter */
        $adapter = $this->_collection->getConnection();
        $quote = $adapter->getQuoteIdentifierSymbol();
        $this->assertContains("({$quote}role_id{$quote} = '" . $user->getRole()->getId()."')", $where);
    }

    public function testAddSortByLength()
    {
        $this->_collection->addSortByLength();

        $order = $this->_collection->getSelect()->getPart(Zend_Db_Select::ORDER);
        $this->assertContains(array('length', 'DESC'), $order);
    }
}
