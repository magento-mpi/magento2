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
use Magento\View\Render\RenderFactory;
use Magento\View\Render\Html;

class Container implements RenderInterface
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
     * @param LayoutInterface $layout
     * @param string $parentName
     * @return Container
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

            $layout->addElement($elementName, $element);

            if (isset($parentName)) {
                $alias = !empty($element['as']) ? $element['as'] : $elementName;
                $layout->setChild($parentName, $elementName, $alias);
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
     * @return Container
     */
    public function register(array $element, LayoutInterface $layout, $parentName)
    {
        if (isset($element['name']) && !isset($element['is_registered'])) {
            $elementName = $element['name'];

            $layout->updateElement($elementName, array('is_registered' => true));

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
     * @return string
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function render(array $element, LayoutInterface $layout, $parentName, $type = Html::TYPE_HTML)
    {
        $result = '';
        if (isset($element['name'])) {
            $elementName = $element['name'];

            foreach ($layout->getChildNames($elementName) as $childName) {
                $child = $layout->getElement($childName);
                /** @var $handle RenderInterface */
                $handle = $this->handleFactory->get($child['type']);
                if ($handle instanceof RenderInterface) {
                    $result .= $handle->render($child, $layout, $elementName, $type);
                }
            }
        }

        $render = $this->renderFactory->get($type);

        $containerInfo['label'] = !empty($element['label']) ? $element['label'] : null;
        $containerInfo['tag'] = !empty($element['htmlTag']) ? $element['htmlTag'] : null;
        $containerInfo['class'] = !empty($element['htmlClass']) ? $element['htmlClass'] : null;
        $containerInfo['id'] = !empty($element['htmlId']) ? $element['htmlId'] : null;

        $result = $render->renderContainer($result, $containerInfo);

        return $result;
    }
}
