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

namespace Magento\View\Layout;

use Magento\View\Layout;
use Magento\View\Context;
use Magento\View\Container;
use Magento\View\ViewFactory;
use Magento\ObjectManager;
use Magento\View\Container\Base;
use Magento\Core\Model\View\DesignInterface;
use Magento\View\Design\ThemeFactory;

class DefaultLayout implements Layout
{
    protected $xml;

    protected $meta = array();

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

    protected $themeFactory;

    public function __construct(
        DesignInterface $design,
        ThemeFactory $themeFactory,
        Context $context,
        ViewFactory $viewFactory,
        ObjectManager $objectManager,
        Reader $layoutReader
    ) {
        $this->design = $design;
        $this->context = $context;
        $this->viewFactory = $viewFactory;
        $this->objectManager = $objectManager;
        $this->layoutReader = $layoutReader;
    }

    public function __destruct()
    {
        //
    }

    /**
     * Retrieve the layout update instance
     *
     * @return \Magento\Core\Model\Layout\Merge
     */
    public function getUpdate()
    {
        $theme = $this->_getThemeInstance($this->getArea());
        return $this->objectManager->get('Magento\\View\\Layout\\Processor', array(
            'layout' => $this,
            'theme' => $theme
        ));
    }

    /**
     * Retrieve instance of a theme currently used in an area
     *
     * @param string $area
     * @return \Magento\View\Design\Theme
     */
    protected function _getThemeInstance($area)
    {
        if ($this->design->getDesignTheme()->getArea() == $area || $this->design->getArea() == $area) {
            return $this->design->getDesignTheme();
        }
        $themeIdentifier = $this->design->getConfigurationDesignTheme($area);

        $theme = $this->themeFactory->getTheme($themeIdentifier);

        return $theme;
    }

    /**
     * TODO MERGE with generateElements()
     * Layout xml generation
     *
     * @return Layout
     */
    public function generateXml()
    {
        $this->xml = $this->getUpdate()->asSimplexml();
        return $this;
    }

    /**
     * TODO MERGE with generateXml()
     * Create structure of elements from the loaded XML configuration
     */
    public function generateElements()
    {
        $this->layoutReader->generateFromXml($this->xml, $this->meta);
        //echo('<xmp>'.$this->xml->asniceXml());
        //var_dump($this->meta['children']['root']['children']['head']);
        $this->root = $this->viewFactory->create($this->meta['type'],
            array(
                'context' => $this->context,
                'meta' => $this->meta
            ));

        Base::$allElements['root'] = $this->root;

        $this->root->register();

        return $this;
    }

    /**
     * @param $name
     * @return \Magento\View\Container
     */
    public function getElement($name)
    {
        return isset(Base::$allElements[$name]) ? Base::$allElements[$name] : null;
        //return isset($this->elements[$name]) ? $this->elements[$name] : null;
    }

    /**
     * Find an element in layout, render it and return string with its output
     *
     * @param string $name
     * @param bool $useCache
     * @return string
     */
    public function renderElement($name, $useCache = true)
    {
        $element = $this->getElement($name);
        if ($element) {
            $result = $element->render();
        } else {
            $result = '';
        }

        return $result;
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
            $result = $this->root->render();
        } else {
            $result = '';
        }
        return $result;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * TODO DELETE (only two-three calls from outside)
     * Check if element exists in layout structure
     *
     * @param string $name
     * @return bool
     */
    public function hasElement($name)
    {
        return isset($this->elements[$name]);
    }

    /**
     * TODO DELETE (only two-three calls from outside)
     * Remove block from registry
     *
     * @param string $name
     * @return DefaultLayout
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
     * @return \Magento\Core\Block\AbstractBlock|bool
     */
    public function getBlock($name)
    {
        $result = false;
        $element = $this->getElement($name);
        if ($element) {
            $result = $element->getWrappedElement();
        }

        return $result;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Get child block if exists
     *
     * @param string $parentName
     * @param string $alias
     * @return null
     */
    public function getChildBlock($parentName, $alias)
    {
        $result = null;

        $parent = $this->getElement($parentName);
        if ($parent) {
            $child = $parent->getElement($alias);
            if ($child) {
                $result = $child->getWrappedElement();
            }
        }

        return $result;
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
     */
    public function reorderChild($parentName, $childName, $offsetOrSibling, $after = true)
    {
        //
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
            $result = array_keys($parent->getChildrenElements());
        } else {
            $result = array();
        }

        return $result;
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
            $result = $parent->getChildBlocks();
        } else {
            $result = array();
        }

        return $result;
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
        $result = false;

        $parent = $this->getElement($parentName);
        if ($parent) {
            $child = $parent->getChild($alias);
            if ($child) {
                $result = $child->getName();
            }
        }

        return $result;
    }

    /**
     * Add element to parent group
     *
     * @param string $blockName
     * @param string $parentGroupName
     * @return bool
     */
    public function addToParentGroup($blockName, $parentGroupName)
    {
        //
    }

    /**
     * Get element names for specified group
     *
     * @param string $blockName
     * @param string $groupName
     * @return array
     */
    public function getGroupChildNames($blockName, $groupName)
    {
        //
    }

