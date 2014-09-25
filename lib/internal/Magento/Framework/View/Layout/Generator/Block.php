<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout\Generator;

use Magento\Framework\View\Layout;

class Block
{
    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    protected $blockFactory;

    /**
     * @var \Magento\Framework\Data\Argument\InterpreterInterface
     */
    protected $argumentInterpreter;

    /**
     * @var \Magento\Framework\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\ScopeResolverInterface
     */
    protected $scopeResolver;

    /**
     * @var string|null
     */
    protected $scopeType;

    protected $_blocks = [];

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
     * @param \Magento\Framework\Data\Argument\InterpreterInterface $argumentInterpreter
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\ScopeResolverInterface $scopeResolver
     * @param null $scopeType
     */
    public function __construct(
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Framework\Data\Argument\InterpreterInterface $argumentInterpreter,
        \Magento\Framework\Logger $logger,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ScopeResolverInterface $scopeResolver,
        $scopeType = null
    ) {
        $this->blockFactory = $blockFactory;
        $this->argumentInterpreter = $argumentInterpreter;
        $this->logger = $logger;
        $this->eventManager = $eventManager;
        $this->scopeConfig = $scopeConfig;
        $this->scopeResolver = $scopeResolver;
        $this->scopeType = $scopeType;
    }

    /**
     * @param \Magento\Framework\View\Layout\Reader\Context $readerContext
     * @param string $elementName
     * @return $this
     */
    public function generate(Layout\Reader\Context $readerContext, $elementName)
    {
        $this->_generateBlock($readerContext->getScheduledStructure(), $readerContext->getStructure(), $elementName);
        return $this;
    }

    /**
     * Creates block object based on xml node data and add it to the layout
     *
     * @param \Magento\Framework\View\Layout\ScheduledStructure $scheduledStructure
     * @param \Magento\Framework\Data\Structure $structure
     * @param string $elementName
     * @throws \Magento\Framework\Exception
     * @return \Magento\Framework\View\Element\AbstractBlock|void
     */
    protected function _generateBlock(
        Layout\ScheduledStructure $scheduledStructure,
        \Magento\Framework\Data\Structure $structure,
        $elementName
    ) {
        /** @var $node Layout\Element */
        list($type, $node, $actions, $args) = $scheduledStructure->getElement($elementName);
        if ($type !== Layout\Element::TYPE_BLOCK) {
            throw new \Magento\Framework\Exception("Unexpected element type specified for generating block: {$type}.");
        }

        $configPath = (string)$node->getAttribute('ifconfig');
        if (!empty($configPath)
            && !$this->scopeConfig->isSetFlag($configPath, $this->scopeType, $this->scopeResolver->getScope())
        ) {
            $scheduledStructure->unsetElement($elementName);
            return;
        }

        $group = (string)$node->getAttribute('group');
        if (!empty($group)) {
            $structure->addToParentGroup($elementName, $group);
        }

        // create block
        $className = (string)$node['class'];

        $arguments = $this->_evaluateArguments($args);

        $block = $this->_createBlock($className, $elementName, array('data' => $arguments));

        if (!empty($node['template'])) {
            $templateFileName = (string)$node['template'];
            $block->setTemplate($templateFileName);
        }

        if (!empty($node['ttl'])) {
            $ttl = (int)$node['ttl'];
            $block->setTtl($ttl);
        }

        $scheduledStructure->unsetElement($elementName);

        // execute block methods
        foreach ($actions as $action) {
            list($actionNode, $parent) = $action;
            $this->_generateAction($block, $actionNode, $parent);
        }

        return $block;
    }

    /**
     * Create block and add to layout
     *
     * @param string|\Magento\Framework\View\Element\AbstractBlock $block
     * @param string $name
     * @param array $attributes
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _createBlock($block, $name, array $attributes = array())
    {
        $block = $this->_getBlockInstance($block, $attributes);

        $block->setType(get_class($block));
        $block->setNameInLayout($name);
        $block->addData(isset($attributes['data']) ? $attributes['data'] : array());
        $block->setLayout($this);

        $this->_blocks[$name] = $block;
        $this->eventManager->dispatch('core_layout_block_create_after', array('block' => $block));
        return $this->_blocks[$name];
    }

    /**
     * Create block object instance based on block type
     *
     * @param string|\Magento\Framework\View\Element\AbstractBlock $block
     * @param array $attributes
     * @throws \Magento\Framework\Model\Exception
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _getBlockInstance($block, array $attributes = array())
    {
        if ($block && is_string($block)) {
            try {
                $block = $this->blockFactory->createBlock($block, $attributes);
            } catch (\ReflectionException $e) {
                $this->logger->log($e->getMessage());
            }
        }
        if (!$block instanceof \Magento\Framework\View\Element\AbstractBlock) {
            throw new \Magento\Framework\Model\Exception(__('Invalid block type: %1', $block));
        }
        return $block;
    }

    /**
     * Run action defined in layout update
     *
     * @param \Magento\Framework\View\Element\AbstractBlock $block
     * @param \Magento\Framework\View\Layout\Element $node
     * @param \Magento\Framework\View\Layout\Element $parent
     * @return void
     */
    protected function _generateAction($block, $node, $parent)
    {
        $configPath = $node->getAttribute('ifconfig');
        if ($configPath
            && !$this->scopeConfig->isSetFlag($configPath, $this->scopeType, $this->scopeResolver->getScope())
        ) {
            return;
        }

        $method = $node->getAttribute('method');
        $parentName = $node->getAttribute('block');
        if (empty($parentName)) {
            $parentName = $parent->getElementName();
        }

        $profilerKey = 'BLOCK_ACTION:' . $parentName . '>' . $method;
        \Magento\Framework\Profiler::start($profilerKey);
        $parentBlock = $block->getParentBlock();
        if (!empty($parentBlock)) {
            $args = $this->_parseArguments($node);
            $args = $this->_evaluateArguments($args);
            call_user_func_array(array($parentBlock, $method), $args);
        }
        \Magento\Framework\Profiler::stop($profilerKey);
    }

    /**
     * Compute and return argument values
     *
     * @param array $arguments
     * @return array
     */
    protected function _evaluateArguments(array $arguments)
    {
        $result = array();
        foreach ($arguments as $argumentName => $argumentData) {
            $result[$argumentName] = $this->argumentInterpreter->evaluate($argumentData);
        }
        return $result;
    }

    /**
     * Parse argument nodes and return their array representation
     *
     * @param \Magento\Framework\View\Layout\Element $node
     * @return array
     */
    protected function _parseArguments(\Magento\Framework\View\Layout\Element $node)
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
}