<?php
/**
 * Magento_Customer_Model_Resource_Customer_Collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Customer_Model_Resource_Customer_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Customer_Model_Resource_Customer_Collection
     */
    protected $_collection;

    public function setUp()
    {
        $this->_collection = Mage::getResourceModel('Magento_Customer_Model_Resource_Customer_Collection');
    }

    public function testAddNameToSelect()
    {
        $this->_collection->addNameToSelect();
        $joinParts = $this->_collection->getSelect()->getPart(Zend_Db_Select::FROM);

        $this->assertArrayHasKey('at_prefix', $joinParts);
        $this->assertArrayHasKey('at_firstname', $joinParts);
        $this->assertArrayHasKey('at_middlename', $joinParts);
        $this->assertArrayHasKey('at_lastname', $joinParts);
        $this->assertArrayHasKey('at_suffix', $joinParts);
    }
}