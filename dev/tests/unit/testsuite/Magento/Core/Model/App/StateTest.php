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
namespace Magento\Core\Model\App;

class StateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\State
     */
    protected $_model;

    /**
     * @param string $mode
     * @dataProvider constructorDataProvider
     */
    public function testConstructor($mode)
    {
        $model = new \Magento\App\State(
            $this->getMockForAbstractClass('Magento\Config\ScopeInterface', array(), '', false),
            time(),
            $mode
        );
        $this->assertEquals($mode, $model->getMode());
    }

    /**
     * @return array
     */
    public static function constructorDataProvider()
    {
        return array(
            'default mode' => array(\Magento\App\State::MODE_DEFAULT),
            'production mode' => array(\Magento\App\State::MODE_PRODUCTION),
            'developer mode' => array(\Magento\App\State::MODE_DEVELOPER)
        );
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Unknown application mode: unknown mode
     */
    public function testConstructorException()
    {
        new \Magento\App\State(
            $this->getMockForAbstractClass('Magento\Config\ScopeInterface', array(), '', false),
            time(),
            "unknown mode"
        );
    }
}
