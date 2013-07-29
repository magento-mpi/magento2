<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Translate_Phrase_Renderer_TranslateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Translate_TranslateInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_translator;

    /**
     * @var Magento_Translate_Phrase|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_phrase;

    /**
     * @var Magento_Translate_Phrase_Renderer_Translate
     */
    protected $_renderer;

    public function setUp()
    {
        $this->_translator = $this->getMock('Magento_Translate_TranslateInterface');
        $this->_phrase = $this->getMock('Magento_Translate_Phrase', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_renderer = $objectManagerHelper->getObject('Magento_Translate_Phrase_Renderer_Translate', array(
            'translator' => $this->_translator,
        ));
    }

    public function testTranslate()
    {
        $result = 'rendered text';

        $this->_phrase->expects($this->once())->method('getArguments')
            ->will($this->returnValue(array('param1', 'param2', 'param3')));
        $this->_phrase->expects($this->once())->method('getText')->will($this->returnValue('text'));

        $this->_translator->expects($this->once())->method('translate')->with('text', 'param1', 'param2', 'param3')
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->_renderer->render($this->_phrase));
    }
}
