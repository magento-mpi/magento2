<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Handle\Command;

use Magento\View\Layout\Handle\AbstractHandle;
use Magento\View\Layout\HandleFactory;
use Magento\View\LayoutInterface;
use Magento\View\Layout\Element;
use Magento\View\Layout\Handle\CommandInterface;
use Magento\Core\Model\Store\Config;
use Magento\View\Render\RenderFactory;
use Magento\Core\Model\Layout\Argument\Processor;

class Action extends AbstractHandle implements CommandInterface
{
    /**
     * Container type
     */
    const TYPE = 'action';

    /**
     * @var int
     */
    private $inc = 0;

    /**
     * Core store config
     *
     * @deprecated
     * @var Config
     */
    protected $coreStoreConfig;

    /**
     * @param HandleFactory $handleFactory
     * @param RenderFactory $renderFactory
     * @param Config $coreStoreConfig
     */
    public function __construct(
        HandleFactory $handleFactory,
        RenderFactory $renderFactory,
        Processor $argumentProcessor,
        Config $coreStoreConfig)
    {
        parent::__construct($handleFactory, $renderFactory, $argumentProcessor);

        $this->coreStoreConfig = $coreStoreConfig;
    }
    /**
     * @inheritdoc
     */
    public function parse(Element $layoutElement, LayoutInterface $layout, $parentName)
    {
        $element = $this->parseAttributes($layoutElement);

        $ifConfig = isset($element['ifconfig']) ? $element['ifconfig'] : null;
        if (!empty($ifConfig) && !$this->coreStoreConfig->getConfigFlag($ifConfig)) {
            return $this;
        }

        $element['type'] = self::TYPE;
        $element['arguments'] = $this->parseArguments($layoutElement);

        $elementName = isset($element['name']) ? $element['name'] : ('Command-Action-' . $this->inc++);
        $layout->addElement($elementName, $element);

        if (isset($parentName)) {
            $layout->setChild($parentName, $elementName, $elementName);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function register(array $element, LayoutInterface $layout, $parentName)
    {
        $method = isset($element['method']) ? $element['method'] : null;

        if (isset($method) && isset($parentName)) {
            $arguments = isset($element['arguments']) ? $element['arguments'] : array();
            $block = $layout->getBlock($parentName);
            if (isset($block)) {
                call_user_func_array(array($block, $method), $arguments);
            }
        }

        $alias = $layout->getChildAlias($parentName, $element['name']);
        $layout->unsetChild($parentName, $alias);

        return $this;
    }
}
