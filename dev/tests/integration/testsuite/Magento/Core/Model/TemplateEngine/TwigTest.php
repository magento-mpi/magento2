<?php
/**
 * {license_notice}
 *
 * Integration test of twig engine and classes it calls.
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_TemplateEngine_TwigTest extends PHPUnit_Framework_TestCase
{
    /** @var  Magento_Core_Model_TemplateEngine_Twig */
    protected $_twigEngine;

    /**
     * @var Magento_TestFramework_ObjectManager
     */
    protected $_objectManager;

    /**
     * Create a Twig template engine to test.
     */
    protected function setUp()
    {
        $this->_objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        Mage::app()->loadAreaPart(Magento_Core_Model_App_Area::AREA_GLOBAL, Magento_Core_Model_App_Area::PART_CONFIG);
        $this->_twigEngine = $this->_objectManager->create('Magento_Core_Model_TemplateEngine_Twig');
    }

    /**
     * Render a twig file using the Magento Twig Template Engine.
     *
     * @param Magento_Core_Block_Template $block
     * @param $fileName
     * @param array $dictionary
     * @return string
     */
    public function render(Magento_Core_Block_Template $block, $fileName, array $dictionary = array())
    {
        return $this->_twigEngine->render($block, $fileName, $dictionary);
    }

    /**
     * Test the render() function with a very simple .twig file.
     *
     * Template will include a title taken from the dictionary passed in.
     */
    public function testSimpleRender()
    {
        Mage::app()->loadAreaPart(Magento_Core_Model_App_Area::AREA_FRONTEND, Magento_Core_Model_App_Area::PART_DESIGN);
        $simpleTitle = 'This is the Title';
        $renderedOutput = '<html><head><title>' . $simpleTitle . '</title></head><body></body></html>';
        $path = __DIR__ . '/_files';
        $blockMock = $this->getMockBuilder('Magento_Core_Block_Template')
            ->disableOriginalConstructor()->getMock();

        $dictionary = array(
            'simple' => array(
                'title' => $simpleTitle
            )
        );

        $actualOutput = $this->render($blockMock, $path . '/simple.twig', $dictionary);
        $this->assertSame($renderedOutput, $actualOutput, 'Twig file did not render properly');
    }
}
