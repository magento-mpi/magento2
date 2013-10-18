<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout;

use Magento\View\Context;
use Magento\View\Layout;
use Magento\View\Layout\Handle;
use Magento\View\Layout\HandleFactory;
use Magento\View\Layout\Handle\Render;
use Magento\Core\Model\BlockFactory;
use Magento\View\Layout\ProcessorFactory;
use Magento\Core\Model\View\DesignInterface;
use Magento\View\Design\ThemeFactory;
use Magento\Core\Exception;
use Magento\Core\Block\AbstractBlock;

use Magento\ObjectManager;

class DefaultLayout extends \Magento\Simplexml\Config implements Layout
{
    static protected $inc = 0;

    /**
     * @var /SimpleXMLElement
     */
    protected $_xml;

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
     * @var HandleFactory
     */
    protected $handleFactory;

    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * @var ProcessorFactory
     */
    protected $processorFactory;

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
     * @var \Magento\View\Layout\Processor
     */
    protected $processor;

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
        HandleFactory $handleFactory,
        BlockFactory $blockFactory,
        ProcessorFactory $processorFactory,
        Reader $layoutReader
    ) {
        $this->design = $design;
        $this->context = $context;
        $this->handleFactory = $handleFactory;
        $this->blockFactory = $blockFactory;
        $this->processorFactory = $processorFactory;
        $this->layoutReader = $layoutReader;
        $this->themeFactory = $themeFactory;
    }

    /**
     * Retrieve the layout update instance
     *
     * @return \Magento\View\Layout\Processor
     */
    public function getUpdate()
    {
        if (!$this->processor) {
            $theme = $this->getThemeInstance($this->getArea());
            $this->processor = $this->processorFactory->create(array('theme' => $theme));
        }
        return $this->processor;
    }

    /**
     * Retrieve instance of a theme currently used in an area
     *
     * @param string $area
     * @return \Magento\View\Design\Theme
     */
    protected function getThemeInstance($area)
    {
        if ($this->design->getDesignTheme()->getArea() == $area
            || $this->design->getArea() == $area
        ) {
            return $this->design->getDesignTheme();
        } else {
            $themeIdentifier = $this->design->getConfigurationDesignTheme($area);
            return $this->themeFactory->getTheme($themeIdentifier);
        }
    }

    /**
     * Layout xml generation
     *
     * @return Layout
     * @todo MERGE with generateElements()
     */
    public function generateXml()
    {
        $this->_xml = $this->getUpdate()->asSimplexml();
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
        $this->meta = array(
            'type' => 'container',
            'name' => '.',
            'children' => array(),
        );

        foreach ($this->_xml as $node) {
            $type = $node->getName();
            /** @var $handle Handle */
            $handle = $this->handleFactory->get($type);
            $handle->parse($node, $this, $this->meta);
        }

        $handle = $this->handleFactory->get($this->meta['type']);
        $handle->register($this->meta, $this);
        return $this;
    }

    /**
     * @param $name
     * @param array $element
     */
    public function addElement($name, array & $element)
    {
        if (isset($this->elements[$name])) {
            //throw new \Exception('The element with same name already exists: ' . $name);
        }
        $this->elements[$name] = & $element;
    }

    /**
     * @param $name
     * @return array
     */
    public function & getElement($name)
    {
        if (!isset($this->elements[$name])) {
            // to be independent from layout elements positions
            $this->elements[$name] = array();
        }

        $result = & $this->elements[$name];

        return $result;
    }

    /**
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
            /** @var $handle Render */
            $handle = $this->handleFactory->get($element['type']);
            return $handle->render($element, $this, $this->context);
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
        $this->meta = $this->getElement($name);

        return $this;
    }

    /**
     * Get Root View Element output
     *
     * @return string
     */
    public function getOutput()
    {
        /** @var $root Render */
        $root = $this->handleFactory->get($this->meta['type']);
        return $root->render($this->meta, $this, $this->context);
    }

    /**
     * Retrieve all blocks from registry as array
     *
     * @return array
     */
    public function getAllBlocks()
    {
        $blocks = array();
        foreach ($this->elements as $name => $element) {
            if (isset($element['_wrapped_'])) {
                $blocks[$name] = $element['_wrapped_'];
            }
        }
        return $blocks;
    }

    /**
     * Get block object by name
     *
     * @param string $name
     * @return bool|AbstractBlock
     */
    public function getBlock($name)
    {
        $element = & $this->getElement($name);
        if (!isset($element['_wrapped_'])) {
            if (isset($element['parent_name'])) {
                $parent = & $this->getElement($element['parent_name']);
                if ($parent) {
                    $handle = $this->handleFactory->get($element['type']);
                    $handle->register($element, $this, $parent);
                }
            }
        }

        if ($element && isset($element['_wrapped_'])) {
            return $element['_wrapped_'];
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
        if ($parent && isset($parent['children'][$alias]['_wrapped_'])) {
            return $parent['children'][$alias]['_wrapped_'];
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
        $parent = & $this->getElement($parentName);
        if ($parent) {
            $element = & $this->getElement($elementName);
            if ($element) {
                $element['parent_name'] = $parentName;
                $childName = !empty($alias) ? $alias : $elementName;
                $parent['children'][$childName] = & $element;
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
        $parent = & $this->getElement($parentName);
        if ($parent) {
            unset($parent['children'][$alias]);
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
        if ($parent && isset($parent['children'])) {
            return array_keys($parent['children']);
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
        $result = array();

        $parent = $this->getElement($parentName);
        if ($parent && isset($parent['children'])) {
            foreach ($parent['children'] as $child) {
                if (isset($child['_wrapped_'])) {
                    $result[$child['name']] = $child['_wrapped_'];
                }
            }
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
        $parent = $this->getElement($parentName);
        if ($parent && isset($parent['children'][$alias]['name'])) {
            return $parent['children'][$alias]['name'];
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

        if (isset($child) && !empty($child['parent_name'])) {
            return $child['parent_name'];
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
        $block = $this->blockFactory->createBlock($type, $attributes);

        if (empty($name)) {
            $name = 'Anonymous-' . self::$inc++;
        }
        //$block->setType($type);
        $block->setNameInLayout($name);
        $block->setLayout($this);

        $element = array(
            'name' => $name,
            'type' => 'block',
            'class' => $type,
            'registered' => true,
            '_wrapped_' => $block,
        );
        $this->addElement($name, $element);

        return $block;
    }

    /**
     * Add a block to registry, create new object if needed
     *
     * @param string $class
     * @param string $name
     * @param string $parentName
     * @param string $alias
     * @return AbstractBlock
     */
    public function addBlock($class, $name = '', $parentName = '', $alias = '')
    {
        $block = $this->createBlock($class, $name);
        if ($block) {
            $element = array(
                'name' => $name,
                'as' => $alias,
                'parent_name' => $parentName,
                'type' => 'block',
                '_wrapped_' => $block,
            );
            if ($name) {
                $this->addElement($name, $element);
            }

            if ($parentName) {
                $parent = & $this->getElement($parentName);
                if ($parent) {
                    $childName = !empty($alias) ? $alias : $name;
                    if ($childName) {
                        $parent['children'][$childName] = & $element;
                    }
                }
            }
        }

        return $block;
    }

    /**
     * Insert container into layout structure
     *
     * @param string $name
     * @param string $label
     * @param array $options
     * @param string $parentName
     * @param string $alias
     * @return Layout
     * @todo DELETE (use viewFactory and addElement instead)
     */
    public function addContainer($name, $label, array $options = array(), $parentName = '', $alias = '')
    {
        $meta['name'] = $name;
        $meta['label'] = $label;
        if ($parentName) {
            $parent = & $this->getElement($parentName);
            if (isset($parent)) {
                $childName = !empty($alias) ? $alias : $name;
                $parent['children'][$childName] = & $options;
            }
        }

        return $this;
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
     * @return \Magento\View\Layout
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
