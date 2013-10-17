<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\View\Layout\Handle\Render;

use Magento\View\Layout\Handle\Render;

use Magento\View\Context;
use Magento\View\Layout;
use Magento\View\Layout\Element;
use Magento\View\Layout\Handle;
use Magento\View\Layout\HandleFactory;
use Magento\View\Render\RenderFactory;
use Magento\View\Layout\ProcessorFactory;
use Magento\View\Layout\Processor;
use Magento\View\Layout\Reader;
use Magento\View\LayoutFactory;
use Magento\View\Render\Html;

class Preset implements Render
{
    const TYPE = 'preset';

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
     * @var \Magento\View\Layout\HandleFactory
     */
    protected $handleFactory;


    /**
     * @var \Magento\View\Render\RenderFactory
     */
    protected $renderFactory;

    /**
     * @param ProcessorFactory $processorFactory
     * @param HandleFactory $handleFactory
     * @param RenderFactory $renderFactory
     * @param Reader $layoutReader
     * @param LayoutFactory $layoutFactory
     */
    public function __construct(
        ProcessorFactory $processorFactory,
        HandleFactory $handleFactory,
        RenderFactory $renderFactory,
        Reader $layoutReader,
        LayoutFactory $layoutFactory
    )
    {
        $this->processorFactory = $processorFactory;
        $this->handleFactory = $handleFactory;
        $this->renderFactory = $renderFactory;
        $this->layoutReader = $layoutReader;
        $this->layoutFactory = $layoutFactory;
    }

    public function parse(Element $layoutElement, Layout $layout, array & $parentNode = null)
    {
        $name = $layoutElement->getAttribute('name');
        if (isset($name)) {
            $element = & $layout->getElement($name);
            foreach ($layoutElement->attributes() as $attributeName => $attribute) {
                if ($attribute) {
                    $element[$attributeName] = (string)$attribute;
                }
            }
            $element['type'] = self::TYPE;

            $alias = isset($element['as']) ? $element['as'] : $name;
            if (isset($alias) && $parentNode) {
                $parentNode['children'][$alias] = & $element;
            }

            if (isset($parentNode) && isset($element['handle'])) {
                $personalLayout = $this->layoutFactory->create();

                /** @var $layoutProcessor Processor */
                $layoutProcessor = $this->processorFactory->create();
                $layoutProcessor->load($element['handle']);
                $xml = $layoutProcessor->asSimplexml();

                foreach ($xml as $childElement) {
                    $type = $childElement->getName();
                    /** @var $handle Handle */
                    $handle = $this->handleFactory->get($type);
                    $handle->parse($childElement, $personalLayout, $element);
                }

                // parse children
                if ($layoutElement->hasChildren()) {
                    foreach ($layoutElement as $childXml) {
                        /** @var $childXml Element */
                        $type = $childXml->getName();
                        /** @var $handle Handle */
                        $handle = $this->handleFactory->get($type);
                        $handle->parse($childXml, $personalLayout, $element);
                    }
                }
                $node['layout'] = $personalLayout;
            }
        }


    }

    public function register(array & $meta, Layout $layout, array & $parentNode = null)
    {
        if (isset($meta['children'])) {
            foreach ($meta['children'] as & $child) {
                $child['parent'] = & $meta;
                /** @var $handle Render */
                $handle = $this->handleFactory->get($child['type']);
                $handle->register($child, $meta['layout'], $meta);
            }
        }
    }

    public function render(array & $meta, Layout $layout, array & $parentNode = null, $type = Html::TYPE_HTML)
    {
        $result = '';

        $children = isset($meta['children']) ? $meta['children'] : array();
        foreach ($children as $child) {
            /** @var $handle Render */
            $handle = $this->handleFactory->get($child['type']);
            if ($handle instanceof Render) {
                $result .= $handle->render($child, $layout, $parentNode, $type);
            }
        }

        $render = $this->renderFactory->get($type);

        $containerInfo['label'] = isset($meta['label']) ? $meta['label'] : null;
        $containerInfo['tag'] = isset($meta['htmlTag']) ? $meta['htmlTag'] : null;
        $containerInfo['class'] = isset($meta['htmlClass']) ? $meta['htmlClass'] : null;
        $containerInfo['id'] = isset($meta['htmlId']) ? $meta['htmlId'] : null;

        $result = $render->renderContainer($result, $containerInfo);

        return $result;
    }
}
