<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Handle\Render;

use Magento\View\Context;
use Magento\View\Layout;
use Magento\View\Layout\Element;
use Magento\View\Layout\Handle;
use Magento\View\Layout\Handle\Render;
use Magento\View\Layout\HandleFactory;
use Magento\View\Render\RenderFactory;
use Magento\Core\Model\BlockFactory;

use Magento\View\Render\Html;

class Block implements Render
{
    /**
     * Container type
     */
    const TYPE = 'block';

    /**
     * @var \Magento\View\Layout\HandleFactory
     */
    protected $handleFactory;

    /**
     * @var \Magento\View\Render\RenderFactory
     */
    protected $renderFactory;

    /**
     * @var \Magento\Core\Model\BlockFactory
     */
    protected $blockFactory;

    /**
     * @param HandleFactory $handleFactory
     * @param RenderFactory $renderFactory
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        HandleFactory $handleFactory,
        RenderFactory $renderFactory,
        BlockFactory $blockFactory
    ) {
        $this->handleFactory = $handleFactory;
        $this->renderFactory = $renderFactory;
        $this->blockFactory = $blockFactory;
    }

    /**
     * @param Element $layoutElement
     * @param Layout $layout
     * @param array $parentNode
     * @return Render
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

        return $this;
    }

    /**
     * @param array $meta
     * @param Layout $layout
     * @param array $parentNode
     * @throws \Exception
     */
    public function register(array & $meta, Layout $layout, array & $parentNode = array())
    {
        if (!class_exists($meta['class'])) {
            throw new \Exception(__('Invalid block class name: ' . $meta['class']));
        }

        $arguments = isset($meta['arguments']) ? $meta['arguments'] : array();

        /** @var $block \Magento\Core\Block\Template */
        $block = $this->blockFactory->createBlock($meta['class'], array('data' => $arguments));

        $name = isset($meta['name']) ? $meta['name'] : null;
        $block->setNameInLayout($name);
        $block->setLayout($layout);

        if (isset($meta['template'])) {
            $block->setTemplate($meta['template']);
        }

        $meta['_wrapped_'] = $block;

        if (isset($meta['children'])) {
            foreach ($meta['children'] as & $child) {
                $child['parent'] = & $meta;
                /** @var $handle Render */
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
     * @return mixed
     */
    public function render(array & $meta, Layout $layout, array & $parentNode = array(), $type = Html::TYPE_HTML)
    {
        return $meta['_wrapped_']->toHtml();
    }
}
