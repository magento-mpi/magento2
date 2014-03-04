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
    private $deploymentVersion;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $invocationChain;

    protected function setUp()
    {
        $this->config = $this->getMock('Magento\View\Url\ConfigInterface');
        $this->deploymentVersion = $this->getMock('Magento\App\View\Deployment\Version', array(), array(), '', false);
        $this->invocationChain = $this->getMock('Magento\Code\Plugin\InvocationChain', array(), array(), '', false);
        $this->invocationChain
            ->expects($this->once())
            ->method('proceed')
            ->with($this->logicalNot($this->isEmpty()))
            ->will($this->returnValue('http://127.0.0.1/magento/pub/static/'))
        ;
        $this->object = new Signature($this->config, $this->deploymentVersion);
    }

    /**
     * @param bool|int $fixtureConfigFlag
     * @param string $inputUrlType
     * @dataProvider aroundGetBaseUrlInactiveDataProvider
     */
    public function testAroundGetBaseUrlInactive($fixtureConfigFlag, $inputUrlType)
    {
        $this->config
            ->expects($this->any())
            ->method('getValue')
            ->with(Signature::XML_PATH_STATIC_FILE_SIGNATURE)
            ->will($this->returnValue($fixtureConfigFlag))
        ;
        $this->deploymentVersion->expects($this->never())->method($this->anything());

        $methodArgs = array($inputUrlType);
        $actualResult = $this->object->aroundGetBaseUrl($methodArgs, $this->invocationChain);
        $this->assertEquals('http://127.0.0.1/magento/pub/static/', $actualResult);
    }

    public function aroundGetBaseUrlInactiveDataProvider()
    {
        return array(
            'disabled in config, relevant URL type'  => array(0, \Magento\UrlInterface::URL_TYPE_STATIC),
            'enabled in config, irrelevant URL type' => array(1, \Magento\UrlInterface::URL_TYPE_LINK),
        );
    }

    public function testAroundGetBaseUrlActive()
    {
        $this->config
            ->expects($this->once())
            ->method('getValue')
            ->with(Signature::XML_PATH_STATIC_FILE_SIGNATURE)
            ->will($this->returnValue(1))
        ;
        $this->deploymentVersion->expects($this->once())->method('getValue')->will($this->returnValue('123'));

        $methodArgs = array(\Magento\UrlInterface::URL_TYPE_STATIC);
        $actualResult = $this->object->aroundGetBaseUrl($methodArgs, $this->invocationChain);
        $this->assertEquals('http://127.0.0.1/magento/pub/static/version123/', $actualResult);
    }
}
