<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout\Generator;

use Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Framework\Data\Argument\InterpreterInterface;
use Magento\Framework\View\Layout;

class UiComponent implements Layout\GeneratorInterface
{
    const TYPE = 'ui_component';

    /**
     * @var \Magento\Framework\View\Element\UiComponentFactory
     */
    protected $uiComponentFactory;

    /**
     * @var \Magento\Framework\Data\Argument\InterpreterInterface
     */
    protected $argumentInterpreter;

    /**
     * Constructor
     *
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\Data\Argument\InterpreterInterface $argumentInterpreter
     */
    public function __construct(
        UiComponentFactory $uiComponentFactory,
        InterpreterInterface $argumentInterpreter
    ) {
        $this->uiComponentFactory = $uiComponentFactory;
        $this->argumentInterpreter = $argumentInterpreter;
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
     * Creates UI Component object based on scheduled data and add it to the layout
     *
     * @param Layout\Reader\Context $readerContext
     * @param null $layout
     * @return $this
     */
    public function process(Layout\Reader\Context $readerContext, $layout = null)
    {
        $this->uiComponentFactory->setLayout($layout);
        /** @var $blocks \Magento\Framework\View\Element\AbstractBlock[] */
        $blocks = [];
        $scheduledStructure = $readerContext->getScheduledStructure();
        // Instantiate blocks and collect all actions data
        foreach ($scheduledStructure->getElements() as $elementName => $element) {
            list($type, $data) = $element;
            if ($type === self::TYPE) {
                $block = $this->generateComponent($readerContext, $elementName, $data);
                $blocks[$elementName] = $block;
                $layout->setBlock($elementName, $block);
                $scheduledStructure->unsetElement($elementName);
            }
        }
        foreach ($blocks as $block) {
            $block->setLayout($layout);
        }
        return $this;
    }

    /**
     * Create component object
     *
     * @param Layout\Reader\Context $readerContext
     * @param string $elementName
     * @param string $data
     * @return \Magento\Framework\View\Element\UiComponentInterface
     */
    protected function generateComponent(Layout\Reader\Context $readerContext, $elementName, $data)
    {
        $structure = $readerContext->getStructure();
        $attributes = $data['attributes'];
        if (!empty($attributes['group'])) {
            $structure->addToParentGroup($elementName, $attributes['group']);
        }
        $arguments = empty($data['arguments']) ? [] : $this->_evaluateArguments($data['arguments']);
        $componentName = isset($attributes['component']) ? $attributes['component'] : '';
        $uiComponent = $this->uiComponentFactory->createUiComponent($componentName, $elementName, $arguments);
        return $uiComponent;
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