<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Phrase\Renderer;

class PlaceholderTest extends \PHPUnit_Framework_TestCase
{
    /** @var Placeholder */
    protected $_renderer;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_renderer = $objectManager->getObject('Magento\Framework\Phrase\Renderer\Placeholder');
    }

    /**
     * @param string $text The text with placeholders
     * @param array $arguments The arguments supplying values for the placeholders
     * @param string $result The result of Phrase rendering
     *
     * @dataProvider renderPlaceholderDataProvider
     */
    public function testRenderPlaceholder($text, array $arguments, $result)
    {
        $this->assertEquals($result, $this->_renderer->render([$text], $arguments));
    }

    /**
     * @return array
     */
    public function renderPlaceholderDataProvider()
    {
        return array(
            array('text %1 %2', array('one', 'two'), 'text one two'),
            array('text %one %two', array('one' => 'one', 'two' => 'two'), 'text one two'),
            array('%one text %two %1', array('one' => 'one', 'two' => 'two', 'three'), 'one text two three'),
            array(
                'text %1 %two %2 %3 %five %4 %5',
                array('one', 'two' => 'two', 'three', 'four', 'five' => 'five', 'six', 'seven'),
                'text one two three four five six seven'
            ),
            array(
                '%one text %two text %three %1 %2',
                array('two' => 'two', 'one' => 'one', 'three' => 'three', 'four', 'five'),
                'one text two text three four five'
            ),
            array(
                '%three text %two text %1',
                array('two' => 'two', 'three' => 'three', 'one'),
                'three text two text one'
            ),
            array('text %1 text %2 text', array(), 'text %1 text %2 text'),
            array('%1 text %2', array('one'), 'one text %2')
        );
    }
}
