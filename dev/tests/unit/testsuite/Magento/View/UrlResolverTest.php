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
     * @var \Magento\View\UrlResolver::__construct
     */
    private $object;

    /**
     * @var \Magento\App\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filesystem;

    /**
     * @var \Magento\UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $url;

    protected function setUp()
    {
        $this->filesystem = $this->getMock('Magento\App\Filesystem', array(), array(), '', false);
        $this->filesystem->expects($this->any())->method('getPath')->will($this->returnArgument(0));
        $this->url = $this->getMockForAbstractClass('Magento\UrlInterface');
        $this->object = new \Magento\View\UrlResolver($this->filesystem, $this->url, array(
            array('key' => 'theme/lib', 'value' => '/root/magento/theme/lib'),
            array('key' => 'lib/web', 'value' => '/root/magento/lib/web'),
        ));
    }

    public function testGetPublicFileUrl()
    {
        $isSecure = 'is_secure';
        $this->url->expects($this->once())
            ->method('getBaseUrl')
            ->with(array(
                '_type' => 'lib/web',
                '_secure' => $isSecure
            ))
            ->will($this->returnValue('http://base.url/'));

        $actualUrl = $this->object->getPublicFileUrl('/root/magento/lib/web/js/some.js', $isSecure);
        $this->assertSame('http://base.url/js/some.js', $actualUrl);
    }

    /**
     * @expectedException \Magento\Exception
     * @expectedExceptionMessage Cannot build URL for the file '/unknown/file.js'
     */
    public function testGetPublicFileUrlException()
    {
        $this->object->getPublicFileUrl('/unknown/file.js');
    }
}
