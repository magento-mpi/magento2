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

    public function testConfigure()
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
            Configuration::XML_FILESYSTEM_WRAPPER_PATH
        )->will(
            $this->returnValue(array(\Magento\Framework\Filesystem::HTTP => array('protocol' => 'http')))
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
            \Magento\Framework\Filesystem::HTTP,
            array('protocol' => 'http')
        );

        $this->dirListConfiguration = $objectManager->getObject(
            'Magento\Framework\App\Filesystem\DirectoryList\Configuration',
            array('config' => $config)
        );
        $this->assertNull($this->dirListConfiguration->configure($directoryList));
    }
}
