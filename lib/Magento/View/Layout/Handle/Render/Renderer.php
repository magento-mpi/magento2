<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Handle\Render;

use Magento\View\Layout\Handle\AbstractHandle;
use Magento\View\Layout\Handle\RenderInterface;
use Magento\View\LayoutInterface;
use Magento\View\Layout\Element;
use Magento\View\Layout\HandleFactory;
use Magento\View\Render\RenderFactory;
use Magento\Core\Model\Layout\Argument\Processor;
use Magento\Core\Model\View\FileSystem;

use Magento\View\Render\Html;

/**
 * @package Magento\View
 */
class Renderer extends Block implements RenderInterface
{
    /**
     * Container type
     */
    const TYPE = 'renderer';

    /**
     * @inheritdoc
     */
    public function register(array $element, LayoutInterface $layout)
    {
        if (!empty($element['name']) && !isset($element['is_registered'])) {
            if (!class_exists($element['class'])) {
                throw new \InvalidArgumentException(__('Invalid renderer class name: ' . $element['class']));
            }

            $elementName = $element['name'];
            $arguments = isset($element['arguments']) ? $element['arguments'] : array();

            $layout->updateElement($elementName, array('is_registered' => true));

            /** @var $block \Magento\View\Element\RendererInterface */
            $block = $layout->createBlock(
                $element['class'],
                $elementName,
                array(
                    'data' => $arguments
                )
            );

            $block->setNameInLayout($elementName);
            $block->setLayout($layout);

            if (isset($element['template'])) {
                $block->setTemplate($element['template']);
            }

            // register children
            $this->registerChildren($elementName, $layout);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function render($elementName, LayoutInterface $layout)
    {
        $result = '';
        $renderer = $layout->getRenderer($elementName);
        if ($renderer) {
            $result = $renderer->render();
        }

        return $result;
    }
}
