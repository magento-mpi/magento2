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
     * @var \Magento\Core\Model\Translate|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_translator;

    /**
     * @var \Magento\Phrase\Renderer\Translate
     */
    protected $_renderer;

    protected function setUp()
    {
        $this->_translator = $this->getMock('Magento\Core\Model\Translate', array(), array(), '', false);

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_renderer = $objectManagerHelper->getObject('Magento\Phrase\Renderer\Translate', array(
            'translator' => $this->_translator,
        ));
    }

    public function testTranslate()
    {
        $result = 'rendered text';

        $this->_translator->expects($this->once())->method('translate')
            ->with(array('text', 'param1', 'param2', 'param3'))
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->_renderer->render('text', array('param1', 'param2', 'param3')));
    }
}
