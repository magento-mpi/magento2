<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Handle\Data;

use Magento\View\Context;
use Magento\View\Layout;
use Magento\View\Layout\Element;
use Magento\View\Layout\Handle;
use Magento\View\Layout\Handle\Data;
use Magento\View\Layout\Handle\Render;
use Magento\View\Layout\HandleFactory;
use Magento\View\Layout\Handle\Data\Registry;

class Source implements Data
{
    /**
     * Container type
     */
    const TYPE = 'data';

    /**
     * @var \Magento\View\Layout\HandleFactory
     */
    protected $handleFactory;

    /**
     * @var \Magento\View\Layout\Handle\Data\Registry
     */
    protected $dataRegistry;

    /**
     * @param HandleFactory $handleFactory
     * @param Registry $dataRegistry
     */
    public function __construct(
        HandleFactory $handleFactory,
        Registry $dataRegistry
    ) {
        $this->handleFactory = $handleFactory;
        $this->dataRegistry = $dataRegistry;
    }

    /**
     * @param Element $layoutElement
     * @param Layout $layout
     * @param array $parentNode
     */
    public function parse(Element $layoutElement, Layout $layout, array & $parentNode = array())
    {
        $node = array();
        foreach ($layoutElement->attributes() as $attributeName => $attribute) {
            if ($attribute) {
                $node[$attributeName] = (string)$attribute;
            }
        }

        $node['type'] = self::TYPE;

        $name = isset($node['name']) ? $node['name'] : null;
        if (isset($name)) {
            // if ($name == 'product.tooltip')var_dump($parentNode);
            // add element to the layout registry (for quick reference)
            $layout->addElement($name, $node);
        }

        $alias = isset($node['as']) ? $node['as'] : $name;
        if (isset($alias) && $parentNode) {
            $parentNode['children'][$alias] = & $node;
        }

        // parse children
        if ($layoutElement->hasChildren()) {
            foreach ($layoutElement as $childXml) {
                /** @var $childXml Element */
                $type = $childXml->getName();
                /** @var $handle Handle */
                $handle = $this->handleFactory->get($type);
                $handle->parse($childXml, $layout, $node);
            }
        }
    }

    /**
     * @param array $meta
     * @param Layout $layout
     * @param array $parentNode
     * @throws \Exception
     */
    public function register(array & $meta, Layout $layout, array & $parentNode = array())
    {
        if (isset($meta['class'])) {
            if (!class_exists($meta['class'])) {
                throw new \Exception(__('Invalid Data Provider class name: ' . $meta['class']));
            }

            $name = isset($meta['name']) ? $meta['name'] : null;

            $data = $this->dataRegistry->get($name, $meta['class']);

            $alias = isset($meta['as']) ? $meta['as'] : $name;

            $parentNode['_data_'][$alias] = $data;
        }
    }
}
