<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Phrase\Renderer;

class InlineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TranslateInterface
     */
    protected $translator;

    /**
     * @var \Magento\Phrase\Renderer\Translate
     */
    protected $_renderer;

    /**
     * @var \Magento\Translate\Inline\ProviderInterface
     */
    protected $provider;

    protected function setUp()
    {
        $this->translator = $this->getMock('Magento\TranslateInterface', [], [], '', false);
        $this->provider = $this->getMock('Magento\Translate\Inline\ProviderInterface', [], [], '', false);

        $this->renderer = new \Magento\Phrase\Renderer\Inline(
            $this->translator,
            $this->provider
        );
    }

    public function testRenderIfInlineTranslationIsAllowed()
    {
        $theme = 'theme';
        $text = 'test';
        $result = sprintf('{{{%s}}{{%s}}}', $text, $theme);

        $this->translator->expects($this->once())
            ->method('getTheme')
            ->will($this->returnValue($theme));

        $inlineTranslate = $this->getMock('Magento\Translate\InlineInterface', [], [], '', []);
        $inlineTranslate->expects($this->once())
            ->method('isAllowed')
            ->will($this->returnValue(true));

        $this->provider->expects($this->once())
            ->method('get')
            ->will($this->returnValue($inlineTranslate));

        $this->assertEquals($result, $this->renderer->render([$text], []));
    }

    public function testRenderIfInlineTranslationIsNotAllowed()
    {
        $text = 'test';

        $inlineTranslate = $this->getMock('Magento\Translate\InlineInterface', [], [], '', []);
        $inlineTranslate->expects($this->once())
            ->method('isAllowed')
            ->will($this->returnValue(false));

        $this->provider->expects($this->once())
            ->method('get')
            ->will($this->returnValue($inlineTranslate));

        $this->assertEquals($text, $this->renderer->render([$text], []));
    }
}
