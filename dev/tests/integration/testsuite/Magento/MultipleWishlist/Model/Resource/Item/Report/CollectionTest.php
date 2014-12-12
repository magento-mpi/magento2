<?php
/**
 * \Magento\MultipleWishlist\Model\Resource\Item\Report\Collection
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
        $this->_collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\MultipleWishlist\Model\Resource\Item\Report\Collection'
        );
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
