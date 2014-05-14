<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Performance\Scenario;

class FailureExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Performance\Scenario\FailureException
     */
    protected $_object;

    /**
     * @var \Magento\TestFramework\Performance\Scenario
     */
    protected $_scenario;

    protected function setUp()
    {
        $this->_scenario = new \Magento\TestFramework\Performance\Scenario('Title', '', array(), array(), array());
        $this->_object = new \Magento\TestFramework\Performance\Scenario\FailureException(
            $this->_scenario,
            'scenario has failed'
        );
    }

    protected function tearDown()
    {
        $this->_object = null;
        $this->_scenario = null;
    }

    public function testConstructor()
    {
        $this->assertEquals('scenario has failed', $this->_object->getMessage());
    }

    public function testGetScenario()
    {
        $this->assertSame($this->_scenario, $this->_object->getScenario());
    }
}
