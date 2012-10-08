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

class Magento_Performance_Testsuite_OptimizerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Performance_Testsuite_Optimizer
     */
    protected $_optimizer;

    protected function setUp()
    {
        $this->_optimizer = new Magento_Performance_Testsuite_Optimizer;
    }

    protected function tearDown()
    {
        unset($this->_optimizer);
    }

    /**
     * @param array $scenarioFixtures
     * @param array $expected
     * @dataProvider runDataProvider
     */
    public function testOptimizeScenarios($scenarioFixtures, $expected)
    {
        $actualScenarios = $this->_optimizer->optimizeScenarios($scenarioFixtures);
        $this->assertEquals($actualScenarios, $expected);
    }

    /**
     * @return array
     */
    public function runDataProvider()
    {
        return array(
            'empty_list' => array(
                'scenarioFixtures' => array(),
                'expected' => array(),
            ),
            'single_scenario' => array(
                'scenarioFixtures' => array(
                    'a' => array('f1', 'f2')
                ),
                'expected' => array('a'),
            ),
            'empty_fixtures' => array(
                'scenarioFixtures' => array(
                    'a' => array(),
                    'b' => array()
                ),
                'expected' => array('a', 'b'),
            ),
            'from_smaller_to_bigger' => array(
                'scenarioFixtures' => array(
                    'a' => array('f1', 'f2'),
                    'b' => array('f2'),
                    'c' => array('f3')
                ),
                'expected' => array('b', 'a', 'c'),
            ),
            'same_together' => array(
                'scenarioFixtures' => array(
                    'a' => array('f1', 'f2'),
                    'b' => array('f1'),
                    'c' => array('f1'),
                ),
                'expected' => array('b', 'c', 'a'),
            )
        );
    }
}
