<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_EntryPoint_HttpTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testProcessRequestBootstrapException()
    {
        if (!Magento_TestFramework_Helper_Bootstrap::canTestHeaders()) {
            $this->markTestSkipped('Can\'t test entry point response without sending headers');
        }

        $config = $this->getMock('Magento_Core_Model_Config_Primary', array(), array(), '', false);
        $objectManager = $this->getMock('Magento_ObjectManager');
        $objectManager->expects($this->any())
            ->method('get')
            ->will($this->throwException(new Magento_BootstrapException('exception_message')));

        $config = $this->getMock('Magento_Core_Model_Config_Primary', array(), array(), '', false);


        /** @var Magento_Core_Model_EntryPoint_Http $model */
        $model = new Magento_Core_Model_EntryPoint_Http($config, $objectManager);
        ob_start();
        $model->processRequest();
        $content = ob_get_clean();

        $headers = xdebug_get_headers();
        $this->assertContains('Content-Type: text/plain', $headers);
        $this->assertEquals('exception_message', $content, 'The response must contain exception message, and only it');
    }
}
