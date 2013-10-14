<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_View
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Abstract View Container.
 *
 * @category    Magento
 * @package     Magento_View
 */

namespace Magento\View\Element;

use Magento\View\Element;
use Magento\App\Context;
use Magento\View\Render\Html;
use Magento\View\Render\RenderFactory;
use Magento\View\ViewFactory;
use Magento\View\Layout\Argument;
use Magento\ObjectManager;

abstract class Base implements Element
{
    const TYPE = '';

    /**
     * @var array
     */
    protected $meta = array();

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $alias;

    protected $before;

    protected $after;

    /**
     * @var Element
     */
    protected $parent;

    /**
     * Context.
     *
     * @var Context
     */
    protected $context;

    /**
     * @var ViewFactory
     */
    protected $viewFactory;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $elements = array();

    protected static $allElements = array();

    /**
     * @var array
     */
    protected $relations = array();

    protected $dataProviders = array();

    /**
     * @param Context $context
     * @param RenderFactory $renderFactory
     * @param ViewFactory $viewFactory
     * @param ObjectManager $objectManager
     * @param Element $parent [optional]
     * @param array $meta [optional]
     * @throws \InvalidArgumentException
     */
    public function __construct(
        Context $context,
        RenderFactory $renderFactory,
        ViewFactory $viewFactory,
        ObjectManager $objectManager,
        Element $parent = null,
        array $meta = array()
    )
    {
        $this->context = $context;
        $this->renderFactory = $renderFactory;
        $this->viewFactory = $viewFactory;

        $this->objectManager = $objectManager;

        $this->parent = $parent;

        $this->meta = $meta;
        $this->name = isset($meta['name']) ? $meta['name'] : null;
        $this->alias = isset($meta['as']) ? $meta['as'] : $this->name;
        $this->before = isset($meta['before']) ? $meta['before'] : null;
        $this->after = isset($meta['after']) ? $meta['after'] : null;

        $this->arguments = isset($meta['arguments']) ? $meta['arguments'] : array();

        self::$allElements[$this->name] = $this;
    }

    public function & getMeta()
    {
        return $this->meta;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return static::TYPE;
    }

    public function addHandle($handleName)
    {
        $handle = $this->viewFactory->createHandle($this->context,
            array('handle' => $handleName));
        $handle->register($this);
    }

    /**
     * @return Element
     */
    public function getParentElement()
    {
        return $this->parent;
    }

    public function & getDataProviders()
    {
        return $this->dataProviders;
    }

    public function addDataProvider($name, $dataProvider)
    {
        $this->dataProviders[$name] = $dataProvider;
    }

    public function getChildren()
    {
        return isset($this->meta['children']) ? $this->meta['children'] : array();
    }

    public function removeElement($name)
    {
        unset($this->elements[$name]);
        unset($this->relations[$name]);
        unset(self::$allElements[$name]);
    }

    public function removeChildrenElements()
    {
        $children = $this->getChildrenElements();
        foreach ($children as $child) {
            $this->removeElement($child->getName());
        }
    }

    /**
     * @return Element[]
     */
    public function getChildrenElements()
    {
        return $this->elements;
    }

    /**
     * @param string $name
     * @return Element
     */
    public function getElement($name)
    {
        return isset(self::$allElements[$name]) ? self::$allElements[$name] : null;
        return isset($this->elements[$name]) ? $this->elements[$name] : null;
    }

    public function renderElement($name, $type = Html::TYPE_HTML)
    {
        $element = $this->getElement($name);
        if ($element) {
            $result = $element->render($type);
        } else {
            $result = '';
        }
        return $result;
    }

    public function getChildBlocks()
    {
        return $this->getChildrenElements();
    }

    public function getChildHtml($name, $type = Html::TYPE_HTML)
    {
        return $this->renderElement($name, $type);
    }

    public function renderChildren($type = Html::TYPE_HTML)
    {
        $out = '';
        foreach ($this->getChildrenElements() as $child) {
            $out .= $child->render($type);
        }
        return $out;
    }

    public function getDesignTheme()
    {
        return $this->context->getDesignTheme();
    }

    public function attach(Element $child, $alias = null, $before = null, $after = null)
    {
        $name = isset($alias) ? $alias : $child->getName();

        if (isset($this->elements[$name])) {
            throw new \LogicException(
                __('Duplicate element of class "' . get_class($child) . '" for name "' . $name . '" has been detected.')
            );
        }

        if (isset($before)) {
            if (isset($this->elements[$before])) {
                $elements = array();
                foreach ($this->elements as $key => $element) {
                    if ($key === $before) {
                        $elements[$name] = $child;
                    }
                    $elements[$key] = $element;
                }
                $this->elements = $elements;
            }
        } elseif (isset($after)) {
            if (isset($this->elements[$after])) {
                $elements = array();
                foreach ($this->elements as $key => $element) {
                    $elements[$key] = $element;
                    if ($key === $after) {
                        $elements[$name] = $child;
                    }
                }
                $this->elements = $elements;
            }
        } else {
            $this->elements[$name] = $child;
        }

        $this->registerRelation($this->getName(), $name);
    }

    public function detach(Element $child, $alias = null)
    {
        $name = isset($alias) ? $alias : $child->getName();

        if (isset($this->elements[$name])) {
            unset($this->elements[$name]);
        }

        if (isset($this->elements[$name])) {
            unset($this->relations[$this->getName()]['children'][$name]);
        }
    }

    public function registerRelation($parentName, $childName)
    {
        $this->relations[$parentName]['children'][$childName] = & $this->elements[$childName];
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function render($type = Html::TYPE_HTML)
    {
        return '';
    }

    public function __toString()
    {
        // TODO there is no chance to enforce other render type this way
        try {
            $this->register();
            $result = $this->render();
        } catch (\Exception $e) {
            $result = "$e";
        }

        return $result;
    }
}
