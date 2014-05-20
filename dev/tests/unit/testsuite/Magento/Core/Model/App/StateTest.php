<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\App;

class StateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $_model;

    /**
     * @param string $mode
     * @dataProvider constructorDataProvider
     */
    public function testConstructor($mode)
    {
        $model = new \Magento\Framework\App\State(
            $this->getMockForAbstractClass('Magento\Framework\Config\ScopeInterface', array(), '', false),
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
            'default mode' => array(\Magento\Framework\App\State::MODE_DEFAULT),
            'production mode' => array(\Magento\Framework\App\State::MODE_PRODUCTION),
            'developer mode' => array(\Magento\Framework\App\State::MODE_DEVELOPER)
        );
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Unknown application mode: unknown mode
     */
    public function testConstructorException()
    {
        new \Magento\Framework\App\State(
            $this->getMockForAbstractClass('Magento\Framework\Config\ScopeInterface', array(), '', false),
            time(),
            "unknown mode"
        );
    }
}
