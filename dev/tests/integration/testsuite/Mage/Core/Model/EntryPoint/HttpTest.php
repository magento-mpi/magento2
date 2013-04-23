<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_EntryPoint_HttpTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testProcessRequestBootstrapException()
    {
        if (!Magento_Test_Helper_Bootstrap::canTestHeaders()) {
            $this->markTestSkipped('Can\'t test entry point response without sending headers');
        }

        $dirVerification = $this->getMock('Mage_Core_Model_Dir_Verification', array(), array(), '', false);
        $dirVerification->expects($this->once())
            ->method('createAndVerifyDirectories')
            ->will($this->throwException(new Magento_BootstrapException('exception_message')));

        $objectManager = $this->getMock('Magento_ObjectManager');
        $objectManager->expects($this->any())
            ->method('get')
            ->with('Mage_Core_Model_Dir_Verification')
            ->will($this->returnValue($dirVerification));

        $config = $this->getMock('Mage_Core_Model_Config_Primary', array(), array(), '', false);

        $model = $this->getMock('Mage_Core_Model_EntryPoint_Http', array('_setGlobalObjectManager'),
            array($config, $objectManager));
        ob_start();
        $model->processRequest();
        $content = ob_get_flush();

        $headers = xdebug_get_headers();
        $this->assertContains('Content-Type: text/plain', $headers);
        $this->assertEquals('exception_message', $content, 'The response must contain exception message, and only it');
    }
}
