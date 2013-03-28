<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_App_StateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_App_State
     */
    protected $_model;

    /**
     * @param string $mode
     * @dataProvider constructorDataProvider
     */
    public function testConstructor($mode)
    {
        $model = new Mage_Core_Model_App_State($mode);
        $this->assertEquals($mode, $model->getMode());
    }

    /**
     * @return array
     */
    public static function constructorDataProvider()
    {
        return array(
            'default mode' => array(
                Mage_Core_Model_App_State::MODE_DEFAULT
            ),
            'production mode' => array(
                Mage_Core_Model_App_State::MODE_PRODUCTION
            ),
            'developer mode' => array(
                Mage_Core_Model_App_State::MODE_DEVELOPER
            ),
        );
    }

    /**
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Unknown application mode: unknown mode
     */
    public function testConstructorException()
    {
        new Mage_Core_Model_App_State("unknown mode");
    }
}
