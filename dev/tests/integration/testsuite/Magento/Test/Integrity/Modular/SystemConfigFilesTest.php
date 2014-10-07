<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Modular;

use Magento\Framework\App\Filesystem\DirectoryList;

class SystemConfigFilesTest extends \PHPUnit_Framework_TestCase
{
    public function testConfiguration()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        // disable config caching to not pollute it
        /** @var $cacheState \Magento\Framework\App\Cache\StateInterface */
        $cacheState = $objectManager->get('Magento\Framework\App\Cache\StateInterface');
        $cacheState->setEnabled(\Magento\Framework\App\Cache\Type\Config::TYPE_IDENTIFIER, false);

        $modulesDir = $objectManager->get('Magento\Framework\App\Filesystem')
            ->getPath(DirectoryList::MODULES_DIR);

        $fileList = glob($modulesDir . '/*/*/etc/adminhtml/system.xml');

        $configMock = $this->getMock(
            'Magento\Framework\Module\Dir\Reader',
            array('getConfigurationFiles', 'getModuleDir'),
            array(),
            '',
            false
        );
        $configMock->expects($this->any())->method('getConfigurationFiles')->will($this->returnValue($fileList));
        $configMock->expects(
            $this->any()
        )->method(
            'getModuleDir'
        )->with(
            'etc',
            'Magento_Backend'
        )->will(
            $this->returnValue($modulesDir . '/Magento/Backend/etc')
        );
        try {
            $objectManager->create(
                'Magento\Backend\Model\Config\Structure\Reader',
                array('moduleReader' => $configMock, 'runtimeValidation' => true)
            );
        } catch (\Magento\Framework\Exception $exp) {
            $this->fail($exp->getMessage());
        }
    }
}
