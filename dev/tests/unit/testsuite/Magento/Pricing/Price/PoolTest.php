<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\Price;

/**
 * Test for Pool
 */
class PoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Pricing\Price\Pool
     */
    protected $pool;

    /**
     * @var array
     */
    protected $prices;

    /**
     * @var array
     */
    protected $target;

    /**
     * \Iterator
     */
    protected $targetPool;

    /**
     * Test setUp
     */
    public function setUp()
    {
        $this->prices = [
            'regular_price' => 'RegularPrice',
            'special_price' => 'SpecialPrice'
        ];
        $this->target = [
            'group_price' => 'TargetGroupPrice',
            'regular_price' => 'TargetRegularPrice'
        ];
        $this->targetPool = new Pool($this->target);
        $this->pool = new Pool($this->prices, $this->targetPool);
    }

    /**
     * test mergedConfiguration
     */
    public function testMergedConfiguration()
    {
        $expected = new Pool([
            'regular_price' => 'RegularPrice',
            'special_price' => 'SpecialPrice',
            'group_price' => 'TargetGroupPrice'
        ]);
        $this->assertEquals($expected, $this->pool);
    }

    /**
     * Test get method
     */
    public function testGet()
    {
        $this->assertEquals('RegularPrice', $this->pool->get('regular_price'));
        $this->assertEquals('SpecialPrice', $this->pool->get('special_price'));
        $this->assertEquals('TargetGroupPrice', $this->pool->get('group_price'));
    }

    /**
     * Test abilities of ArrayAccess method
     */
    public function testArrayAccess()
    {
        $this->assertEquals('RegularPrice', $this->pool['regular_price']);
        $this->assertEquals('SpecialPrice', $this->pool['special_price']);
        $this->assertEquals('TargetGroupPrice', $this->pool['group_price']);
    }
}
