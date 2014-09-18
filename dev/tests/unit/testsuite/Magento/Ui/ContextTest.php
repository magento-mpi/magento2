<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Ui;

use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\ContentType\Builders\ConfigurationStorageBuilder;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

/**
 * Class ContextTest
 * @package Magento\Ui
 * @package Magento\Ui
 */
class ContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var ConfigurationStorageBuilder
     */
    protected $configurationStorageBuilder;

    /**
     * @var ConfigurationStorageInterface
     */
    protected $configurationStorage;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var string
     */
    protected $acceptType;

    /**
     * @var Context
     */
    protected $context;

    public function setUp()
    {
        $this->config = $this->getMock('\Magento\Ui\Config', [], [], '', false);
        $this->configurationStorageBuilder = $this->getMock(
            '\Magento\Ui\ContentType\Builders\ConfigurationStorageBuilder',
            [],
            [],
            '',
            false
        );
        $this->configurationStorage = $this->getMock('\Magento\Ui\ConfigurationStorage', [], [], '', false);
        $this->request = $this->getMock('\Magento\Framework\App\Request\Http', [], [], '', false);
        $this->request->expects($this->once())
            ->method('getHeader')
            ->willReturn('json');
        $templateContext = $this->getMock('\Magento\Framework\View\Element\Template\Context', [], [], '', false);
        $templateContext->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->request);

        $this->context = new Context(
            $this->config,
            $this->configurationStorage,
            $this->configurationStorageBuilder,
            $templateContext
        );
    }

    public function testGetAcceptType()
    {
        $this->assertEquals('json', $this->context->getAcceptType());
    }

    public function testSetGetPageLayout()
    {
        $layout = $this->getMock('\Magento\Framework\View\Layout', [], [], '', false);
        $this->context->setPageLayout($layout);
        $this->assertEquals($layout, $this->context->getPageLayout());
    }

    public function testSetGetRootView()
    {
        $view = $this->getMock('\Magento\Ui\AbstractView', [], [], '', false);
        $this->context->setRootView($view);
        $this->assertEquals($view, $this->context->getRootView());
    }
}
 