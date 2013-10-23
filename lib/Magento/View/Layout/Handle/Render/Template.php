<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Handle\Render;

use Magento\View\Layout\Handle\AbstractHandle;
use Magento\View\Layout\HandleInterface;
use Magento\View\Layout\Handle\RenderInterface;

use Magento\View\LayoutInterface;
use Magento\View\Layout\Element;

use Magento\View\Layout\HandleFactory;
use Magento\View\Render\RenderFactory;
use Magento\Core\Model\View\FileSystem;

use Magento\View\Render\Html;

class Template extends AbstractHandle implements RenderInterface
{
    /**
     * Container type
     */
    const TYPE = 'template';

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
    )
    {
        parent::__construct($handleFactory, $renderFactory);

        $this->filesystem = $filesystem;
    }

    /**
     * @param Element $layoutElement
     * @param LayoutInterface $layout
     * @param string $parentName
     * @return Template
     */
    public function parse(Element $layoutElement, LayoutInterface $layout, $parentName)
    {
        $elementName = $layoutElement->getAttribute('name');
        $elementName = $elementName ?: ('Template-' . $this->nameIncrement++);

        if (isset($elementName)) {
            $element = $this->parseAttributes($layoutElement);

            $element['type'] = self::TYPE;
            $element['name'] = $elementName;

            $layout->addElement($elementName, $element);

            // assign to parent element
            $this->assignToParentElement($element, $layout, $parentName);

            // parse children
            $this->parseChildren($layoutElement, $layout, $elementName);
        }

        return $this;
    }

    /**
     * @param array $element
     * @param LayoutInterface $layout
     * @param string $parentName
     * @return Template
     */
    public function register(array $element, LayoutInterface $layout, $parentName)
    {
        if (isset($element['name']) && !isset($element['is_registered'])) {
            $elementName = $element['name'];

            $layout->updateElement($elementName, array('is_registered' => true));

            // register children
            $this->registerChildren($elementName, $layout);
        }

        return $this;
    }

    /**
     * @param array $element
     * @param LayoutInterface $layout
     * @param string $parentName
     * @param string $type [optional]
     * @return string
     */
    public function render(array $element, LayoutInterface $layout, $parentName, $type = Html::TYPE_HTML)
    {
        $render = $this->renderFactory->get($type);
        $elementName = $element['name'];

        $data = array();
        if (isset($parentName)) {
            $data = $layout->getElementDataSources($parentName);
        }
        $ownData = $layout->getElementDataSources($elementName);
        $data = array_merge($data, $ownData);

        // TODO probably prepare limited proxy to avoid violations
        $data['layout'] = $layout;

        $result = $render->renderTemplate($this->getTemplateFile($element['path'], $layout), $data);

        return $result;
    }

    /**
     * Get absolute path to template
     *
     * @param string $path
     * @param LayoutInterface $layout
     * @return string
     */
    protected function getTemplateFile($path, LayoutInterface $layout)
    {
        // TODO: Rid of using area
        $params = array(
            'area' => $layout->getArea()
        );
        $templateName = $this->filesystem->getFilename($path, $params);

        return $templateName;
    }
}
