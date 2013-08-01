<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento_Cache_Core test case
 */
class Magento_Cache_CoreTest extends PHPUnit_Framework_TestCase
{
    public function testSetBackendSuccess()
    {
        $mockBackend = $this->getMock('Zend_Cache_Backend_File');
        $config = array(
            'backend_decorators' => array(
                'test_decorator' => array(
                    'class' => 'Magento_Cache_Backend_Decorator_Compression',
                    'options' => array(
                        'compression_threshold' => '100',
                    )
                )
            )
        );

        $core = new Magento_Cache_Core($config);
        $core->setBackend($mockBackend);

        $this->assertInstanceOf('Magento_Cache_Backend_Decorator_DecoratorAbstract', $core->getBackend());
    }

    /**
     * @expectedException Zend_Cache_Exception
     */
    public function testSetBackendException()
    {
        $mockBackend = $this->getMock('Zend_Cache_Backend_File');
        $config = array(
            'backend_decorators' => array(
                'test_decorator' => array(
                    'class' => 'Zend_Cache_Backend',
                )
            )
        );

        $core = new Magento_Cache_Core($config);
        $core->setBackend($mockBackend);
    }
}
