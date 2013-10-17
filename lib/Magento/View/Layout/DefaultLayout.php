<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout;

use Magento\View\Layout;
use Magento\View\Context;
use Magento\View\Container as ContainerInterface;
use Magento\View\Container\Container;
use Magento\View\Container\Base;
use Magento\View\Container\Block;
use Magento\View\ViewFactory;
use Magento\ObjectManager;
use Magento\Core\Model\View\DesignInterface;
use Magento\View\Design\ThemeFactory;
use Magento\Core\Exception;
use Magento\Core\Block\AbstractBlock;

class DefaultLayout implements Layout
{
    /**
     * @var /SimpleXMLElement
     */
    protected $xml;

    /**
     * @var array
     */
    protected $meta = array();

    /**
     * @var array
     */
    protected $elements = array();

    /**
     * @var Element
     */
    protected $root;

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
     * @var Reader
     */
    protected $layoutReader;

    /**
     * @var DesignInterface
     */
    protected $design;

    /**
     * @var ThemeFactory
     */
    protected $themeFactory;

    /**
     * @var array
     */
    protected $helpers = array();

    /**
     * @var string
     */
    protected $area;

    /**
     * @var bool
     */
    protected $directOutput = false;

    /**
     * @var \Magento\Core\Block\Messages
     */
    protected $messages;

    public function __construct(
        DesignInterface $design,
        ThemeFactory $themeFactory,
        Context $context,
        ViewFactory $viewFactory,
        ObjectManager $objectManager,
        Reader $layoutReader
    ) {
        $this->design = $design;
        $this->themeFactory = $themeFactory;
        $this->context = $context;
        $this->viewFactory = $viewFactory;
        $this->objectManager = $objectManager;
        $this->layoutReader = $layoutReader;
    }

    /**
     * Retrieve the layout update instance
     *
     * @return \Magento\Core\Model\Layout\Merge
     */
    public function getUpdate()
    {
        $theme = $this->getThemeInstance($this->getArea());
        return $this->objectManager->get(
            'Magento\\View\\Layout\\Processor',
            array(
                'layout' => $this,
                'theme' => $theme,
            )
        );
    }

    /**
     * Retrieve instance of a theme currently used in an area
     *
     * @param string $area
     * @return \Magento\View\Design\Theme
     */
    protected function getThemeInstance($area)
    {
        if ($this->design->getDesignTheme()->getArea() == $area || $this->design->getArea() == $area) {
            return $this->design->getDesignTheme();
        }

        $themeIdentifier = $this->design->getConfigurationDesignTheme($area);
        // TODO: ?
        return $this->themeFactory->getTheme($themeIdentifier);
    }

    /**
     * Layout xml generation
     *
     * @return Layout
     * @todo MERGE with generateElements()
     */
    public function generateXml()
    {
        $this->xml = $this->getUpdate()->asSimplexml();
        return $this;
    }

