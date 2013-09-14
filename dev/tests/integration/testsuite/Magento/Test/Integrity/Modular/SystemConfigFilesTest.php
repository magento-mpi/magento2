<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_Integrity_Modular_SystemConfigFilesTest extends PHPUnit_Framework_TestCase
{
    public function testConfiguration()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        // disable config caching to not pollute it
        /** @var $cacheState \Magento\Core\Model\Cache\StateInterface */
        $cacheState = $objectManager->get('Magento\Core\Model\Cache\StateInterface');
        $cacheState->setEnabled(\Magento\Core\Model\Cache\Type\Config::TYPE_IDENTIFIER, false);

        /** @var $dirs \Magento\Core\Model\Dir */
        $dirs = $objectManager->get('Magento\Core\Model\Dir');
        $modulesDir = $dirs->getDir(\Magento\Core\Model\Dir::MODULES);

        $fileList = glob($modulesDir . '/*/*/etc/adminhtml/system.xml');

        $configMock = $this->getMock(
            'Magento\Core\Model\Config\Modules\Reader', array('getConfigurationFiles', 'getModuleDir'),
            array(), '', false
        );
        $configMock->expects($this->any())
            ->method('getConfigurationFiles')
            ->will($this->returnValue($fileList))
        ;
        $configMock->expects($this->any())
            ->method('getModuleDir')
            ->with('etc', 'Magento_Backend')
            ->will($this->returnValue($modulesDir . '/Magento/Backend/etc'))
        ;
        try {
            $objectManager->create('Magento\Backend\Model\Config\Structure\Reader', array(
                'moduleReader' => $configMock,
                'runtimeValidation' => true,
            ));
        } catch (\Magento\Exception $exp) {
            $this->fail($exp->getMessage());
        }
    }
}
