<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Handle\Render;

use Magento\View\Layout\Handle;
use Magento\View\Layout\Handle\Render;

use Magento\View\Context;
use Magento\View\Layout;
use Magento\View\Layout\Element;

use Magento\View\Layout\HandleFactory;
use Magento\View\Render\RenderFactory;
use Magento\Core\Model\View\FileSystem;

use Magento\View\Render\Html;

class Template implements Render
{
    /**
     * Container type
     */
    const TYPE = 'template';

    /**
     * @var HandleFactory
     */
    protected $handleFactory;

    /**
     * @var RenderFactory
     */
    protected $renderFactory;

    /**
     * @var FileSystem
     */
    protected $filesystem;

    /**
     * @param HandleFactory $handleFactory
     * @param RenderFactory $renderFactory
     * @param FileSystem $filesystem
     */
    public function __construct(
        HandleFactory $handleFactory,
        RenderFactory $renderFactory,
        FileSystem $filesystem
    ) {
        $this->handleFactory = $handleFactory;
        $this->renderFactory = $renderFactory;
        $this->filesystem = $filesystem;
    }

    /**
     * @param Element $layoutElement
     * @param Layout $layout
     * @param string $parentName
     * @return Template
     */
    public function parse(Element $layoutElement, Layout $layout, $parentName)
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
                $alias = isset($element['as']) ? $element['as'] : $elementName;
                $layout->setChild($parentName, $elementName, $alias);
            }

            // parse children
            if ($layoutElement->hasChildren()) {
                foreach ($layoutElement as $childXml) {
                    /** @var $childXml Element */
                    $type = $childXml->getName();
                    /** @var $handle Handle */
                    $handle = $this->handleFactory->get($type);
                    $handle->parse($childXml, $layout, $elementName);
                }
            }
        }

        return $this;
    }

    /**
     * @param array $element
     * @param Layout $layout
     * @param string $parentName
     * @return Template
     */
    public function register(array $element, Layout $layout, $parentName)
    {
        if (isset($element['name']) && !isset($element['is_registered'])) {
            $elementName = $element['name'];

            $layout->updateElement($elementName, array('is_registered' => true));

            foreach ($layout->getChildNames($elementName) as $childName => $alias) {
                $child = $layout->getElement($childName);
                /** @var $handle Render */
                $handle = $this->handleFactory->get($child['type']);
                $handle->register($child, $layout, $elementName);
            }
        }

        return $this;
    }

    /**
     * @param array $element
     * @param Layout $layout
     * @param string $parentName
     * @param string $type [optional]
     * @return string
     */
    public function render(array $element, Layout $layout, $parentName, $type = Html::TYPE_HTML)
    {
        $render = $this->renderFactory->get($type);
        $elementName = $element['name'];

        $inheritedData = $layout->getElementDataSources($parentName);
        $ownData = $layout->getElementDataSources($elementName);
        $data = array_merge($inheritedData, $ownData);

        // TODO probably prepare limited proxy to avoid violations
        $data['layout'] = $layout;

        $result = $render->renderTemplate($this->getTemplateFile($element['path'], $layout), $data);

        return $result;
    }

    /**
     * Get absolute path to template
     *
     * @param string $path
     * @param Layout $layout
     * @return string
     */
    protected function getTemplateFile($path, Layout $layout)
    {
        // TODO: Rid of using area
        $params = array(
            'area' => $layout->getArea()
        );
        $templateName = $this->filesystem->getFilename($path, $params);

        return $templateName;
    }
}
