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
                $parentNode['children'][$alias] = & $node;
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
     * @return string
     */
    public function render(array & $meta, Layout $layout, array & $parentNode = array(), $type = Html::TYPE_HTML)
    {
        $render = $this->renderFactory->get($type);

        $data = isset($parentNode['_data_']) ? $parentNode['_data_'] : array();

        if (isset($meta['_data_']) && is_array($meta['_data_'])) {
            $data = array_merge($data, $meta['_data_']);
        }

        // TODO probably prepare limited proxy to avoid violations
        $data['layout'] = $layout;

        $result = $render->renderTemplate($this->getTemplateFile($meta['path'], $layout), $data);

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
