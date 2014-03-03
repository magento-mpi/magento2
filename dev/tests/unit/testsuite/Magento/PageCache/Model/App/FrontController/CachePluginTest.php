<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Model\App\FrontController;

class CachePluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\PageCache\Model\App\FrontController\CachePlugin
     */
    protected $plugin;

    /**
     * @var \Magento\PageCache\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $config;

    /**
     * @var \Magento\App\PageCache\Version|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $version;

    /**
     * @var \Magento\App\PageCache\Kernel|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $kernel;

    /**
     * @var \Magento\App\Response\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $response;

    /**
     * @var \Magento\App\FrontControllerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $frontController;

    /**
     * @var \Closure
     */
    protected $closure;

    /**
     * @var \Magento\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * SetUp
     */
    public function setUp()
    {
        $this->config = $this->getMock('Magento\PageCache\Model\Config', array(), array(), '', false);
        $this->version = $this->getMock('Magento\App\PageCache\Version', array(), array(), '', false);
        $this->kernel = $this->getMock('Magento\App\PageCache\Kernel', array(), array(), '', false);
        $this->frontController = $this->getMock('Magento\App\FrontControllerInterface', array(), array(), '', false);
        $this->request = $this->getMock('Magento\App\RequestInterface', array(), array(), '', false);
        $this->response = $this->getMock('Magento\App\Response\Http', array(), array(), '', false);
        $response = $this->response;
        $this->closure = function ($request) use ($response) {
            return $response;
        };
        $this->plugin = new CachePlugin($this->config, $this->version, $this->kernel);
    }

    public function testAroundDispatchProcessIfCacheMissedForBuiltIn()
    {
        $this->version
            ->expects($this->once())
            ->method('process');
        $this->config
            ->expects($this->once())
            ->method('getType')
            ->will($this->returnValue(\Magento\PageCache\Model\Config::BUILT_IN));
        $this->kernel
            ->expects($this->once())
            ->method('load')
            ->will($this->returnValue(false));
        $this->kernel
            ->expects($this->once())
            ->method('process')
            ->with($this->response);
        $this->plugin->aroundDispatch($this->frontController, $this->closure, $this->request);
    }

    public function testAroundDispatchReturnsCacheForBuiltIn()
    {
        $this->version
            ->expects($this->once())
            ->method('process');
        $this->config
            ->expects($this->once())
            ->method('getType')
            ->will($this->returnValue(\Magento\PageCache\Model\Config::BUILT_IN));
        $this->kernel
            ->expects($this->once())
            ->method('load')
            ->will($this->returnValue($this->response));
        $this->plugin->aroundDispatch($this->frontController, $this->closure, $this->request);
    }

    public function testAroundDispatchVarnish()
    {
        $this->version
            ->expects($this->once())
            ->method('process');
        $this->config
            ->expects($this->once())
            ->method('getType')
            ->will($this->returnValue(\Magento\PageCache\Model\Config::VARNISH));
        $this->plugin->aroundDispatch($this->frontController, $this->closure, $this->request);
    }
}
