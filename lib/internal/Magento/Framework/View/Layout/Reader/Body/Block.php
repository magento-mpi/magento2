<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout\Reader\Body;

use Magento\Framework\View\Layout;
use Magento\Framework\View\Layout\Reader\Context;
use Magento\Framework\App;

class Block implements Layout\ReaderInterface
{
    /**#@+
     * Supported types
     */
    const TYPE_BLOCK = 'block';
    const TYPE_REFERENCE_BLOCK = 'referenceBlock';
    /**#@-*/

    /**#@+
     * Supported subtypes for blocks
     */
    const TYPE_ARGUMENTS = 'arguments';
    const TYPE_ACTION = 'action';
    /**#@-*/

    /**
     * @var array
     */
    protected $attributes = ['group', 'class', 'template', 'ttl'];

    /**
     * @var \Magento\Framework\View\Layout\ScheduledStructure\Helper
     */
    protected $helper;

    /**
     * @var \Magento\Framework\View\Layout\Argument\Parser
     */
    protected $argumentParser;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\ScopeResolverInterface
     */
    protected $scopeResolver;

    /**
     * @var string
     */
    protected $scopeType;

    /**
     * Constructor
     *
     * @param Layout\ScheduledStructure\Helper $helper
     * @param Layout\Argument\Parser $argumentParser
     * @param App\Config\ScopeConfigInterface $scopeConfig
     * @param App\ScopeResolverInterface $scopeResolver
     */
    public function __construct(
        Layout\ScheduledStructure\Helper $helper,
        Layout\Argument\Parser $argumentParser,
        App\Config\ScopeConfigInterface $scopeConfig,
        App\ScopeResolverInterface $scopeResolver
    ) {
        $this->helper = $helper;
        $this->argumentParser = $argumentParser;
        $this->scopeConfig = $scopeConfig;
        $this->scopeResolver = $scopeResolver;
        // TODO: Must be included through DI configuration
        $this->scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
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
        $scheduledStructure = $readerContext->getScheduledStructure();
        switch ($currentElement->getName()) {
            case self::TYPE_BLOCK:
                $this->processBlock($scheduledStructure, $currentElement, $parentElement);
                $this->processChildren($readerContext->getScheduledStructure(), $currentElement);
                $isChildProcessed = true;
                break;

            case self::TYPE_REFERENCE_BLOCK:
                $this->processChildren($readerContext->getScheduledStructure(), $currentElement);
                $isChildProcessed = true;
                break;

            default:
                break;
        }
        return $isChildProcessed;
    }

    /**
     * @param Layout\ScheduledStructure $scheduledStructure
     * @param Layout\Element $currentElement
     * @param Layout\Element $parentElement
     */
    protected function processBlock(
        Layout\ScheduledStructure $scheduledStructure,
        Layout\Element $currentElement,
        Layout\Element $parentElement
    ) {
        $referenceName = $this->helper->scheduleStructure($scheduledStructure, $currentElement, $parentElement);
        $element = $scheduledStructure->getStructureElement($referenceName, array());
        $element['attributes'] = $this->processAttributes($currentElement);
        $scheduledStructure->setStructureElement($referenceName, $element);

        $configPath = (string)$currentElement->getAttribute('ifconfig');
        if (!empty($configPath)
            && !$this->scopeConfig->isSetFlag($configPath, $this->scopeType, $this->scopeResolver->getScope())
        ) {
            $scheduledStructure->setElementToRemoveList($referenceName);
        }
    }

    /**
     * Process block attributes
     *
     * @param Layout\Element $blockElement
     * @return array
     */
    protected function processAttributes(Layout\Element $blockElement)
    {
        $attributes = [];
        foreach ($this->attributes as $attributeName) {
            $attributes[$attributeName] = (string)$blockElement->getAttribute($attributeName);
        }
        return $attributes;
    }

    /**
     * Process children
     *
     * @param Layout\ScheduledStructure $scheduledStructure
     * @param Layout\Element $blockElement
     * @return void
     */
    protected function processChildren(Layout\ScheduledStructure $scheduledStructure, Layout\Element $blockElement)
    {
        /** @var $childElement Layout\Element */
        foreach ($blockElement as $childElement) {
            switch ($childElement->getName()) {
                case self::TYPE_ARGUMENTS:
                    $this->_processArguments($scheduledStructure, $childElement, $blockElement);
                    break;

                case self::TYPE_ACTION:
                    $this->_processActions($scheduledStructure, $childElement, $blockElement);
                    break;

                default:
                    break;
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
        $configPath = $currentElement->getAttribute('ifconfig');
        if ($configPath
            && !$this->scopeConfig->isSetFlag($configPath, $this->scopeType, $this->scopeResolver->getScope())
        ) {
            return;
        }

        $referenceName = $parentElement->getAttribute('name');
        $element = $scheduledStructure->getStructureElement($referenceName, array());
        $methodName = $currentElement->getAttribute('method');
        $actionArguments = $this->_parseArguments($currentElement);
        $element['actions'][] = [$methodName, $actionArguments];
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
