<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Model\Resource\Review\Product;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Review/_files/different_reviews.php
     */
    public function testGetResultingIds()
    {
        $collection = \Mage::getResourceModel('Magento\Review\Model\Resource\Review\Product\Collection');
        $collection->addStatusFilter(\Magento\Review\Model\Review::STATUS_APPROVED);
        $actual = $collection->getResultingIds();
        $this->assertCount(2, $actual);
    }
}
