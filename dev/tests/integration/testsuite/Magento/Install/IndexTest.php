<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Install_IndexTest extends Magento_TestFramework_TestCase_ControllerAbstract
{
    public function testIndexAction()
    {
        $this->dispatch('install/index/index');
        $request = $this->getRequest();
        $this->assertEquals('begin', $request->getActionName());
        $this->assertEquals('wizard', $request->getControllerName());
        $this->assertEquals('install', $request->getModuleName());
        /**
         * Make sure that preDispatch() didn't cleanup var directory (by asserting presence of anything there),
         * because in integration testing environment the application is considered "installed"
         */
        $this->assertFileExists(Mage::getBaseDir(Magento_Core_Model_Dir::TMP));
    }
}
