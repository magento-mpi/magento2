<?php
/**
 * {license_notice}
 *
 * Integration test of twig engine and classes it calls.
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_TemplateEngine_TwigTest extends PHPUnit_Framework_TestCase
{
    /** @var  Mage_Core_Model_TemplateEngine_Twig */
    protected $_twigEngine;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    protected $_designPackageMock;

    /**
     * @var Magento_Test_ObjectManager
     */
    protected $_objectManager;

    /**
     * Create a Twig template engine to test.
     */
    protected function setUp()
    {
        $this->_objectManager = Mage::getObjectManager();
        Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_GLOBAL, Mage_Core_Model_App_Area::PART_CONFIG);
        $this->_createSharedInstances();
        $this->_twigEngine = $this->_objectManager->create('Mage_Core_Model_TemplateEngine_Twig');
    }

    /**
     * Create any shared instances of objects to override default behavior.
     *
     * Need to mock out designPackage because the real one won't get initialized
     * properly and there will be a failure in the templateEngine getFunctions() method.
     * This is a good place for subclasses to setup mocks.
     */
    protected function _createSharedInstances()
    {
        $this->_designPackageMock = $this->getMockBuilder('Mage_Core_Model_Design_Package')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_objectManager->addSharedInstance($this->_designPackageMock, 'Mage_Core_Model_Design_Package');
    }

    /**
     * Render a twig file using the Magento Twig Template Engine.
     *
     * @param Mage_Core_Block_Template $block
     * @param $fileName
     * @param array $dictionary
     * @return string
     */
    public function render(Mage_Core_Block_Template $block, $fileName, array $dictionary = array())
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
        $simpleTitle = 'This is the Title';
        $renderedOutput = '<html><head><title>' . $simpleTitle . '</title></head><body></body></html>';
        $path = __DIR__ . '/_files';
        $blockMock = $this->getMockBuilder('Mage_Core_Block_Template')
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
