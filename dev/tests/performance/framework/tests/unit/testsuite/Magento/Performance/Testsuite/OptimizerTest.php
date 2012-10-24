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
     * @param array $scenarios
     * @param array $expectedScenarios
     * @dataProvider runDataProvider
     */
    public function testRun($scenarios, $expectedScenarios)
    {
        $actualScenarios = $this->_optimizer->run($scenarios);
        $this->assertEquals($actualScenarios, $expectedScenarios);
    }

    /**
     * @return array
     */
    public function runDataProvider()
    {
        return array(
            'empty_list' => array(
                'scenarios' => array(),
                'expectedScenarios' => array(),
            ),
            'single_scenario' => array(
                'scenarios' => array(
                    'a' => array('f1', 'f2')
                ),
                'expectedScenarios' => array('a'),
            ),
            'empty_fixtures' => array(
                'scenarios' => array(
                    'a' => array(),
                    'b' => array()
                ),
                'expectedScenarios' => array('a', 'b'),
            ),
            'from_smaller_to_bigger' => array(
                'scenarios' => array(
                    'a' => array('f1', 'f2'),
                    'b' => array('f2'),
                    'c' => array('f3')
                ),
                'expectedScenarios' => array('b', 'a', 'c'),
            ),
            'same_together' => array(
                'scenarios' => array(
                    'a' => array('f1', 'f2'),
                    'b' => array('f1'),
                    'c' => array('f1'),
                ),
                'expectedScenarios' => array('b', 'c', 'a'),
            )
        );
    }
}
