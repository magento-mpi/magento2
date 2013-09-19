<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Performance\Testsuite;

class OptimizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Performance\Testsuite\Optimizer
     */
    protected $_optimizer;

    protected function setUp()
    {
        $this->_optimizer = new \Magento\TestFramework\Performance\Testsuite\Optimizer;
    }

    protected function tearDown()
    {
        unset($this->_optimizer);
    }

    /**
     * @param array $fixtureSets
     * @param array $expected
     * @dataProvider optimizeFixtureSetsDataProvider
     */
    public function testOptimizeFixtureSets($fixtureSets, $expected)
    {
        $optimized = $this->_optimizer->optimizeFixtureSets($fixtureSets);
        $this->assertEquals($optimized, $expected);
    }

    /**
     * @return array
     */
    public function optimizeFixtureSetsDataProvider()
    {
        return array(
            'empty_list' => array(
                'fixtureSets' => array(),
                'expected' => array(),
            ),
            'single_scenario' => array(
                'fixtureSets' => array(
                    'a' => array('f1', 'f2')
                ),
                'expected' => array('a'),
            ),
            'empty_fixtures' => array(
                'fixtureSets' => array(
                    'a' => array(),
                    'b' => array()
                ),
                'expected' => array('a', 'b'),
            ),
            'from_smaller_to_bigger' => array(
                'fixtureSets' => array(
                    'a' => array('f1', 'f2'),
                    'b' => array('f2'),
                    'c' => array('f3')
                ),
                'expected' => array('b', 'a', 'c'),
            ),
            'same_together' => array(
                'fixtureSets' => array(
                    'a' => array('f1', 'f2'),
                    'b' => array('f1'),
                    'c' => array('f1'),
                ),
                'expected' => array('b', 'c', 'a'),
            )
        );
    }
}
