<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Container;

use Magento\View\Container as ContainerInterface;
use Magento\View\Context;
use Magento\View\Render\Html;
use Magento\View\Render\RenderFactory;
use Magento\View\ViewFactory;
use Magento\ObjectManager;

abstract class Base implements ContainerInterface
{
    /**
     * Container type
     */
    const TYPE = '';

    /**
     * Container configuration data
     *
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

    /**#@+
     * Container sorting directives
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
     * @var ContainerInterface
     */
    protected $parent;

    /**
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
     * List of attached containers
     *
     * @var array
     */
    protected $elements = array();

    /**
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
     * @param ContainerInterface $parent [optional]
     * @param array $meta [optional]
     * @throws \InvalidArgumentException
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function __construct(
        Context $context,
        RenderFactory $renderFactory,
        ViewFactory $viewFactory,
        ObjectManager $objectManager,
        ContainerInterface $parent = null,
        array $meta = array()
    ) {
        $this->context = $context;
        $this->renderFactory = $renderFactory;
        $this->viewFactory = $viewFactory;
        $this->objectManager = $objectManager;

        $this->parent = $parent;
        $this->meta = $meta;

        // TODO: reduce NPathComplexity value

        $this->name = isset($meta['name']) ? $meta['name'] : null;
        $this->alias = isset($meta['as']) ? $meta['as'] : $this->name;

        $this->before = isset($meta['before']) ? $meta['before'] : null;
        $this->after = isset($meta['after']) ? $meta['after'] : null;

        $this->ifConfig = isset($meta['ifconfig']) ? $meta['ifconfig'] : null;
        $this->arguments = isset($meta['arguments']) ? $meta['arguments'] : array();

        self::$allElements[$this->getName()] = $this;
    }

    /**
     * Retrieve container configuration data
     *
     * @return array
     */
    public function &getMeta()
    {
        return $this->meta;
    }

    /**
     * Retrieve container name
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Retrieve container type
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
        $handle = $this->viewFactory->createHandle($this->context, array('handle' => $handleName));
        $handle->register($this);
    }

    /**
     * Return parent container
     *
     * @return ContainerInterface
     */
    public function getParentElement()
    {
        return $this->parent;
    }

    /**
     * Retrieve assigned data providers
     *
     * @return array
     */
    public function &getDataProviders()
    {
        return $this->dataProviders;
    }

    /**
     * Add data provider
     *
     * @param string $name
     * @param \Magento\View\DataProvider $dataProvider
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
     * Remove container by name
     *
     * @param string $name
     */
    public function removeElement($name)
    {
        unset($this->elements[$name]);
        unset(self::$allElements[$name]);
    }

    /**
     * Remove children
     */
    public function removeChildrenElements()
    {
        $children = $this->getChildrenElements();
        foreach ($children as $child) {
            $this->removeElement($child->getName());
        }
    }

    /**
     * Retrieve children
     *
     * @return ContainerInterface[]
     */
    public function getChildrenElements()
    {
        return $this->elements;
    }

    /**
     * Alias for getChildrenElements
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
     * @param ContainerInterface $container
     * @return bool
     */
    public function isBlock(ContainerInterface $container)
    {
        return $container->getType() === Block::TYPE;
    }

    /**
     * Whether element is a container
     *
     * @param ContainerInterface $container
     * @return bool
     */
    public function isContainer(ContainerInterface $container)
    {
        return $container->getType() === Container::TYPE;
    }

    /**
     * Retrieve container by name
     *
     * @param string $name
     * @return ContainerInterface
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
            return $element->render($type);
        }
        return '';
    }

    /**
     * Alias for renderElement
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
     * Attach container
     *
     * @param ContainerInterface $child
     * @param string $alias
     * @param string $before
     * @param string $after
     * @throws \LogicException
     * @todo Reduce CyclomaticComplexity
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function attach(ContainerInterface $child, $alias = null, $before = null, $after = null)
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
    }

    /**
     * @param string $alias
     * @return ContainerInterface|null
     */
    public function getChild($alias)
    {
        return isset($this->elements[$alias]) ? $this->elements[$alias] : null;
    }

    /**
     * Detach container
     *
     * @param ContainerInterface $child
     * @param null $alias
     */
    public function detach(ContainerInterface $child, $alias = null)
    {
        $name = isset($alias) ? $alias : $child->getName();
        if (isset($this->elements[$name])) {
            unset($this->elements[$name]);
        }
    }

    /**
     * @param string $type
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
        // TODO: There is no chance to enforce other render type this way
        try {
            $this->register();
            return $this->render();
        } catch (\Exception $e) {
            return (string)$e;
        }
    }
}
