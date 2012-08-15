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
     * Test getResultingIds
     * 1) check that filter was applied
     * 2) check that elements are ordered correctly
     *
     * @magentoDataFixtureDisabled Mage/Review/_files/different_reviews.php
     */
    public function testGetResultingIds()
    {
        $this->markTestIncomplete('Bug MAGETWO-2595');
        $collection = new Mage_Review_Model_Resource_Review_Product_Collection();
        $collection->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
            ->setOrder('rdt.title', Mage_Review_Model_Resource_Review_Product_Collection::SORT_ORDER_ASC);
        $actual = $collection->getResultingIds();
        $this->assertCount(2, $actual);
        $this->assertLessThan($actual[0], $actual[1]);
    }
}
