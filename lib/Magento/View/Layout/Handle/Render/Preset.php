<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\View\Layout\Handle\Render;

use Magento\View\Layout\Handle\Render;

use Magento\View\LayoutInterface;
use Magento\View\Layout\Element;
use Magento\View\Layout\Handle;
use Magento\View\Layout\HandleFactory;
use Magento\View\Render\RenderFactory;
use Magento\View\Layout\ProcessorFactory;
use Magento\View\Layout\ProcessorInterface;
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
     * @var HandleFactory
     */
    protected $handleFactory;

    /**
     * @var RenderFactory
     */
    protected $renderFactory;

    /**
     * @var LayoutFactory
     */
    protected $layoutFactory;

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
     * @param LayoutInterface $layout
     * @param string $parentName
     * @return Preset
     */
    public function parse(Element $layoutElement, LayoutInterface $layout, $parentName)
    {
        $elementName = $layoutElement->getAttribute('name');
        if (isset($elementName)) {
            $arguments = $element = array();
            foreach ($layoutElement->attributes() as $attributeName => $attribute) {
                if ($attribute) {
                    $arguments[$attributeName] = (string)$attribute;
                }
            }
            $element = $arguments;
            $element['arguments'] = $arguments;
            $element['type'] = self::TYPE;

            if (isset($element['handle'])) {
                $personalLayout = $this->layoutFactory->create();
                $element['layout'] = $personalLayout;

                $layout->addElement($elementName, $element);

                if (isset($parentName)) {
                    $alias = isset($element['as']) ? $element['as'] : $elementName;
                    $layout->setChild($parentName, $elementName, $alias);
                }

                /** @var $layoutProcessor ProcessorInterface */
                $layoutProcessor = $this->processorFactory->create();
                $layoutProcessor->load($element['handle']);
                $xml = $layoutProcessor->asSimplexml();

                foreach ($xml as $childElement) {
                    /** @var $childElement Element  */
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
            }
        }

        return $this;
    }

    /**
     * @param array $element
     * @param LayoutInterface $layout
     * @param string $parentName
     * @return Preset
     */
    public function register(array $element, LayoutInterface $layout, $parentName)
    {
        if (isset($element['name']) && !isset($element['is_registered'])) {
            $elementName = $element['name'];

            $layout->updateElement($elementName, array('is_registered' => true));

            $personalLayout = isset($element['layout']) ? $element['layout'] : $layout;

            foreach ($personalLayout->getChildNames($elementName) as $childName) {
                $child = $personalLayout->getElement($childName);
                /** @var $handle Render */
                $handle = $this->handleFactory->get($child['type']);
                $handle->register($child, $personalLayout, $elementName);
            }

        }

        return $this;
    }

    /**
     * @param array $element
     * @param LayoutInterface $layout
     * @param string $parentName
     * @param string $type [optional]
     * @return mixed
     */
    public function render(array $element, LayoutInterface $layout, $parentName, $type = Html::TYPE_HTML)
    {
        $result = '';

        if (isset($element['name'])) {
            $elementName = $element['name'];

            $personalLayout = isset($element['layout']) ? $element['layout'] : $layout;

            foreach ($personalLayout->getChildNames($elementName) as $childName) {
                $child = $personalLayout->getElement($childName);
                /** @var $handle Render */
                $handle = $this->handleFactory->get($child['type']);
                if ($handle instanceof Render) {
                    $result .= $handle->render($child, $personalLayout, $elementName, $type);
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
