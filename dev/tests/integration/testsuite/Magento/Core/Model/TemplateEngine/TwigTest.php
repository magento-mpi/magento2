<?php
/**
 * {license_notice}
 *
 * Integration test of twig engine and classes it calls.
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\TemplateEngine;

class TwigTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \Magento\Core\Model\TemplateEngine\Twig */
    protected $_twigEngine;

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $_objectManager;

    /**
     * Create a Twig template engine to test.
     */
    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')->loadAreaPart(
            \Magento\Core\Model\App\Area::AREA_GLOBAL,
            \Magento\Core\Model\App\Area::PART_CONFIG
        );
        $this->_twigEngine = $this->_objectManager->create('Magento\Core\Model\TemplateEngine\Twig');
    }

    /**
     * Render a twig file using the Magento Twig Template Engine.
     *
     * @param \Magento\Core\Block\Template $block
     * @param $fileName
     * @param array $dictionary
     * @return string
     */
    public function render(\Magento\Core\Block\Template $block, $fileName, array $dictionary = array())
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
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')
            ->loadAreaPart(\Magento\Core\Model\App\Area::AREA_FRONTEND, \Magento\Core\Model\App\Area::PART_DESIGN);
        $simpleTitle = 'This is the Title';
        $renderedOutput = '<html><head><title>' . $simpleTitle . '</title></head><body></body></html>';
        $path = __DIR__ . '/_files';
        $blockMock = $this->getMockBuilder('Magento\Core\Block\Template')
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
