<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View\Element;

use Magento\View\Element;
use Magento\View\Context;
use Magento\View\Render\Html;
use Magento\View\Render\RenderFactory;
use Magento\View\ViewFactory;
use Magento\ObjectManager;

abstract class Base implements Element
{
    /**
     * Element type
     */
    const TYPE = '';

    /**
     * Element configuration data
     *
     * @var array
     */
    protected $meta = array();

    /**
     * Element name
     *
     * @var string
     */
    protected $name;

    /**
     * Element alias
     *
     * @var string
     */
    protected $alias;

    /**#@+
     * Element order directives
     *
     * @var string
     */
    protected $before;
    protected $after;
    /**#@-*/

    /**
     * Path to configuration flag
     *
     * @var string
     */
    protected $ifConfig;

    /**
     * Parent of element
     *
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
     * List of attached elements
     *
     * @var array
     */
    protected $elements = array();

    /**
     * Common list of elements
     *
     * @var array
     */
    public static $allElements = array();

    /**
     * List of data providers
     *
     * @var array
     */
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
    ) {
        $this->context = $context;
        $this->renderFactory = $renderFactory;
        $this->viewFactory = $viewFactory;

        $this->objectManager = $objectManager;

        $this->parent = $parent;
        $this->meta = $meta;


        $this->before = isset($meta['before']) ? $meta['before'] : null;
        $this->after = isset($meta['after']) ? $meta['after'] : null;

        $this->ifConfig = isset($meta['ifconfig']) ? $meta['ifconfig'] : null;
        $this->arguments = isset($meta['arguments']) ? $meta['arguments'] : array();

        self::$allElements[$this->getName()] = $this;
    }

    /**
     * Retrieve element configuration data
     *
     * @return array
     */
    public function & getMeta()
    {
        return $this->meta;
    }

    /**
     * Retrieve element name
     *
     * @return string
     */
    public function getName()
    {
        if (!$this->name) {
            $this->name = isset($this->meta['name']) ? $this->meta['name'] : null;
        }
        return $this->name;
    }

    /**
     * Retrieve element alias
     *
     * @return string
     */
    public function getAlias()
    {
        if (!$this->alias) {
            $this->alias = isset($this->meta['as']) ? $this->meta['as'] : $this->getName();
        }
        return $this->alias;
    }

    /**
     * Retrieve element type
     *
     * @return string
     */
    public function getType()
    {
        return static::TYPE;
    }

    /**
     * @param string $handleName
     */
    public function addHandle($handleName)
    {
        $handle = $this->viewFactory->createHandle($this->context,
            array('handle' => $handleName));
        $handle->register($this);
    }

    /**
     * Retrieve parent element
     *
     * @return \Magento\View\Element
     */
    public function getParentElement()
    {
        return $this->parent;
    }

    /**
     * Retrieve all data providers
     *
     * @return array
     */
    public function & getDataProviders()
    {
        return $this->dataProviders;
    }

    /**
     * Add data provider
     *
     * @param string $name
     * @param \Magento\Core\Block\AbstractBlock $dataProvider
     * @return void
     */
    public function addDataProvider($name, $dataProvider)
    {
        $this->dataProviders[$name] = $dataProvider;
    }

    /**
     * @return array
     */
    public function getChildren()
    {
        return isset($this->meta['children']) ? $this->meta['children'] : array();
    }

    /**
     * Remove child element
     *
     * @param string $name
     */
    public function removeElement($name)
    {
        unset($this->elements[$name]);
        unset(self::$allElements[$name]);
    }

    /**
     * Remove all children elements
     */
    public function removeChildrenElements()
    {
        $children = $this->getChildrenElements();
        foreach ($children as $child) {
            $this->removeElement($child->getName());
        }
    }

    /**
     * Return array of children
     *
     * @return array
     */
    public function getChildrenElements()
    {
        return $this->elements;
    }

    /**
     * Retrieve child blocks (alias for getChildrenElements)
     *
     * @return array
     */
    public function getChildBlocks()
    {
        $blocks = array();
        foreach ($this->getChildrenElements() as $element) {
            if ($this->isBlock($element)) {
                $blocks[] = $element->getWrappedElement();
            }
        }
        return $blocks;
    }

    /**
     * Whether element is a block
     *
     * @param Element $element
     * @return bool
     */
    public function isBlock(Element $element)
    {
        return ($element->getType() === Block::TYPE);
    }

    /**
     * Whether element is a container
     *
     * @param Element $element
     * @return bool
     */
    public function isContainer(Element $element)
    {
        return ($element->getType() === Container::TYPE);
    }

    /**
     * Retrieve element by name
     *
     * @param string $name
     * @return Element|null
     */
    public function getElement($name)
    {
        return isset(Base::$allElements[$name]) ? Base::$allElements[$name] : null;
    }

    /**
     * @param string $name
     * @param string $type
     * @return string
     */
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

    /**
     * @param string $alias
     * @return Element|null
     */
    public function getChild($alias)
    {
        return isset($this->elements[$alias]) ? $this->elements[$alias] : null;
    }

    /**
     * Retrieve child element output (alias for renderElement)
     *
     * @param string $name
     * @param string $type
     * @return string
     */
    public function getChildHtml($name, $type = Html::TYPE_HTML)
    {
        return $this->renderElement($name, $type);
    }

    /**
     * @param string $type
     * @return string
     */
    public function renderChildren($type = Html::TYPE_HTML)
    {
        $out = '';
        foreach ($this->getChildrenElements() as $child) {
            $out .= $child->render($type);
        }
        return $out;
    }

    /**
     * Retrieve design theme
     *
     * @return \Magento\Core\Model\Theme
     */
    public function getDesignTheme()
    {
        return $this->context->getDesignTheme();
    }

    /**
     * Attach element
     *
     * @param Element $child
     * @param string $alias
     * @param string $before
     * @param string $after
     * @throws \LogicException
     */
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

    /**
     * Detach element
     *
     * @param Element $child
     * @param string $alias
     * @return void
     */
    public function detach(Element $child, $alias = null)
    {
        $name = isset($alias) ? $alias : $child->getName();

        if (isset($this->elements[$name])) {
            unset($this->elements[$name]);
        }
    }

    /**
     * @param string $type
     * @return string
     */
    public function render($type = Html::TYPE_HTML)
    {
        return '';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        // TODO: there is no chance to enforce other render type this way
        try {
            $this->register();
            $result = $this->render();
        } catch (\Exception $e) {
            $result = "$e";
        }
        return $result;
    }
}
