<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_PageCache
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\PageCache\Model\Indexer;

/**
 * Class ContextTest
 */
class ContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\PageCache\Model\Indexer\Context
     */
    protected $context;

    /**
     * Set up test
     */
    public function setUp()
    {
        $this->context = new \Magento\PageCache\Model\Indexer\Context();
    }

    /**
     * Test registerEntities
     */
    public function testRegisterEntities()
    {
        $cacheTag = 'tag';
        $expectedIds = array(1, 2, 3);
        $this->context->registerEntities($cacheTag, $expectedIds);
        $actualIds = $this->context->getRegisteredEntity($cacheTag);
        $this->assertEquals($expectedIds, $actualIds);
    }

    /**
     * test getIdentities
     */
    public function testGetIdentities()
    {
        $expectedIdentities = array(
            'product_1', 'product_2', 'product_3', 'category_5', 'category_6', 'category_7'
        );
        $productTag = 'product';
        $categoryTag = 'category';
        $productIds = array(1, 2, 3);
        $categoryIds = array(5, 6, 7);
        $this->context->registerEntities($productTag, $productIds);
        $this->context->registerEntities($categoryTag, $categoryIds);
        $actualIdentities = $this->context->getIdentities();
        $this->assertEquals($expectedIdentities, $actualIdentities);
    }
}
