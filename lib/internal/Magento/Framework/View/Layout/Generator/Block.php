<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout\Generator;

use Magento\Framework\View\Layout;

class Block implements Layout\GeneratorInterface
{
    const TYPE = 'block';

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
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
     * @param \Magento\Framework\Data\Argument\InterpreterInterface $argumentInterpreter
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\View\LayoutInterface\Proxy $layout
     */
    public function __construct(
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Framework\Data\Argument\InterpreterInterface $argumentInterpreter,
        \Magento\Framework\Logger $logger,
        \Magento\Framework\View\LayoutInterface\Proxy $layout
    ) {
        $this->blockFactory = $blockFactory;
        $this->argumentInterpreter = $argumentInterpreter;
        $this->logger = $logger;
        // has to be not injected as shared object
        $this->layout = $layout;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * Creates block object based on xml node data and add it to the layout
     *
     * @param \Magento\Framework\View\Layout\Reader\Context $readerContext
     * @param string $elementName
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @return $this
     */
    public function process(Layout\Reader\Context $readerContext, $elementName, $layout = null)
    {
        $scheduledStructure = $readerContext->getScheduledStructure();
        $structure = $readerContext->getStructure();

        $row = $scheduledStructure->getElement($elementName);
        list(, $data) = $row;
        $attributes = $data['attributes'];

        if (!empty($attributes['group'])) {
            $structure->addToParentGroup($elementName, $attributes['group']);
        }

        // create block
        $className = $attributes['class'];
        $block = $this->createBlock($className, $elementName, [
            'data' => $this->_evaluateArguments($data['arguments'])
        ]);
        if (!empty($attributes['template'])) {
            $block->setTemplate($attributes['template']);
        }
        if (!empty($attributes['ttl'])) {
            $ttl = (int)$attributes['ttl'];
            $block->setTtl($ttl);
        }
        $layout->setBlock($elementName, $block);
        // execute block methods
        foreach ($data['actions'] as $action) {
            list($methodName, $actionArguments) = $action;
            $this->_generateAction($block, $methodName, $actionArguments);
        }
        return $block;
    }

    /**
     * Create block instance
     *
     * @param string|\Magento\Framework\View\Element\AbstractBlock $block
     * @param string $name
     * @param array $arguments
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    public function createBlock($block, $name, array $arguments = [])
    {
        $block = $this->_getBlockInstance($block, $arguments);
        $block->setType(get_class($block));
        $block->setNameInLayout($name);
        $block->addData(isset($arguments['data']) ? $arguments['data'] : []);
        return $block;
    }

    /**
     * Create block object instance based on block type
     *
     * @param string|\Magento\Framework\View\Element\AbstractBlock $block
     * @param array $arguments
     * @throws \Magento\Framework\Model\Exception
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _getBlockInstance($block, array $arguments = [])
    {
        if ($block && is_string($block)) {
            try {
                $block = $this->blockFactory->createBlock($block, $arguments);
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
     * @param string $methodName
     * @param array $actionArguments
     * @return void
     */
    protected function _generateAction($block, $methodName, $actionArguments)
    {
        $profilerKey = 'BLOCK_ACTION:' . $block->getNameInLayout() . '>' . $methodName;
        \Magento\Framework\Profiler::start($profilerKey);
        $args = $this->_evaluateArguments($actionArguments);
        call_user_func_array(array($block, $methodName), $args);
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
}