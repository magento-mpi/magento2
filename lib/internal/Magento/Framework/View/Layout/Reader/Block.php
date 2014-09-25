<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout\Reader;

use Magento\Framework\View\Layout;

class Block implements Layout\ReaderInterface
{
    /**#@+
     * Supported types
     */
    const TYPE_BLOCK = 'block';
    const TYPE_REFERENCE_BLOCK = 'referenceBlock';
    const TYPE_ARGUMENTS = 'arguments';
    const TYPE_ACTION = 'action';
    /**#@-*/

    /**
     * @var \Magento\Framework\View\Layout\ScheduledStructure\Helper
     */
    protected $helper;

    /**
     * @var \Magento\Framework\View\Layout\Argument\Parser
     */
    protected $argumentParser;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Layout\ScheduledStructure\Helper $helper
     * @param \Magento\Framework\View\Layout\Argument\Parser $argumentParser
     */
    public function __construct(
        Layout\ScheduledStructure\Helper $helper,
        Layout\Argument\Parser $argumentParser
    ) {
        $this->helper = $helper;
        $this->argumentParser = $argumentParser;
    }

    /**
     * @return string[]
     */
    public function getSupportedNodes()
    {
        return [self::TYPE_BLOCK, self::TYPE_REFERENCE_BLOCK];
    }

    /**
     * {@inheritdoc}
     *
     * @param Context $readerContext
     * @param Layout\Element $currentElement
     * @param Layout\Element $parentElement
     * @return $this
     */
    public function process(Context $readerContext, Layout\Element $currentElement, Layout\Element $parentElement)
    {
        $isChildProcessed = false;
        switch ($currentElement->getName()) {
            case self::TYPE_BLOCK:
                $this->helper->scheduleStructure(
                    $readerContext->getScheduledStructure(),
                    $currentElement,
                    $parentElement
                );
                $isChildProcessed = true;
                break;

            case self::TYPE_REFERENCE_BLOCK:
                $isChildProcessed = true;
                break;

            default:
                break;
        }
        $this->processChildren($readerContext->getScheduledStructure(), $currentElement);
        return $isChildProcessed;
    }

    /**
     * @param Layout\ScheduledStructure $scheduledStructure
     * @param Layout\Element $blockElement
     * @return void
     */
    protected function processChildren(Layout\ScheduledStructure $scheduledStructure, Layout\Element $blockElement)
    {
        /** @var $childElement Layout\Element */
        foreach ($blockElement as $childElement) {
            if ($childElement->getName() === self::TYPE_ARGUMENTS) {
                $this->_processArguments($scheduledStructure, $childElement, $blockElement);
            } elseif ($childElement->getName() === self::TYPE_ACTION) {
                $this->_processActions($scheduledStructure, $childElement, $blockElement);
            }
        }
    }

    /**
     * Process arguments
     *
     * @param \Magento\Framework\View\Layout\ScheduledStructure $scheduledStructure
     * @param Layout\Element $currentElement
     * @param Layout\Element $parentElement
     */
    protected function _processArguments(
        Layout\ScheduledStructure $scheduledStructure,
        Layout\Element $currentElement,
        Layout\Element $parentElement
    ) {
        $referenceName = $parentElement->getAttribute('name');
        $element = $scheduledStructure->getStructureElement($referenceName, array());
        $args = $this->_parseArguments($currentElement);
        $element['arguments'] = $this->_mergeArguments($element, $args);
        $scheduledStructure->setStructureElement($referenceName, $element);
    }

    /**
     * Process actions
     *
     * @param \Magento\Framework\View\Layout\ScheduledStructure $scheduledStructure
     * @param Layout\Element $currentElement
     * @param Layout\Element $parentElement
     */
    protected function _processActions(
        Layout\ScheduledStructure $scheduledStructure,
        Layout\Element $currentElement,
        Layout\Element $parentElement
    ) {
        $referenceName = $parentElement->getAttribute('name');
        $element = $scheduledStructure->getStructureElement($referenceName, array());
        $element['actions'][] = array($currentElement, $parentElement);
        $scheduledStructure->setStructureElement($referenceName, $element);
    }

    /**
     * Parse argument nodes and return their array representation
     *
     * @param Layout\Element $node
     * @return array
     */
    protected function _parseArguments(Layout\Element $node)
    {
        $nodeDom = dom_import_simplexml($node);
        $result = array();
        foreach ($nodeDom->childNodes as $argumentNode) {
            if ($argumentNode instanceof \DOMElement && $argumentNode->nodeName == 'argument') {
                $argumentName = $argumentNode->getAttribute('name');
                $result[$argumentName] = $this->argumentParser->parse($argumentNode);
            }
        }
        return $result;
    }

    /**
     * Merge element arguments
     *
     * @param array $element
     * @param array $arguments
     * @return array
     */
    protected function _mergeArguments(array $element, array $arguments)
    {
        $output = $arguments;
        if (isset($element['arguments'])) {
            $output = array_replace_recursive($element['arguments'], $arguments);
        }
        return $output;
    }
}