    /**
     * Gets parent name of an element with specified name
     *
     * @param string $childName
     * @return bool|string
     */
    public function getParentName($childName)
    {
        $result = false;

        $child = $this->getElement($childName);
        if ($child) {
            $parent = $child->getParentElement();
            if ($parent) {
                $result = $parent->getName();
            }
        }

        return $result;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * TODO ???
     * Block Factory
     *
     * @param  string $type
     * @param  string $name
     * @param  array $attributes
     * @return \Magento\Core\Block\AbstractBlock
     */
    public function createBlock($type, $name = '', array $attributes = array())
    {
        $attributes['type'] = $type;
        $attributes['name_in_layout'] = $name;

        return $this->objectManager->create($type, $attributes);
    }

    /**
     * TODO DELETE (use viewFactory and addElement instead)
     * Add a block to registry, create new object if needed
     *
     * @param string $class
     * @param string $name
     * @param string $parentName
     * @param string $alias
     * @return \Magento\View\Container\Block
     */
    public function addBlock($class, $name = '', $parentName = '', $alias = '')
    {
        $element = $this->viewFactory->createBlock($this->context,
            array(
                'class' => $class,
                'name' => $name
            ));
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
     * TODO DELETE (use viewFactory and addElement instead)
     * Insert container into layout structure
     *
     * @param string $name
     * @param string $label
     * @param array $meta
     * @param string $parentName
     * @param string $alias
     * @return \Magento\View\Container\Container
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
     * // TODO DELETE (used mostly in setNameInLayout)
     * Rename element in layout and layout structure
     *
     * @param string $oldName
     * @param string $newName
     * @return bool
     */
    public function renameElement($oldName, $newName)
    {
        die(__METHOD__);
    }

    /**
     * TODO DELETE
     * Get element alias by name
     *
     * @param string $name
     * @return bool|string
     */
    public function getElementAlias($name)
    {
        die(__METHOD__);
    }

    /**
     * TODO DELETE
     * Remove an element from output
     *
     * @param string $name
     * @return \Magento\Core\Model\Layout
     */
    public function removeOutputElement($name)
    {
        die(__METHOD__);
    }

    /**
     * @var \Magento\Core\Block\Messages
     */
    protected $messages;

    /**
     * TODO DELETE use whatever instead
     * Retrieve messages block
     *
     * @return \Magento\Core\Block\Messages
     */
    public function getMessagesBlock()
    {
        if (!isset($this->messages)) {
            $this->messages = $this->createBlock('Magento\Core\Block\Messages');
        }

        return $this->messages;
    }

    protected $helpers = array();

    /**
     * // TODO DELETE use object manager or view factory
     * Get block singleton
     *
     * @param string $class
     * @throws \Magento\Core\Exception
     * @return \Magento\Core\Helper\AbstractHelper
     */
    public function getBlockSingleton($class)
    {
        if (!isset($this->helpers[$class])) {
            if (!class_exists($class)) {
                throw new \Magento\Core\Exception(__('Invalid block class name: %1', $class));
            }

            $helper = $this->createBlock($class);
            if ($helper) {
                if ($helper instanceof \Magento\Core\Block\AbstractBlock) {
                    $helper->setLayout($this);
                }
                $this->helpers[$class] = $helper;
            }
        }
        return $this->helpers[$class];
    }

    /**
     * TODO DELETE
     * Retrieve block factory
     *
     * @return \Magento\Core\Model\BlockFactory
     */
    public function getBlockFactory()
    {
        die(__METHOD__);
    }

    protected $area;

    /**
     * TODO DELETE
     * Retrieve layout area
     *
     * @return string
     */
    public function getArea()
    {
        if (isset($this->area)) {
            return $this->area;
        }
        return $this->context->getArea();
    }

    /**
     * TODO DELETE
     * Set layout area
     *
     * @param $area
     * @return Layout
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    protected $directOutput = false;

    /**
     * TODO DELETE
     * Declaring layout direct output flag
     *
     * @param   bool $flag
     * @return  Layout
     */
    public function setDirectOutput($flag)
    {
        $this->directOutput = $flag;
    }

    /**
     * TODO DELETE
     * Retrieve direct output flag
     *
     * @return bool
     */
    public function isDirectOutput()
    {
        return $this->directOutput;
    }

    /**
     * TODO DELETE
     * Get property value of an element
     *
     * @param string $name
     * @param string $attribute
     * @return mixed
     */
    public function getElementProperty($name, $attribute)
    {
        die(__METHOD__);
    }

    /**
     * TODO DELETE
     * Whether specified element is a block
     *
     * @param string $name
     * @return bool
     */
    public function isBlock($name)
    {
        $result = false;
        $element = $this->getElement($name);
        if ($element) {
            $result = $element->getType() === \Magento\View\Container\Block::TYPE;
        }

        return $result;
    }

    /**
     * TODO DELETE
     * Checks if element with specified name is container
     *
     * @param string $name
     * @return bool
     */
    public function isContainer($name)
    {
        $result = false;
        $element = $this->getElement($name);
        if ($element) {
            $result = $element->getType() === \Magento\View\Container\Container::TYPE;
        }

        return $result;
    }

    /**
     * TODO DELETE
     * Whether the specified element may be manipulated externally
     *
     * @param string $name
     * @return bool
     */
    public function isManipulationAllowed($name)
    {
        die(__METHOD__);
    }

    /**
     * TODO DELETE
     * Save block in blocks registry
     *
     * @param string $name
     * @param \Magento\Core\Block\AbstractBlock $block
     * @return Layout
     */
    public function setBlock($name, $block)
    {
        die(__METHOD__);
    }
}
