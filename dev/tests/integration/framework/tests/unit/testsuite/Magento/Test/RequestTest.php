<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Request
     */
    protected $_model = null;

    protected function setUp()
    {
        $this->_model = new \Magento\TestFramework\Request(
            $this->getMock('Magento\Framework\App\Route\ConfigInterface', array(), array(), '', false),
            $this->getMock('Magento\Framework\App\Request\PathInfoProcessorInterface', array(), array(), '', false)
        );
    }

    public function testGetHttpHost()
    {
        $this->assertEquals('localhost', $this->_model->getHttpHost());
        $this->assertEquals('localhost', $this->_model->getHttpHost(false));
    }

    public function testSetGetServer()
    {
        $this->assertSame(array(), $this->_model->getServer());
        $this->assertSame($this->_model, $this->_model->setServer(array('test' => 'value', 'null' => null)));
        $this->assertSame(array('test' => 'value', 'null' => null), $this->_model->getServer());
        $this->assertEquals('value', $this->_model->getServer('test'));
        $this->assertSame(null, $this->_model->getServer('non-existing'));
        $this->assertSame('default', $this->_model->getServer('non-existing', 'default'));
        $this->assertSame(null, $this->_model->getServer('null'));
    }
}
