<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Handle\Render;

use Magento\View\LayoutInterface;
use Magento\View\Layout\Element;
use Magento\View\Layout\HandleInterface;
use Magento\View\Layout\Handle\RenderInterface;
use Magento\View\Layout\HandleFactory;
use Magento\View\BlockPool;

use Magento\View\Render\Html;

class Block implements RenderInterface
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
     * @var int
     */
    protected $inc = 0;

    /**
     * @param HandleFactory $handleFactory
     */
    public function __construct(
        HandleFactory $handleFactory
    ) {
        $this->handleFactory = $handleFactory;
    }

    /**
     * @param Element $layoutElement
     * @param LayoutInterface $layout
     * @param string $parentName
     * @return Block
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function parse(Element $layoutElement, LayoutInterface $layout, $parentName)
    {
        $elementName = $layoutElement->getAttribute('name');
        $elementName = $elementName ?: ('Block-' . $this->inc++);
        if (!empty($elementName)) {
            $arguments = $element = array();
            foreach ($layoutElement->attributes() as $attributeName => $attribute) {
                if ($attribute) {
                    $arguments[$attributeName] = (string)$attribute;
                }
            }
            $element = $arguments;
            $element['arguments'] = $arguments;
            $element['type'] = self::TYPE;

            $layout->addElement($elementName, $element);

            if (isset($parentName)) {
                $alias = !empty($element['as']) ? $element['as'] : $elementName;
                $layout->setChild($parentName, $elementName, $alias);
                list($siblingName, $isAfter) = $this->beforeAfterToSibling($element);
                $layout->reorderChild($parentName, $elementName, $siblingName, $isAfter);
            }

            // parse children
            if ($layoutElement->hasChildren()) {
                foreach ($layoutElement as $childXml) {
                    /** @var $childXml Element */
                    $type = $childXml->getName();
                    /** @var $handle HandleInterface */
                    $handle = $this->handleFactory->get($type);
                    $handle->parse($childXml, $layout, $elementName);
                }
            }
        }

        return $this;
    }

    /**
     * @param array $element
     * @param LayoutInterface $layout
     * @param string $parentName
     * @return Block
     * @throws \Exception
     */
    public function register(array $element, LayoutInterface $layout, $parentName)
    {
        if (!empty($element['name']) && !isset($element['is_registered'])) {
            if (!class_exists($element['class'])) {
                throw new \Exception(__('Invalid block class name: ' . $element['class']));
            }

            $elementName = $element['name'];
            $arguments = isset($element['arguments']) ? $element['arguments'] : array();

            $layout->updateElement($elementName, array('is_registered' => true));

            /** @var $block \Magento\Core\Block\Template */
            $block = $layout->createBlock($element['class'], $elementName,
                array(
                    'data' => $arguments
                ));

            $block->setNameInLayout($elementName);
            $block->setLayout($layout);

            if (isset($element['template'])) {
                $block->setTemplate($element['template']);
            }

            foreach ($layout->getChildNames($elementName) as $childName) {
                $child = $layout->getElement($childName);
                /** @var $handle RenderInterface */
                $handle = $this->handleFactory->get($child['type']);
                $handle->register($child, $layout, $elementName);
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
        if ($block = $layout->getBlock($element['name'])) {
            $result = $block->toHtml();
        }

        return $result;
    }

    /**
     * Analyze "before" and "after" information in the node and return sibling name and whether "after" or "before"
     *
     * @param array $element
     * @return array
     */
    protected function beforeAfterToSibling($element)
    {
        $result = array(null, true);
        if (isset($element['after'])) {
            $result[0] = (string)$element['after'];
        } elseif (isset($element['before'])) {
            $result[0] = (string)$element['before'];
            $result[1] = false;
        }
        return $result;
    }
}
