<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\Modular;

class LayoutFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Layout\Argument\Parser
     */
    protected $_argParser;

    /**
     * @var \Magento\Data\Argument\InterpreterInterface
     */
    protected $_argInterpreter;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_argParser = $objectManager->get('Magento\View\Layout\Argument\Parser');
        $this->_argInterpreter = $objectManager->get('layoutArgumentInterpreter');
    }

    /**
     * @param string $area
     * @param string $layoutFile
     * @dataProvider layoutArgumentsDataProvider
     */
    public function testLayoutArguments($area, $layoutFile)
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')->loadArea($area);
        $dom = new \DOMDocument();
        $dom->load($layoutFile);
        $xpath = new \DOMXPath($dom);
        $argumentNodes = $xpath->query('/layout//arguments/argument | /layout//action/argument');
        /** @var \DOMNode $argumentNode */
        foreach ($argumentNodes as $argumentNode) {
            try {
                $argumentData = $this->_argParser->parse($argumentNode);
                $this->_argInterpreter->evaluate($argumentData);
            } catch (\Exception $e) {
                $this->fail($e->getMessage());
            }
        }
    }

    /**
     * @return array
     */
    public function layoutArgumentsDataProvider()
    {
        $areas = array('adminhtml', 'frontend', 'install', 'email');
        $data = array();
        foreach ($areas as $area) {
            $layoutFiles = \Magento\TestFramework\Utility\Files::init()->getLayoutFiles(array('area' => $area), false);
            foreach ($layoutFiles as $layoutFile) {
                $data[$layoutFile] = array($area, $layoutFile);
            }
        }
        return $data;
    }
}
