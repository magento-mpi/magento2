<?php
/**
 * \Magento\MultipleWishlist\Model\Resource\Item\Report\Collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Model\Resource\Item\Report;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\MultipleWishlist\Model\Resource\Item\Report\Collection
     */
    protected $_collection;

    public function setUp()
    {
        $this->_collection = \Mage::getResourceModel('Magento\MultipleWishlist\Model\Resource\Item\Report\Collection');
    }

    public function testAddCustomerInfo()
    {
        $joinParts = $this->_collection->getSelect()->getPart(\Zend_Db_Select::FROM);
        $this->assertArrayHasKey('at_prefix', $joinParts);
        $this->assertArrayHasKey('at_firstname', $joinParts);
        $this->assertArrayHasKey('at_middlename', $joinParts);
        $this->assertArrayHasKey('at_lastname', $joinParts);
        $this->assertArrayHasKey('at_suffix', $joinParts);
    }
}
