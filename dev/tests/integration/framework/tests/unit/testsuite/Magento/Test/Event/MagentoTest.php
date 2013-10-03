<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\TestFramework\Event\Magento.
 */
namespace Magento\Test\Event;

class MagentoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Event\Magento
     */
    protected $_object;

    /**
     * @var \Magento\TestFramework\EventManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManager;

    protected function setUp()
    {
        $this->_eventManager = $this->getMock('Magento\TestFramework\EventManager', array('fireEvent'), array(array()));
        $this->_object = new \Magento\TestFramework\Event\Magento($this->_eventManager);
    }

    protected function tearDown()
    {
        \Magento\TestFramework\Event\Magento::setDefaultEventManager(null);
    }

    public function testConstructorDefaultEventManager()
    {
        \Magento\TestFramework\Event\Magento::setDefaultEventManager($this->_eventManager);
        $this->_object = new \Magento\TestFramework\Event\Magento();
        $this->testInitStoreAfter();
    }

    /**
     * @dataProvider constructorExceptionDataProvider
     * @expectedException \Magento\Exception
     * @param mixed $eventManager
     */
    public function testConstructorException($eventManager)
    {
        new \Magento\TestFramework\Event\Magento($eventManager);
    }

    public function constructorExceptionDataProvider()
    {
        return array(
            'no event manager'     => array(null),
            'not an event manager' => array(new \stdClass()),
        );
    }

    public function testInitStoreAfter()
    {
        $this->_eventManager
            ->expects($this->once())
            ->method('fireEvent')
            ->with('initStoreAfter')
        ;
        $this->_object->initStoreAfter();
    }
}
