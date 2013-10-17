<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Handle\Command;

use Magento\View\Context;
use Magento\View\Layout;
use Magento\View\Layout\Element;
use Magento\View\Layout\Handle;
use Magento\View\Layout\Handle\Command;
use Magento\View\Layout\Handle\Render;

use Magento\View\Layout\HandleFactory;

use Magento\View\Layout\ProcessorFactory;
use Magento\View\Layout\Processor;
use Magento\View\Layout\Reader;
use Magento\View\LayoutFactory;

class Update implements Comman
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
     * @var Reader
     */
    protected $layoutReader;

    /**
     * @var LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @param ProcessorFactory $processorFactory
     * @param HandleFactory $handleFactory
     * @param Reader $layoutReader
     * @param LayoutFactory $layoutFactory
     */
    public function __construct(
        ProcessorFactory $processorFactory,
        HandleFactory $handleFactory,
        Reader $layoutReader,
        LayoutFactory $layoutFactory
    ) {
        $this->processorFactory = $processorFactory;
        $this->handleFactory = $handleFactory;
        $this->layoutReader = $layoutReader;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * @param Element $layoutElement
     * @param Layout $layout
     * @param array $parentNode
     */
    public function parse(Element $layoutElement, Layout $layout, array & $parentNode = null)
    {
        $node = array();
        foreach ($layoutElement->attributes() as $attributeName => $attribute) {
            if ($attribute) {
                $node[$attributeName] = (string)$attribute;
            }
        }
        $node['type'] = self::TYPE;

        if (isset($parentNode) && isset($node['handle'])) {
            /** @var $layoutProcessor Processor */
            $layoutProcessor = $this->processorFactory->create();
            $layoutProcessor->load($node['handle']);
            $xml = $layoutProcessor->asSimplexml();

            foreach ($xml as $childElement) {
                $type = $childElement->getName();
                /** @var $handle Handle */
                $handle = $this->handleFactory->get($type);
                $handle->parse($childElement, $layout, $parentNode);
            }
        }
    }

    /**
     * @param array $meta
     * @param Layout $layout
     * @param array $parentNode
     */
    public function register(array & $meta, Layout $layout, array & $parentNode = null)
    {
        // TODO:
    }
}
