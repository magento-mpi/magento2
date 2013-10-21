<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\View\Layout\Handle;

//use Magento\View\Context;
use Magento\View\Layout;
use Magento\View\Layout\Element;
use Magento\View\Layout\Handle;

use Magento\Core\Model\Layout\Argument\Processor;

class Arguments implements Handle
{
    const TYPE = 'arguments';

    /**
     * @var Processor
     */
    protected $argumentProcessor;

    /**
     * @param Processor $argumentProcessor
     */
    public function __construct(Processor $argumentProcessor)
    {
        $this->argumentProcessor = $argumentProcessor;
    }

    /**
     * @inheritdoc
     *
     * @param Element $layoutElement
     * @param Layout $layout
     * @param string $parentName
     * @return Arguments
     */
    public function parse(Element $layoutElement, Layout $layout, $parentName)
    {
        $element = array();
        foreach ($layoutElement->attributes() as $attributeName => $attribute) {
            if ($attribute) {
                $element[$attributeName] = (string)$attribute;
            }
        }

        $parsedArguments = $this->parseArguments($layoutElement);

        $arguments = $this->processArguments($parsedArguments);

        $layout->updateElement($parentName, array('arguments' => $arguments));

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @param array $element
     * @param Layout $layout
     * @param string $parentName
     */
    public function register(array $element, Layout $layout, $parentName)
    {
        //
    }

    /**
     * Parse argument nodes and create prepared array of items
     *
     * @param Element $node
     * @return array
     */
    protected function parseArguments(Element $node)
    {
        $arguments = array();
        foreach ($node->xpath('argument') as $argument) {
            /** @var $argument Element */
            $argumentName = (string)$argument['name'];
            $arguments[$argumentName] = $this->argumentProcessor->parse($argument);
        }
        return $arguments;
    }

    /**
     * Process arguments
     *
     * @param array $arguments
     * @return array
     */
    protected function processArguments(array $arguments)
    {
        $result = array();
        foreach ($arguments as $name => $argument) {
            $result[$name] = $this->argumentProcessor->process($argument);
        }
        return $result;
    }
}
