<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install;

class IndexTest extends \Magento\TestFramework\TestCase\AbstractController
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
        $this->assertFileExists(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Dir')
                ->getDir(\Magento\Core\Model\Dir::TMP)
        );
    }
}
