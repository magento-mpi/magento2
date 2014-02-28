<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\Filesystem\DirectoryList;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Configuration
     */
    protected $dirListConfiguration;

    /**
     * @dataProvider configureDataProvider
     */
    public function testConfigure($pubDirIsConfigured)
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        /* Mock Config model */
        $config = $this->getMockBuilder('Magento\App\ConfigInterface')
            ->disableOriginalConstructor()
            ->setMethods(['getValue', 'setValue', 'isSetFlag'])
            ->getMock();

        $config->expects($this->at(0))
            ->method('getValue')
            ->with(Configuration::XML_FILESYSTEM_DIRECTORY_PATH)
            ->will($this->returnValue([\Magento\App\Filesystem::PUB_DIR => ['uri' => '']]));

        $config->expects($this->at(1))
            ->method('getValue')
            ->with(Configuration::XML_FILESYSTEM_WRAPPER_PATH)
            ->will($this->returnValue([\Magento\Filesystem::HTTP => ['protocol' => 'http']]));

        /* Mock DirectoryList model */
        $directoryList = $this->getMockBuilder('Magento\Filesystem\DirectoryList')
            ->disableOriginalConstructor()
            ->setMethods(['addDirectory', 'isConfigured', 'addProtocol'])
            ->getMock();

        $directoryList->expects($this->once())
            ->method('addProtocol')
            ->with(\Magento\Filesystem::HTTP, ['protocol' => 'http']);

        $directoryList->expects($this->atLeastOnce())
            ->method('isConfigured')
            ->with(\Magento\App\Filesystem::PUB_DIR)
            ->will($this->returnValue($pubDirIsConfigured));

        if ($pubDirIsConfigured) {
            $directoryList->expects($this->never())
                ->method('addDirectory');
        } else {
            $directoryList->expects($this->once())
                ->method('addDirectory')
                ->with(\Magento\App\Filesystem::PUB_DIR, ['uri' => '']);
        }

        $this->dirListConfiguration = $objectManager->getObject(
            'Magento\App\Filesystem\DirectoryList\Configuration',
            ['config' => $config]
        );
        $this->assertNull($this->dirListConfiguration->configure($directoryList));
    }

    public function configureDataProvider()
    {
        return [
            ['pubDirIsConfigured' => true],
            ['pubDirIsConfigured' => false]
        ];
    }
}
