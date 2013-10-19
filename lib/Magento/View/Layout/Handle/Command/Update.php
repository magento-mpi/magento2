<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Handle\Command;

use Magento\View\Layout;
use Magento\View\Layout\Element;
use Magento\View\Layout\Handle;
use Magento\View\Layout\Handle\Command;
use Magento\View\Layout\Handle\Render;

use Magento\View\Layout\HandleFactory;

use Magento\View\Layout\ProcessorFactory;
use Magento\View\Layout\Processor;

class Update implements Command
{
    /**
     * Container type
     */
    const TYPE = 'update';

    /**
     * @var ProcessorFactory
     */
    protected $processorFactory;

    /**
     * @param ProcessorFactory $processorFactory
     * @param HandleFactory $handleFactory
     */
    public function __construct(
        ProcessorFactory $processorFactory,
        HandleFactory $handleFactory
    ) {
        $this->processorFactory = $processorFactory;
        $this->handleFactory = $handleFactory;
    }

    /**
     * @param Element $layoutElement
     * @param Layout $layout
     * @param string $parentName
     * @return Update
     */
    public function parse(Element $layoutElement, Layout $layout, $parentName)
    {
        $element = array();
        foreach ($layoutElement->attributes() as $attributeName => $attribute) {
            if ($attribute) {
                $element[$attributeName] = (string)$attribute;
            }
        }
        $element['type'] = self::TYPE;

        if (isset($parentName) && isset($element['handle'])) {
            /** @var $layoutProcessor Processor */
            $layoutProcessor = $this->processorFactory->create();
            $layoutProcessor->load($element['handle']);
            $xml = $layoutProcessor->asSimplexml();

            foreach ($xml as $childElement) {
                $type = $childElement->getName();
                /** @var $handle Handle */
                $handle = $this->handleFactory->get($type);
                $handle->parse($childElement, $layout, $parentName);
            }
        }

        return $this;
    }

    /**
     * @param array $element
     * @param Layout $layout
     * @param string $parentName
     * @return Update
     */
    public function register(array $element, Layout $layout, $parentName)
    {
        return $this;
    }
}
