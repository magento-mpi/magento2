<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     functional_test
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Test_SkipFilter_RegexpTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Test_SkipFilter_Regexp
     */
    protected $_filter;

    protected function setUp()
    {
        $this->_filter = new Mage_Test_SkipFilter_Regexp(array(
            '/test/',
            '/testA/i',
        ));
    }

    /**
     * @dataProvider getFilterData
     */
    public function testFilter($className, $result)
    {
        $this->assertEquals($result, $this->_filter->filter($className));
    }

    public static function getFilterData()
    {
        return array(
            array('test', true),
            array('TestA', true),
            array('TestB', false),
        );
    }
}