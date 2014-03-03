<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Theme\Model\Url\Plugin;

class SignatureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Signature
     */
    private $object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $baseUrl;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $deploymentVersion;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $invocationChain;

    protected function setUp()
    {
        $this->config = $this->getMock('Magento\View\Url\ConfigInterface');
        $this->baseUrl = $this->getMock('Magento\UrlInterface');
        $this->deploymentVersion = $this->getMock('Magento\App\View\Deployment\Version', array(), array(), '', false);
        $this->invocationChain = $this->getMock('Magento\Code\Plugin\InvocationChain', array(), array(), '', false);
        $this->invocationChain
            ->expects($this->once())
            ->method('proceed')
            ->with($this->contains('fixture/resource.js'))
            ->will($this->returnValue('http://127.0.0.1/magento/fixture/resource.js'))
        ;
        $this->object = new Signature($this->config, $this->baseUrl, $this->deploymentVersion);
    }

    public function testAroundGetViewFileUrlInactive()
    {
        $this->config
            ->expects($this->once())
            ->method('getValue')
            ->with(Signature::XML_PATH_STATIC_FILE_SIGNATURE)
            ->will($this->returnValue(0))
        ;
        $this->baseUrl->expects($this->never())->method($this->anything());
        $this->deploymentVersion->expects($this->never())->method($this->anything());

        $methodArgs = array('fixture/resource.js');
        $actualResult = $this->object->aroundGetViewFileUrl($methodArgs, $this->invocationChain);
        $this->assertEquals('http://127.0.0.1/magento/fixture/resource.js', $actualResult);
    }

    public function testAroundGetViewFileUrl()
    {
        $this->config
            ->expects($this->once())
            ->method('getValue')
            ->with(Signature::XML_PATH_STATIC_FILE_SIGNATURE)
            ->will($this->returnValue(1))
        ;
        $this->baseUrl
            ->expects($this->once())
            ->method('getBaseUrl')
            ->with(array(
                '_type' => \Magento\UrlInterface::URL_TYPE_STATIC,
                '_secure' => false
            ))
            ->will($this->returnValue('http://127.0.0.1/magento/'))
        ;
        $this->deploymentVersion->expects($this->once())->method('getValue')->will($this->returnValue('123'));

        $methodArgs = array('fixture/resource.js');
        $actualResult = $this->object->aroundGetViewFileUrl($methodArgs, $this->invocationChain);
        $this->assertEquals('http://127.0.0.1/magento/version123/fixture/resource.js', $actualResult);
    }

    public function testAroundGetViewFileUrlSecure()
    {
        $this->config
            ->expects($this->once())
            ->method('getValue')
            ->with(Signature::XML_PATH_STATIC_FILE_SIGNATURE)
            ->will($this->returnValue(1))
        ;
        $this->baseUrl
            ->expects($this->once())
            ->method('getBaseUrl')
            ->with(array(
                '_type' => \Magento\UrlInterface::URL_TYPE_STATIC,
                '_secure' => true
            ))
            ->will($this->returnValue('http://127.0.0.1/magento/'))
        ;
        $this->deploymentVersion->expects($this->once())->method('getValue')->will($this->returnValue('123'));

        $methodArgs = array('fixture/resource.js', array('_secure' => true));
        $actualResult = $this->object->aroundGetViewFileUrl($methodArgs, $this->invocationChain);
        $this->assertEquals('http://127.0.0.1/magento/version123/fixture/resource.js', $actualResult);
    }
}
