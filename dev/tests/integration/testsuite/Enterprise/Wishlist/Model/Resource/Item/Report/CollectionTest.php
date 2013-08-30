<?php
/**
 * Enterprise_Wishlist_Model_Resource_Item_Report_Collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Wishlist_Model_Resource_Item_Report_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Wishlist_Model_Resource_Item_Report_Collection
     */
    protected $_collection;

    public function setUp()
    {
        $this->_collection = Mage::getResourceModel('Enterprise_Wishlist_Model_Resource_Item_Report_Collection');
    }

    public function testAddCustomerInfo()
    {
        $joinParts = $this->_collection->getSelect()->getPart(Zend_Db_Select::FROM);
        $this->assertArrayHasKey('at_prefix', $joinParts);
        $this->assertArrayHasKey('at_firstname', $joinParts);
        $this->assertArrayHasKey('at_middlename', $joinParts);
        $this->assertArrayHasKey('at_lastname', $joinParts);
        $this->assertArrayHasKey('at_suffix', $joinParts);
    }
}