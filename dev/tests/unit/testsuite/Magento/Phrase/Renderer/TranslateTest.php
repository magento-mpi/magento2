<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Phrase\Renderer;

class TranslateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Translate|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_translator;

    /**
     * @var \Magento\Phrase\Renderer\Translate
     */
    protected $_renderer;

    protected function setUp()
    {
        $this->_translator = $this->getMock('Magento\Framework\TranslateInterface', array(), array(), '', false);

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_renderer = $objectManagerHelper->getObject(
            'Magento\Phrase\Renderer\Translate',
            array('translator' => $this->_translator)
        );
    }

    public function testRenderByCode()
    {
        $text = 'original text';
        $result = 'rendered text';

        $this->_translator->expects(
            $this->once()
        )->method(
            'getTheme'
        )->will(
            $this->returnValue('theme')
        );
        $this->_translator->expects(
            $this->once()
        )->method(
            'getData'
        )->will(
            $this->returnValue(['theme::' . $text => $result])
        );

        $this->assertEquals($result, $this->_renderer->render([$text], []));
    }

    public function testRenderByText()
    {
        $text = 'original text';
        $result = 'rendered text';

        $this->_translator->expects($this->once())
            ->method('getTheme')
            ->will($this->returnValue('theme'));
        $this->_translator->expects($this->once())
            ->method('getData')
            ->will($this->returnValue([
                'theme::' . $text => $result,
                $text => $result,
            ]));

        $this->assertEquals($result, $this->_renderer->render([$text], []));
    }
}
