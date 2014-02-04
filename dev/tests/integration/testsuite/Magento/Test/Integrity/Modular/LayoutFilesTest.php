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
                if ($this->isSkippedArgument($argumentData)) {
                    continue;
                }
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

    /**
     * Whether an argument should be skipped, because it cannot be evaluated in the testing environment
     *
     * @param array $argumentData
     * @return bool
     */
    protected function isSkippedArgument(array $argumentData)
    {
        // Do not take into account argument name and parameters
        unset($argumentData['name']);
        unset($argumentData['param']);

        $isUpdater = isset($argumentData['updater']);
        unset($argumentData['updater']);

        // Arguments, evaluation of which causes a run-time error, because of unsafe assumptions to the environment
        $ignoredArguments = array(
            array('xsi:type' => 'object',
                'value' => 'Magento\GroupedProduct\Model\Resource\Product\Type\Grouped\AssociatedProductsCollection'),
            array('xsi:type' => 'object',
                'value' => 'Magento\Catalog\Model\Resource\Product\Collection\AssociatedProduct'),
            array('xsi:type' => 'object', 'value' => 'Magento\Search\Model\Resource\Search\Grid\Collection'),
            array('xsi:type' => 'object', 'value' => 'Magento\Wishlist\Model\Resource\Item\Collection\Grid'),
            array('xsi:type' => 'object',
                'value' => 'Magento\CustomerSegment\Model\Resource\Segment\Report\Detail\Collection'),
            array('xsi:type' => 'helper', 'helper' => 'Magento\Pbridge\Helper\Data::getReviewButtonTemplate'),
            array('xsi:type' => 'options', 'model' => 'Magento\Search\Model\Adminhtml\Search\Grid\Options'),
            array('xsi:type' => 'options', 'model' => 'Magento\Logging\Model\Resource\Grid\ActionsGroup'),
            array('xsi:type' => 'options', 'model' => 'Magento\Logging\Model\Resource\Grid\Actions'),
        );
        $isIgnoredArgument = in_array($argumentData, $ignoredArguments, true);

        unset($argumentData['xsi:type']);
        $hasValue = !empty($argumentData);

        return $isIgnoredArgument || ($isUpdater && !$hasValue);
    }
}
