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
        $config = $this->getMockBuilder('Magento\App\Config\ScopeConfigInterface')
            ->disableOriginalConstructor()
            ->setMethods(['getValue', 'setValue', 'isSetFlag'])
            ->getMock();

        $config->expects(
            $this->at(0)
        )->method(
            'getValue'
        )->with(
            Configuration::XML_FILESYSTEM_DIRECTORY_PATH
        )->will(
            $this->returnValue(array(\Magento\App\Filesystem::PUB_DIR => array('uri' => '')))
        );

        $config->expects(
            $this->at(1)
        )->method(
            'getValue'
        )->with(
            Configuration::XML_FILESYSTEM_WRAPPER_PATH
        )->will(
            $this->returnValue(array(\Magento\Filesystem::HTTP => array('protocol' => 'http')))
        );

        /* Mock DirectoryList model */
        $directoryList = $this->getMockBuilder(
            'Magento\Filesystem\DirectoryList'
        )->disableOriginalConstructor()->setMethods(
            array('addDirectory', 'isConfigured', 'addProtocol')
        )->getMock();

        $directoryList->expects(
            $this->once()
        )->method(
            'addProtocol'
        )->with(
            \Magento\Filesystem::HTTP,
            array('protocol' => 'http')
        );

        $directoryList->expects(
            $this->atLeastOnce()
        )->method(
            'isConfigured'
        )->with(
            \Magento\App\Filesystem::PUB_DIR
        )->will(
            $this->returnValue($pubDirIsConfigured)
        );

        if ($pubDirIsConfigured) {
            $directoryList->expects($this->never())->method('addDirectory');
        } else {
            $directoryList->expects(
                $this->once()
            )->method(
                'addDirectory'
            )->with(
                \Magento\App\Filesystem::PUB_DIR,
                array('uri' => '')
            );
        }

        $this->dirListConfiguration = $objectManager->getObject(
            'Magento\App\Filesystem\DirectoryList\Configuration',
            array('config' => $config)
        );
        $this->assertNull($this->dirListConfiguration->configure($directoryList));
    }

    public function configureDataProvider()
    {
        return array(array('pubDirIsConfigured' => true), array('pubDirIsConfigured' => false));
    }
}
