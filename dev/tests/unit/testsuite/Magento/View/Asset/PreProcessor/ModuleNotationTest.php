<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\PreProcessor;

class ModuleNotationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Asset\PreProcessor\ModuleNotation
     */
    protected $moduleNotation;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cssResolverMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $assetMock;

    protected function setUp()
    {
        $this->assetMock = $this->getMock('Magento\View\Asset\File', array(), array(), '', false);
        $this->cssResolverMock = $this->getMock('Magento\View\Url\CssResolver', array(), array(), '', false);
        $notationResolver = $this->getMock('\Magento\View\Asset\ModuleNotation\Resolver', array(), array(), '', false);
        $this->moduleNotation = new ModuleNotation(
            $this->cssResolverMock, $notationResolver
        );
    }

    public function testProcess()
    {
        $content = 'ol.favicon {background: url(Magento_Theme::favicon.ico)}';
        $replacedContent = 'Foo_Bar/images/logo.gif';
        $contentType = 'type';
        $this->cssResolverMock->expects($this->once())
            ->method('replaceRelativeUrls')
            ->with($content, $this->isInstanceOf('Closure'))
            ->will($this->returnValue($replacedContent));

        $this->assertSame(
            array($replacedContent, $contentType),
            $this->moduleNotation->process($content, $contentType, $this->assetMock)
        );
    }
}
