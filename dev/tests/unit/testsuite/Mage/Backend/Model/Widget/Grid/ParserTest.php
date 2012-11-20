<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Widget_Grid_ParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Widget_Grid_Parser
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Backend_Model_Widget_Grid_Parser();
    }

    /**
     * @param string $expression
     * @param array $expected
     * @dataProvider parseExpressionDataProvider
     */
    public function testParseExpression($expression, $expected)
    {
        $this->assertEquals($expected, $this->_model->parseExpression($expression));
    }

    /**
     * @return array
     */
    public function parseExpressionDataProvider()
    {
        return array(
            array(
                '1+2',
                array('1', '2', '+')
            ),
            array(
                '1-2',
                array('1', '2', '-')
            ),
            array(
                '1*2',
                array('1', '2', '*')
            ),
            array(
                '1/2',
                array('1', '2', '/')
            )
        );
    }
}
