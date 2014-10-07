<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Filesystem\DirectoryList;

use Magento\Framework\App\Filesystem\DirectoryList;

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
        $config = $this->getMockBuilder(
            'Magento\Framework\App\Config\ScopeConfigInterface'
        )->disableOriginalConstructor()->setMethods(
            array('getValue', 'setValue', 'isSetFlag')
        )->getMock();

        $config->expects(
            $this->at(0)
        )->method(
            'getValue'
        )->with(
            Configuration::XML_FILESYSTEM_DIRECTORY_PATH
        )->will(
            $this->returnValue(array(DirectoryList::PUB_DIR => array('uri' => '')))
        );

        $config->expects(
            $this->at(1)
        )->method(
            'getValue'
        )->with(
            Configuration::XML_FILESYSTEM_WRAPPER_PATH
        )->will(
            $this->returnValue(array(DirectoryList::HTTP => array('protocol' => 'http')))
        );

        /* Mock DirectoryList model */
        $directoryList = $this->getMockBuilder(
            'Magento\Framework\Filesystem\DirectoryList'
        )->disableOriginalConstructor()->setMethods(
            array('setDirectory', 'isConfigured', 'addProtocol', 'getConfig')
        )->getMock();

        $directoryList->expects(
            $this->once()
        )->method(
            'addProtocol'
        )->with(
            DirectoryList::HTTP,
            array('protocol' => 'http')
        );

        $directoryList->expects(
            $this->atLeastOnce()
        )->method(
            'isConfigured'
        )->with(
                DirectoryList::PUB_DIR
        )->will(
            $this->returnValue($pubDirIsConfigured)
        );

        if ($pubDirIsConfigured) {
            $directoryList->expects($this->once())
                ->method('getConfig')
                ->with(DirectoryList::PUB_DIR)
                ->will($this->returnValue(['test_key' => 'test_value']));
            $directoryList->expects($this->once())
                ->method('setDirectory')
                ->with(DirectoryList::PUB_DIR, ['uri' => '', 'test_key' => 'test_value']);
        } else {
            $directoryList->expects($this->once())
                ->method('setDirectory')
                ->with(DirectoryList::PUB_DIR, array('uri' => ''));
        }

        $this->dirListConfiguration = $objectManager->getObject(
            'Magento\Framework\App\Filesystem\DirectoryList\Configuration',
            array('config' => $config)
        );
        $this->assertNull($this->dirListConfiguration->configure($directoryList));
    }

    public function configureDataProvider()
    {
        return array(array('pubDirIsConfigured' => true), array('pubDirIsConfigured' => false));
    }
}
