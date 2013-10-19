<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\View\Layout\Handle\Render;

use Magento\View\Layout\Handle\Render;

use Magento\View\Layout;
use Magento\View\Layout\Element;
use Magento\View\Layout\Handle;
use Magento\View\Layout\HandleFactory;
use Magento\View\Render\RenderFactory;
use Magento\View\Layout\ProcessorFactory;
use Magento\View\Layout\Processor;
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
     * @param LayoutFactory $layoutFactory
     */
    public function __construct(
        ProcessorFactory $processorFactory,
        HandleFactory $handleFactory,
        RenderFactory $renderFactory,
        LayoutFactory $layoutFactory
    ) {
        $this->processorFactory = $processorFactory;
        $this->handleFactory = $handleFactory;
        $this->renderFactory = $renderFactory;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * @param Element $layoutElement
     * @param Layout $layout
     * @param string $parentName
     * @return Preset
     */
    public function parse(Element $layoutElement, Layout $layout, $parentName)
    {
        $elementName = $layoutElement->getAttribute('name');
        if (isset($elementName)) {
            $element = array();
            foreach ($layoutElement->attributes() as $attributeName => $attribute) {
                if ($attribute) {
                    $element[$attributeName] = (string)$attribute;
                }
            }
            $element['type'] = self::TYPE;

            $layout->addElement($elementName, $element);

            if (isset($parentName)) {
                $alias = isset($element['as']) ? $element['as'] : $elementName;
                $layout->setChild($parentName, $elementName, $alias);
            }

            if (isset($parentName) && isset($element['handle'])) {
                $personalLayout = $this->layoutFactory->create();

                /** @var $layoutProcessor Processor */
                $layoutProcessor = $this->processorFactory->create();
                $layoutProcessor->load($element['handle']);
                $xml = $layoutProcessor->asSimplexml();

                foreach ($xml as $childElement) {
                    $type = $childElement->getName();
                    /** @var $handle Handle */
                    $handle = $this->handleFactory->get($type);
                    $handle->parse($childElement, $personalLayout, $elementName);
                }

                // parse children
                if ($layoutElement->hasChildren()) {
                    foreach ($layoutElement as $childXml) {
                        /** @var $childXml Element */
                        $type = $childXml->getName();
                        /** @var $handle Handle */
                        $handle = $this->handleFactory->get($type);
                        $handle->parse($childXml, $personalLayout, $elementName);
                    }
                }
                $node['layout'] = $personalLayout;
            }
        }

        return $this;
    }

    /**
     * @param array $element
     * @param Layout $layout
     * @param string $parentName
     */
    public function register(array $element, Layout $layout, $parentName)
    {
        if (isset($element['name']) && !isset($element['is_registered'])) {
            $elementName = $element['name'];

            $layout->setElementAttribute($elementName, 'is_registered', true);

            foreach ($layout->getChildNames($elementName) as $childName => $alias) {
                $child = $layout->getElement($childName);
                /** @var $handle Render */
                $handle = $this->handleFactory->get($child['type']);
                $handle->register($child, $layout, $elementName);
            }
        }
    }

    /**
     * @param array $elements
     * @param Layout $layout
     * @param $type
     * @return mixed
     */
    public function render(array $elements, Layout $layout, $type = Html::TYPE_HTML)
    {
        $result = '';

        if (isset($element['name'])) {
            $elementName = $element['name'];
            foreach ($layout->getChildNames($elementName) as $childName => $alias) {
                $child = $layout->getElement($childName);
                /** @var $handle Render */
                $handle = $this->handleFactory->get($child['type']);
                if ($handle instanceof Render) {
                    $result .= $handle->render($child, $layout, $type);
                }
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
