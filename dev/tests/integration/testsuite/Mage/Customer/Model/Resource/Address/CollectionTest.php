<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for customer addresses collection
 *
 * @group module:Mage_Customer
 */
class Mage_Customer_Model_Resource_Address_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Customer_Model_Resource_Address_Collection
     */
    protected $_collection;

    public function setUp()
    {
        $this->_collection = new Mage_Customer_Model_Resource_Address_Collection();
    }

    /**
     * Test setCustomerFilter() using empty object as possible filter
     */
    public function testSetEmptyObjectAsCustomerFilter()
    {
        $this->_collection->setCustomerFilter(new Varien_Object());
        $this->assertContains("`parent_id` = '-1'", (string) $this->_collection->getSelect());
    }

    /**
     * Test setCustomerFilter() using object with existing Id as possible filter
     */
    public function testSetCorrectObjectAsCustomerFilter()
    {
        $customer = new Varien_Object(array('id' => 10));
        $this->_collection->setCustomerFilter($customer);
        $this->assertContains("`parent_id` = '10'", (string) $this->_collection->getSelect());
    }

    /**
     * Test setCustomerFilter() using array of Ids as possible filter
     */
    public function testSetArrayAsCustomerFilter()
    {
        $this->_collection->setCustomerFilter(array(1, 2));
        $this->assertContains('`parent_id` IN', (string) $this->_collection->getSelect());
    }
}