    /**
     *
     * Create structure of elements from the loaded XML configuration
     *
     * @todo MERGE with generateXml()
     */
    public function generateElements()
    {
        $this->layoutReader->generateFromXml($this->xml, $this->meta);
        $this->root = $this->viewFactory->create(
            $this->meta['type'],
            array(
                'context' => $this->context,
                'meta' => $this->meta,
            )
        );

        Base::$allElements['root'] = $this->root;

        $this->root->register();

        return $this;
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
     * Find an element in layout, render it and return string with its output
     *
     * @param string $name
     * @param bool $useCache
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function renderElement($name, $useCache = true)
    {
        $element = $this->getElement($name);
        if ($element) {
            return $element->render();
        }
        return '';
    }

    /**
     * Add an element to output
     *
     * @param string $name
     * @return DefaultLayout
     */
    public function addOutputElement($name)
    {
        $this->root = $this->getElement($name);

        return $this;
    }

    /**
     * Get Root View Element output
     *
     * @return string
     */
    public function getOutput()
    {
        if (isset($this->root)) {
            return $this->root->render();
        }
        return '';
    }

    /**
     * Check if element exists in layout structure
     *
     * @param string $name
     * @return bool
     * @todo DELETE (only two-three calls from outside)
     */
    public function hasElement($name)
    {
        return isset($this->elements[$name]);
    }

    /**
     * Remove block from registry
     *
     * @param string $name
     * @return DefaultLayout
     * @todo DELETE (only two-three calls from outside)
     */
    public function unsetElement($name)
    {
        unset($this->elements[$name]);

        return $this;
    }

    /**
     * Retrieve all blocks from registry as array
     *
     * @return array
     */
    public function getAllBlocks()
    {
        return $this->elements;
    }

    /**
     * Get block object by name
     *
     * @param string $name
     * @return bool|AbstractBlock
     */
    public function getBlock($name)
    {
        $element = $this->getElement($name);
        if ($element) {
            return $element->getWrappedElement();
        }
        return false;
    }

    /**
     * Get child block if exists
     *
     * @param string $parentName
     * @param string $alias
     * @return null
     */
    public function getChildBlock($parentName, $alias)
    {
        $parent = $this->getElement($parentName);
        if ($parent) {
            $child = $parent->getElement($alias);
            if ($child) {
                return $child->getWrappedElement();
            }
        }
        return null;
    }

    /**
     * Set child element into layout structure
     *
     * @param string $parentName
     * @param string $elementName
     * @param string $alias
     * @return DefaultLayout
     */
    public function setChild($parentName, $elementName, $alias)
    {
        $parent = $this->getElement($parentName);
        if ($parent) {
            $element = $this->getElement($elementName);
            if ($element) {
                $parent->attach($element, $alias);
            }
        }
        return $this;
    }

    /**
     * Reorder a child of a specified element
     *
     * If $offsetOrSibling is null, it will put the element to the end
     * If $offsetOrSibling is numeric (integer) value, it will put the element after/before specified position
     * Otherwise -- after/before specified sibling
     *
     * @param string $parentName
     * @param string $childName
     * @param string|int|null $offsetOrSibling
     * @param bool $after
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function reorderChild($parentName, $childName, $offsetOrSibling, $after = true)
    {
        // TODO:
    }

    /**
     * Remove child element from parent
     *
     * @param string $parentName
     * @param string $alias
     * @return DefaultLayout
     */
    public function unsetChild($parentName, $alias)
    {
        $parent = $this->getElement($parentName);
        if ($parent) {
            $child = $parent->getElement($alias);
            if ($child) {
                $parent->detach($child, $alias);
            }
        }
        return $this;
    }

    /**
     * Get list of child names
     *
     * @param string $parentName
     * @return array
     */
    public function getChildNames($parentName)
    {
        $parent = $this->getElement($parentName);
        if ($parent) {
            return array_keys($parent->getChildrenElements());
        }
        return array();
    }

    /**
     * Get list of child blocks
     *
     * Returns associative array of <alias> => <block instance>
     *
     * @param string $parentName
     * @return array
     */
    public function getChildBlocks($parentName)
    {
        $parent = $this->getElement($parentName);
        if ($parent) {
            return $parent->getChildBlocks();
        }
        return array();
    }

    /**
     * Get child name by alias
     *
     * @param string $parentName
     * @param string $alias
     * @return bool|string
     */
    public function getChildName($parentName, $alias)
    {
        $parent = $this->getElement($parentName);
        if ($parent) {
            $child = $parent->getChild($alias);
            if ($child) {
                return $child->getName();
            }
        }
        return false;
    }

    /**
     * Add element to parent group
     *
     * @param string $blockName
     * @param string $parentGroupName
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addToParentGroup($blockName, $parentGroupName)
    {
        // TODO:
    }

    /**
     * Get element names for specified group
     *
     * @param string $blockName
     * @param string $groupName
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getGroupChildNames($blockName, $groupName)
    {
        // TODO:
    }

    /**
     * Gets parent name of an element with specified name
     *
     * @param string $childName
     * @return bool|string
     */
    public function getParentName($childName)
    {
        $child = $this->getElement($childName);
        if ($child) {
            $parent = $child->getParentElement();
            if ($parent) {
                return $parent->getName();
            }
        }
        return false;
    }

    /**
     * Block Factory
     *
     * @param  string $type
     * @param  string $name
     * @param  array $attributes
     * @return AbstractBlock
     */
    public function createBlock($type, $name = '', array $attributes = array())
    {
        $attributes['type'] = $type;
        $attributes['name_in_layout'] = $name;

        return $this->objectManager->create($type, $attributes);
    }

    /**
     * Add a block to registry, create new object if needed
     *
     * @param string $class
     * @param string $name
     * @param string $parentName
     * @param string $alias
     * @return \Magento\View\Container\Block
     * @todo DELETE (use viewFactory and addElement instead)
     */
    public function addBlock($class, $name = '', $parentName = '', $alias = '')
    {
        $element = $this->viewFactory->createBlock(
            $this->context,
            array(
                'class' => $class,
                'name' => $name,
            )
        );
        if ($element) {
            if ($parentName) {
                $parent = $this->getElement($parentName);
                if ($parent) {
                    $parent->attach($element, $alias);
                }
            }
        }
        return $element;
    }

    /**
     * Insert container into layout structure
     *
     * @param string $name
     * @param string $label
     * @param array $meta
     * @param string $parentName
     * @param string $alias
     * @return \Magento\View\Container\Container
     * @todo DELETE (use viewFactory and addElement instead)
     */
    public function addContainer($name, $label, array $meta = array(), $parentName = '', $alias = '')
    {
        $meta['name'] = $name;
        $meta['label'] = $label;
        $element = $this->viewFactory->createContainer($this->context, $meta);
        if ($element) {
            if ($parentName) {
                $parent = $this->getElement($parentName);
                if ($parent) {
                    $parent->attach($element, $alias);
                }
            }
        }
        return $element;
    }

    /**
     * Rename element in layout and layout structure
     *
     * @param string $oldName
     * @param string $newName
     * @return bool
     * @todo DELETE (used mostly in setNameInLayout)
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function renameElement($oldName, $newName)
    {
        die(__METHOD__);
    }

    /**
     * Get element alias by name
     *
     * @param string $name
     * @return bool|string
     * @todo DELETE
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getElementAlias($name)
    {
        die(__METHOD__);
    }

    /**
     * Remove an element from output
     *
     * @param string $name
     * @return \Magento\Core\Model\Layout
     * @todo DELETE
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function removeOutputElement($name)
    {
        die(__METHOD__);
    }

    /**
     * Retrieve messages block
     *
     * @return \Magento\Core\Block\Messages
     * @todo DELETE use whatever instead
     */
    public function getMessagesBlock()
    {
        if (!isset($this->messages)) {
            $this->messages = $this->createBlock('Magento\Core\Block\Messages');
        }
        return $this->messages;
    }

    /**
     * Get block singleton
     *
     * @param string $class
     * @throws \Magento\Core\Exception
     * @return \Magento\Core\Helper\AbstractHelper
     * @todo DELETE use object manager or view factory
     */
    public function getBlockSingleton($class)
    {
        if (!isset($this->helpers[$class])) {
            if (!class_exists($class)) {
                throw new Exception(__('Invalid block class name: %1', $class));
            }

            $helper = $this->createBlock($class);
            if ($helper) {
                if ($helper instanceof AbstractBlock) {
                    $helper->setLayout($this);
                }
                $this->helpers[$class] = $helper;
            }
        }
        return $this->helpers[$class];
    }

    /**
     * Retrieve block factory
     *
     * @return \Magento\Core\Model\BlockFactory
     * @todo DELETE
     */
    public function getBlockFactory()
    {
        die(__METHOD__);
    }

    /**
     * Retrieve layout area
     *
     * @return string
     * @todo DELETE
     */
    public function getArea()
    {
        if (isset($this->area)) {
            return $this->area;
        }
        return $this->context->getArea();
    }

    /**
     * Set layout area
     *
     * @param $area
     * @return Layout
     * @todo DELETE
     */
    public function setArea($area)
    {
        $this->area = $area;
        return $this;
    }

    /**
     * Declaring layout direct output flag
     *
     * @param   bool $flag
     * @return  Layout
     * @todo DELETE
     */
    public function setDirectOutput($flag)
    {
        $this->directOutput = $flag;
    }

    /**
     * Retrieve direct output flag
     *
     * @return bool
     * @todo DELETE
     */
    public function isDirectOutput()
    {
        return $this->directOutput;
    }

    /**
     * Get property value of an element
     *
     * @param string $name
     * @param string $attribute
     * @return mixed
     * @todo DELETE
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getElementProperty($name, $attribute)
    {
        die(__METHOD__);
    }

    /**
     * Whether specified element is a block
     *
     * @param string $name
     * @return bool
     * @todo DELETE
     */
    public function isBlock($name)
    {
        $element = $this->getElement($name);
        if ($element) {
            return $element->getType() === Block::TYPE;
        }
        return false;
    }

    /**
     * Checks if element with specified name is container
     *
     * @param string $name
     * @return bool
     * @todo DELETE
     */
    public function isContainer($name)
    {
        $element = $this->getElement($name);
        if ($element) {
            return $element->getType() === Container::TYPE;
        }
        return false;
    }

    /**
     * Whether the specified element may be manipulated externally
     *
     * @param string $name
     * @return bool
     * @todo DELETE
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isManipulationAllowed($name)
    {
        die(__METHOD__);
    }

    /**
     * Save block in blocks registry
     *
     * @param string $name
     * @param AbstractBlock $block
     * @return Layout
     * @todo DELETE
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setBlock($name, $block)
    {
        die(__METHOD__);
    }
}
