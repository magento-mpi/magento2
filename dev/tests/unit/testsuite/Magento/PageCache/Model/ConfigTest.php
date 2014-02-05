<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Model;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\PageCache\Model\Config  */
    protected $_model;

    /**
     * setUp all mocks and data function
     */
    public function setUp()
    {
        $filesystemMock = $this->getMock('Magento\App\Filesystem', ['getDirectoryRead'], [], '', false);
        $coreStoreConfigMock = $this->getMock('Magento\Core\Model\Store\Config', ['getConfig'], [], '', false);

        $modulesDirectoryMock = $this->getMock('Magento\Filesystem\Directory\Write', [], [], '', false);
        $filesystemMock->expects($this->once())
            ->method('getDirectoryRead')
            ->with(\Magento\App\Filesystem::MODULES_DIR)
            ->will($this->returnValue($modulesDirectoryMock));
        $modulesDirectoryMock->expects($this->once())
            ->method('readFile')
            ->will($this->returnValue(file_get_contents(__DIR__ . '/_files/test.vcl')));
        $coreStoreConfigMock->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValueMap([
                [\Magento\PageCache\Model\Config::XML_VARNISH_PAGECACHE_BACKEND_HOST, null, 'example.com'],
                [\Magento\PageCache\Model\Config::XML_VARNISH_PAGECACHE_BACKEND_PORT, null, '8080'],
                [\Magento\PageCache\Model\Config::XML_VARNISH_PAGECACHE_ACCESS_LIST, null, '127.0.0.1, 192.168.0.1'],
                [
                    \Magento\PageCache\Model\Config::XML_VARNISH_PAGECACHE_DESIGN_THEME_REGEX,
                    null,
                    serialize([
                        [
                            'regexp' => '(?i)pattern',
                            'value'  => 'value_for_pattern'
                        ]
                    ])
                ]
            ]));

        $this->_model = new \Magento\PageCache\Model\Config($filesystemMock, $coreStoreConfigMock);
    }

    /**
     * test for getVcl method
     */
    public function testGetVcl()
    {
        $test = $this->_model->getVclFile();
        $this->assertEquals(file_get_contents(__DIR__ . '/_files/result.vcl'), $test);
    }
}
