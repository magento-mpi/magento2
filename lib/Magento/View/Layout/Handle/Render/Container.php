<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Handle\Render;

use Magento\View\Layout\Handle\Render;
use Magento\View\Layout\Handle;

use Magento\View\Context;
use Magento\View\Layout;
use Magento\View\Layout\Element;

use Magento\View\Layout\HandleFactory;
use Magento\View\Render\RenderFactory;

use Magento\View\Render\Html;

class Container implements Render
{
    /**
     * Container type
     */
    const TYPE = 'container';

    /**#@+
     * Names of container options in layout
     */
    const CONTAINER_OPT_HTML_TAG = 'htmlTag';
    const CONTAINER_OPT_HTML_CLASS = 'htmlClass';
    const CONTAINER_OPT_HTML_ID = 'htmlId';
    const CONTAINER_OPT_LABEL = 'label';
    /**#@-*/

    /**
     * @var \Magento\View\Layout\HandleFactory
     */
    protected $handleFactory;

    /**
     * @var \Magento\View\Render\RenderFactory
     */
    protected $renderFactory;

    /**
     * @param HandleFactory $handleFactory
     * @param RenderFactory $renderFactory
     */
    public function __construct(
        HandleFactory $handleFactory,
        RenderFactory $renderFactory
    ) {
        $this->handleFactory = $handleFactory;
        $this->renderFactory = $renderFactory;
    }

    /**
     * @param Element $layoutElement
     * @param Layout $layout
     * @param array $parentNode
     */
    public function parse(Element $layoutElement, Layout $layout, array & $parentNode = array())
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

            // parse children
            if ($layoutElement->hasChildren()) {
                foreach ($layoutElement as $childXml) {
                    /** @var $childXml Element */
                    $type = $childXml->getName();
                    /** @var $handle Handle */
                    $handle = $this->handleFactory->get($type);
                    $handle->parse($childXml, $layout, $element);
                }
            }
        }
    }

    /**
     * @param array $meta
     * @param Layout $layout
     * @param array $parentNode
     */
    public function register(array & $meta, Layout $layout, array & $parentNode = array())
    {
        if (isset($meta['children'])) {
            foreach ($meta['children'] as & $child) {
                $child['parent'] = & $meta;
                /** @var $handle Handle */
                $handle = $this->handleFactory->get($child['type']);
                $handle->register($child, $layout, $meta);
            }
        }
    }

    /**
     * @param array $meta
     * @param Layout $layout
     * @param array $parentNode
     * @param $type
     * @return string
     */
    public function render(array & $meta, Layout $layout, array & $parentNode = array(), $type = Html::TYPE_HTML)
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
