<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_App_StateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\App\State
     */
    protected $_model;

    /**
     * @param string $mode
     * @dataProvider constructorDataProvider
     */
    public function testConstructor($mode)
    {
        $model = new \Magento\Core\Model\App\State($mode);
        $this->assertEquals($mode, $model->getMode());
    }

    /**
     * @return array
     */
    public static function constructorDataProvider()
    {
        return array(
            'default mode' => array(
                \Magento\Core\Model\App\State::MODE_DEFAULT
            ),
            'production mode' => array(
                \Magento\Core\Model\App\State::MODE_PRODUCTION
            ),
            'developer mode' => array(
                \Magento\Core\Model\App\State::MODE_DEVELOPER
            ),
        );
    }

    /**
     * @expectedException \Magento\Core\Exception
     * @expectedExceptionMessage Unknown application mode: unknown mode
     */
    public function testConstructorException()
    {
        new \Magento\Core\Model\App\State("unknown mode");
    }
}
