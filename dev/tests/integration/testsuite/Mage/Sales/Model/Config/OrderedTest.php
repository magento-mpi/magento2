<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Sales_Model_Config_OrderedTest extends PHPUnit_Framework_TestCase
{
    /**
     * Whether it's necessary to re-enable config cache upon a test completion
     *
     * @var bool
     */
    protected $_restoreUseCache = false;

    /**
     * @var Mage_Sales_Model_Config_Ordered
     */
    protected $_model;

    /**
     * Disable configuration cache
     */
    protected function setUp()
    {
        $this->_restoreUseCache = Mage::app()->useCache('config');
        Mage::app()->getCacheInstance()->banUse('config');
        $this->_model = $this->getMockForAbstractClass('Mage_Sales_Model_Config_Ordered');
    }

    /**
     * Restore config cache usage
     */
    protected function tearDown()
    {
        $this->_model = null;
        if ($this->_restoreUseCache) {
            Mage::app()->getCacheInstance()->allowUse('config');
        }
    }

    /**
     * @dataProvider getSortedCollectorCodesDataProvider
     */
    public function testGetSortedCollectorCodes($totalConfig, $expectedResult)
    {
        $method = new ReflectionMethod($this->_model, '_getSortedCollectorCodes');
        $method->setAccessible(true);
        $actualResult = $method->invoke($this->_model, $totalConfig);
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function getSortedCollectorCodesDataProvider()
    {
        return array(
            'core totals' => array(
                require __DIR__ . '/_files/core_totals_config.php',
                array(
                    'nominal', 'subtotal', 'freeshipping', 'tax_subtotal', 'shipping', 'tax_shipping', 'discount',
                    'tax', 'grand_total', 'msrp', 'wee',
                )
            ),
            'custom totals' => array(
                require __DIR__ . '/_files/custom_totals_config.php',
                array(
                    'nominal', 'own_subtotal', 'own_total1', 'own_total2', 'subtotal', 'freeshipping', 'tax_subtotal',
                    'shipping', 'tax_shipping', 'discount', 'handling', 'handling_tax', 'tax', 'grand_total', 'msrp',
                    'wee',
                )
            ),
            '"before" ambiguity 1' => array(
                array(
                    'total_one' => array('after' => array(), 'before' => array('total_two')),
                    'total_two' => array('after' => array(), 'before' => array('total_one')),
                ),
                array('total_one', 'total_two'),
            ),
            '"before" ambiguity 2' => array(
                array(
                    'total_two' => array('after' => array(), 'before' => array('total_one')),
                    'total_one' => array('after' => array(), 'before' => array('total_two')),
                ),
                array('total_two', 'total_one'),
            ),
            '"after" ambiguity 1' => array(
                array(
                    'total_one' => array('before' => array('total_two'), 'after' => array()),
                    'total_two' => array('before' => array('total_one'), 'after' => array()),
                ),
                array('total_one', 'total_two'),
            ),
            '"after" ambiguity 2' => array(
                array(
                    'total_two' => array('before' => array('total_one'), 'after' => array()),
                    'total_one' => array('before' => array('total_two'), 'after' => array()),
                ),
                array('total_two', 'total_one'),
            ),
        );
    }
}
