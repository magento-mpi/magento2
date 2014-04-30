<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * \Magento\Framework\Cache\Core test case
 */
namespace Magento\Framework\Cache;

class CoreTest extends \PHPUnit_Framework_TestCase
{
    public function testSetBackendSuccess()
    {
        $mockBackend = $this->getMock('Zend_Cache_Backend_File');
        $config = array(
            'backend_decorators' => array(
                'test_decorator' => array(
                    'class' => 'Magento\Framework\Cache\Backend\Decorator\Compression',
                    'options' => array('compression_threshold' => '100')
                )
            )
        );

        $core = new \Magento\Framework\Cache\Core($config);
        $core->setBackend($mockBackend);

        $this->assertInstanceOf('Magento\Framework\Cache\Backend\Decorator\AbstractDecorator', $core->getBackend());
    }

    /**
     * @expectedException \Zend_Cache_Exception
     */
    public function testSetBackendException()
    {
        $mockBackend = $this->getMock('Zend_Cache_Backend_File');
        $config = array('backend_decorators' => array('test_decorator' => array('class' => 'Zend_Cache_Backend')));

        $core = new \Magento\Framework\Cache\Core($config);
        $core->setBackend($mockBackend);
    }
}
