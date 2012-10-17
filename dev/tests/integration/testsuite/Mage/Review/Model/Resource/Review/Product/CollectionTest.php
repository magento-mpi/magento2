<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Review
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Review_Model_Resource_Review_Product_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Mage/Review/_files/different_reviews.php
     */
    public function testGetResultingIds()
    {
        $collection = new Mage_Review_Model_Resource_Review_Product_Collection();
        $collection->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED);
        $actual = $collection->getResultingIds();
        $this->assertCount(2, $actual);
    }
}
