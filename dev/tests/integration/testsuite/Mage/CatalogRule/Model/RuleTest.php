<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CatalogRule
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_CatalogRule_Model_RuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_CatalogRule_Model_Rule
     */
    protected $_object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_object = Mage::getModel('Mage_CatalogRule_Model_Rule');
    }

    protected function tearDown()
    {
        $this->_object = null;
    }

    /**
     * @magentoAppIsolation enabled
     * @covers Mage_CatalogRule_Model_Rule::calcProductPriceRule
     */
    public function testCalcProductPriceRule()
    {
        /** @var $catalogRule Mage_CatalogRule_Model_Rule */
        $catalogRule = $this->getMock('Mage_CatalogRule_Model_Rule', array('_getRulesFromProduct'), array(), '', false);
        $catalogRule->expects(self::any())
            ->method('_getRulesFromProduct')
            ->will($this->returnValue($this->_getCatalogRulesFixtures()));

        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $this->assertEquals($catalogRule->calcProductPriceRule($product, 100), 45);
        $product->setParentId(true);
        $this->assertEquals($catalogRule->calcProductPriceRule($product, 50), 5);
    }

    /**
     * Get array with catalog rule data
     *
     * @return array
     */
    protected function _getCatalogRulesFixtures()
    {
        return array(
            array(
                'action_operator' => 'by_percent',
                'action_amount' => '10.0000',
                'sub_simple_action' => 'by_percent',
                'sub_discount_amount' => '90.0000',
                'action_stop' => '0',
            ),
            array(
                'action_operator' => 'by_percent',
                'action_amount' => '50.0000',
                'sub_simple_action' => '',
                'sub_discount_amount' => '0.0000',
                'action_stop' => '0',
            ),
        );
    }
}
