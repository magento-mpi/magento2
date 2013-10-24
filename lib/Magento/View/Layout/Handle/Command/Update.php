<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Handle\Command;

use Magento\View\Layout\Handle\AbstractHandle;
use Magento\View\LayoutInterface;
use Magento\View\Layout\Element;
use Magento\View\Layout\Handle\CommandInterface;
use Magento\View\Layout\Handle\Render;
use Magento\View\Layout\HandleFactory;
use Magento\View\Layout\ProcessorFactory;
use Magento\View\Layout\ProcessorInterface;
use Magento\View\Render\RenderFactory;
use Magento\Core\Model\Layout\Argument\Processor;

class Update extends AbstractHandle implements CommandInterface
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
     * @param HandleFactory $handleFactory
     * @param RenderFactory $renderFactory
     * @param Processor $argumentProcessor
     * @param ProcessorFactory $processorFactory
     */
    public function __construct(
        HandleFactory $handleFactory,
        RenderFactory $renderFactory,
        Processor $argumentProcessor,
        ProcessorFactory $processorFactory
    )
    {
        parent::__construct($handleFactory, $renderFactory, $argumentProcessor);

        $this->processorFactory = $processorFactory;
    }

    /**
     * @param Element $layoutElement
     * @param LayoutInterface $layout
     * @param string $parentName
     * @return Update
     */
    public function parse(Element $layoutElement, LayoutInterface $layout, $parentName)
    {
        $element = $this->parseAttributes($layoutElement);

        $element['type'] = self::TYPE;

        if (isset($parentName) && isset($element['handle'])) {
            // load layout handle
            $xml = $this->loadLayoutHandle($element['handle']);

            // parse layout elements as parent's elements
            $this->parseChildren($xml, $layout, $parentName);
        }

        return $this;
    }

    /**
     * @param $handle
     * @return Element
     */
    protected function loadLayoutHandle($handle)
    {
        /** @var $layoutProcessor ProcessorInterface */
        $layoutProcessor = $this->processorFactory->create();
        $layoutProcessor->load($handle);
        $xml = $layoutProcessor->asSimplexml();

        return $xml;
    }
}
