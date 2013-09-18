<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Phrase_Renderer_PlaceholderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Phrase\Renderer\Placeholder
     */
    protected $_renderer;

    protected function setUp()
    {
        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_renderer = $objectManagerHelper->getObject('Magento\Phrase\Renderer\Placeholder');
    }

    public function testRenderPlaceholder()
    {
        $result = 'text param1 param2';
        $this->assertEquals($result, $this->_renderer->render('text %1 %2', array('param1', 'param2')));
    }
}
