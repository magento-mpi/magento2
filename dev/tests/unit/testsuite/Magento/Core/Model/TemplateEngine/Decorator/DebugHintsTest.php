<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\TemplateEngine\Decorator;

class DebugHintsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param bool $showBlockHints
     * @dataProvider renderDataProvider
     */
    public function testRender($showBlockHints)
    {
        $subject = $this->getMock('Magento\Framework\View\TemplateEngineInterface');
        $block = $this->getMock('Magento\Framework\View\Element\BlockInterface', array(), array(), 'TestBlock', false);
        $subject->expects(
            $this->once()
        )->method(
            'render'
        )->with(
            $this->identicalTo($block),
            'template.phtml',
            array('var' => 'val')
        )->will(
            $this->returnValue('<div id="fixture"/>')
        );
        $model = new DebugHints($subject, $showBlockHints);
        $actualResult = $model->render($block, 'template.phtml', array('var' => 'val'));
        $this->assertSelectEquals('div > div[title="template.phtml"]', 'template.phtml', 1, $actualResult);
        $this->assertSelectCount('div > div#fixture', 1, $actualResult);
        $this->assertSelectEquals('div > div[title="TestBlock"]', 'TestBlock', (int)$showBlockHints, $actualResult);
    }

    public function renderDataProvider()
    {
        return array('block hints disabled' => array(false), 'block hints enabled' => array(true));
    }
}
