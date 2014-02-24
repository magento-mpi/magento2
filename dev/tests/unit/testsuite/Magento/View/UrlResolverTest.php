<?php
/**
 * {license_notice}
 *
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

class UrlResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filesystem;

    /**
     * @var \Magento\UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $url;

    /**
     * @var \Magento\View\Url\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    /**
     * @var \Magento\View\Service|\PHPUnit_Framework_MockObject_MockObject
     */
    private $viewService;

    protected function setUp()
    {
        $this->filesystem = $this->getMock('Magento\App\Filesystem', array(), array(), '', false);
        $this->url = $this->getMockForAbstractClass('Magento\UrlInterface');
        $this->config = $this->getMockForAbstractClass('Magento\View\Url\ConfigInterface');
        $this->viewService = $this->getMock('Magento\View\Service', array(), array(), '', false);
    }

    public function testGetPublicFileUrl()
    {
        $map = array(
            array('value' => '/root/magento/theme/lib', 'key' => 'theme/lib'),
            array('value' => '/root/magento/lib/web', 'key' => 'lib/web'),
        );
        $file = '/root/magento/lib/web/js/some.js';
        $expectedKey = 'lib/web';
        $expectedUrl = 'http://base.url/js/some.js';

        $this->filesystem->expects($this->any())
            ->method('getPath')
            ->will($this->returnArgument(0));
        $isSecure = 'is_secure';
        $this->config->expects($this->once())
            ->method('getValue')
            ->with(\Magento\View\UrlResolver::XML_PATH_STATIC_FILE_SIGNATURE)
            ->will($this->returnValue(false));
        $this->url->expects($this->once())
            ->method('getBaseUrl')
            ->with(array(
                '_type' => $expectedKey,
                '_secure' => $isSecure
            ))
            ->will($this->returnValue('http://base.url/'));

        $object = new \Magento\View\UrlResolver(
            $this->filesystem, $this->url, $this->config, $this->viewService, $map
        );
        $actualUrl = $object->getPublicFileUrl($file, $isSecure);
        $this->assertSame($expectedUrl, $actualUrl);
    }

    public function testGetPublicFileUrlSigned()
    {
        $map = array(
            array('value' => '/root/magento/theme/lib', 'key' => 'theme/lib'),
            array('value' => '/root/magento/lib/web', 'key' => 'lib/web'),
        );
        $file = '/root/magento/lib/web/js/some.js';
        $expectedKey = 'lib/web';
        $mTime = 123456;
        $expectedUrl = 'http://base.url/js/some.js?' . $mTime;
        $isSecure = 'is_secure';

        $this->filesystem->expects($this->any())
            ->method('getPath')
            ->will($this->returnArgument(0));
        $this->url->expects($this->once())
            ->method('getBaseUrl')
            ->with(array(
                '_type' => $expectedKey,
                '_secure' => $isSecure
            ))
            ->will($this->returnValue('http://base.url/'));
        $this->config->expects($this->once())
            ->method('getValue')
            ->with(\Magento\View\UrlResolver::XML_PATH_STATIC_FILE_SIGNATURE)
            ->will($this->returnValue(true));
        $this->viewService->expects($this->once())
            ->method('isViewFileOperationAllowed')
            ->will($this->returnValue(true));

        $dir = $this->getMockForAbstractClass('Magento\Filesystem\Directory\ReadInterface');
        $this->filesystem->expects($this->once())
            ->method('getDirectoryRead')
            ->with(\Magento\App\Filesystem::ROOT_DIR)
            ->will($this->returnValue($dir));
        $dir->expects($this->once())
            ->method('getRelativePath')
            ->will($this->returnArgument(0));
        $dir->expects($this->once())
            ->method('stat')
            ->with($file)
            ->will($this->returnValue(array('mtime' => $mTime)));


        $object = new \Magento\View\UrlResolver(
            $this->filesystem, $this->url, $this->config, $this->viewService, $map
        );
        $actualUrl = $object->getPublicFileUrl($file, $isSecure);
        $this->assertSame($expectedUrl, $actualUrl);
    }

    /**
     * @expectedException \Magento\Exception
     * @expectedExceptionMessage Cannot build URL for the file '/any/file.js'
     */
    public function testGetPublicFileUrlSignedException()
    {
        $map = array();
        $object = new \Magento\View\UrlResolver(
            $this->filesystem, $this->url, $this->config, $this->viewService, $map
        );
        $object->getPublicFileUrl('/any/file.js');
    }
}
