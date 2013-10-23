<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout;

use Magento\View\DataSourcePool;
use Magento\View\BlockPool;
use Magento\View\Context;
use Magento\View\LayoutInterface;
use Magento\View\Design\ThemeFactory;
use Magento\ObjectManager;
use Magento\Simplexml;

use Magento\Core\Block\AbstractBlock;
use Magento\View\DesignInterface;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DefaultLayout extends Simplexml\Config implements LayoutInterface
{
    /**
     * @var int
     */
    protected $inc = 0;

    /**
     * @var /SimpleXMLElement
     */
    protected $_xml;

    /**
     * @var array
     */
    protected $root;

    /**
     * @var Structure
     */
    protected $structure;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var HandleFactory
     */
    protected $handleFactory;

    /**
     * @var ProcessorFactory
     */
    protected $processorFactory;

    /**
     * @var DesignInterface
     */
    protected $design;

    /**
     * @var ThemeFactory
     */
    protected $themeFactory;

    /**
     * @var BlockPool
     */
    protected $blockPool;

    /**
     * @var DataSourcePool
     */
    protected $dataSourcePool;

    /**
     * @var \Magento\View\Layout\ProcessorInterface
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

    /**
     * @param DesignInterface $design
     * @param ThemeFactory $themeFactory
     * @param Context $context
     * @param HandleFactory $handleFactory
     * @param ProcessorFactory $processorFactory
     * @param Structure $structure
     * @param BlockPool $blockPool
     * @param DataSourcePool $dataSourcePool
     */
    public function __construct(
        DesignInterface $design,
        ThemeFactory $themeFactory,
        Context $context,
        HandleFactory $handleFactory,
        ProcessorFactory $processorFactory,
        Structure $structure,
        BlockPool $blockPool,
        DataSourcePool $dataSourcePool
    ) {
        $this->design = $design;
        $this->themeFactory = $themeFactory;
        $this->context = $context;
        $this->handleFactory = $handleFactory;
        $this->processorFactory = $processorFactory;
        $this->structure = $structure;
        $this->blockPool = $blockPool;
        $this->dataSourcePool = $dataSourcePool;
    }

    /**
     * Retrieve the layout update instance
     *
     * @return \Magento\View\Layout\ProcessorInterface
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
     * @return \Magento\View\Design\ThemeInterface
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
     * @return DefaultLayout
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
     * @return DefaultLayout
     */
    public function generateElements()
    {
        $this->root = array(
            'type' => 'container',
            'name' => '.'
        );

        $this->structure->createElement('.', $this->root);

        foreach ($this->_xml as $node) {
            /** @var $node Element  */
            $type = $node->getName();
            /** @var $handle HandleInterface */
            $handle = $this->handleFactory->get($type);
            $handle->parse($node, $this, $this->root['name']);
        }

        $this->root = $this->structure->getElement('.');

        $handle = $this->handleFactory->get($this->root['type']);
        $handle->register($this->root, $this, null);

        $this->root = $this->structure->getElement('.');
        return $this;
    }

    /**
     * @param $name
     * @param array $element
     * @return DefaultLayout
     */
    public function addElement($name, array $element)
    {
        $this->structure->createElement($name, $element);

        return $this;
    }

    public function updateElement($name, array $arguments, $rewrite = false)
    {
        $this->structure->updateElement($name, $arguments, $rewrite);

        return $this;
    }

    /**
     * Check if element exists in layout structure
     *
     * @param string $name
     * @return bool
     */
    public function hasElement($name)
    {
        return $this->structure->hasElement($name);
    }

    public function getElement($name)
    {
        return $this->structure->getElement($name);
    }

    /**
     * Remove block from registry
     *
     * @param string $name
     * @return DefaultLayout
     */
    public function unsetElement($name)
    {
        $this->structure->unsetElement($name);

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
        $element = $this->structure->getElement($name);
        if ($element) {
            /** @var $handle \Magento\View\Layout\Handle\RenderInterface */
            $handle = $this->handleFactory->get($element['type']);
            return $handle->render($element, $this, '');
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
        $this->root = $this->structure->getElement($name);

        return $this;
    }

    /**
     * Get Root View Element output
     *
     * @return string
     */
    public function getOutput()
    {
        /** @var $handle \Magento\View\Layout\Handle\RenderInterface */
        $handle = $this->handleFactory->get($this->root['type']);
        return $handle->render($this->root, $this, '');
    }

    /**
     * Retrieve all blocks from registry as array
     *
     * @return array
     */
    public function getAllBlocks()
    {
        return $this->blockPool->get();
    }

    /**
     * Get block object by name
     *
     * @param string $name
     * @return AbstractBlock|null
     */
    public function getBlock($name)
    {
        $block = $this->blockPool->get($name);
        if (!$block) {
            $element = $this->getElement($name);
            $parentName = $this->getParentName($name);

            $handler = $this->handleFactory->get($element['type']);
            $handler->register($element, $this, $parentName);
        }
        return $this->blockPool->get($name);
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
        $childId = $this->structure->getChildId($parentName, $alias);
        return $this->getBlock($childId);
    }

    /**
     * @param string $parentId
     * @param string $childId
     * @return null|string
     */
    public function getChildAlias($parentId, $childId)
    {
        return $this->structure->getChildAlias($parentId, $childId);
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
        $this->structure->setAsChild($elementName, $parentName, $alias);

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
        if (is_numeric($offsetOrSibling)) {
            $offset = (int)abs($offsetOrSibling) * ($after ? 1 : -1);
            $this->structure->reorderChild($parentName, $childName, $offset);
        } elseif (null === $offsetOrSibling) {
            $this->structure->reorderChild($parentName, $childName, null);
        } else {
            $children = $this->getChildNames($parentName);
            $sibling = $this->filterSearchMinus($offsetOrSibling, $children, $after);
            if ($childName !== $sibling) {
                $siblingParentName = $this->structure->getParentId($sibling);
                if ($parentName === $siblingParentName) {
                    $this->structure->reorderToSibling($parentName, $childName, $sibling, $after ? 1 : -1);
                }
            }
        }
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
        $this->structure->unsetChild($parentName, $alias);

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
        return array_keys($this->structure->getChildren($parentName));
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

        $children = $this->structure->getChildren($parentName);
        if (!empty($children)) {
            foreach ($children as $childId => $alias) {
                $result[] = $this->getBlock($childId);
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
        return $this->structure->getChildId($parentName, $alias);
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
        return $this->structure->addToParentGroup($blockName, $parentGroupName);
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
        return $this->structure->getGroupChildNames($blockName, $groupName);
    }

    /**
     * Gets parent name of an element with specified name
     *
     * @param string $childName
     * @return bool|string
     */
    public function getParentName($childName)
    {
        return $this->structure->getParentId($childName);
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
        if (empty($name)) {
            $name = 'Anonymous-' . $this->inc++;
        }

        $block = $this->blockPool->add($name, $type, $attributes);

        $block->setNameInLayout($name);
        $block->setLayout($this);

        $element = array(
            'name' => $name,
            'type' => 'block',
            'class' => $type,
            'is_registered' => true
        );
        $this->structure->createElement($name, $element);

        return $block;
    }

    /**
     * Add a block to registry, create new object if needed
     *
     * @param AbstractBlock|string $block
     * @param string $name
     * @param string $parent
     * @param string $alias
     * @return AbstractBlock
     */
    public function addBlock($block, $name = '', $parent = '', $alias = '')
    {
        $block = $this->createBlock($block, $name);
        if ($block && !empty($parent)) {
            $this->structure->setAsChild($name, $parent, $alias);
        }

        return $block;
    }

    /**
     * @param string $class
     * @param string $dataName
     * @param string $parentName
     * @param string $alias
     * @return AbstractBlock
     */
    public function addDataSource($class, $dataName, $parentName = '', $alias = '')
    {
        $data = $this->dataSourcePool->add($dataName, $class);
        if ($data && !empty($parentName)) {
            $this->dataSourcePool->assign($dataName, $parentName, $alias);
        }

        return $data;
    }

    /**
     * @return array|null|object
     */
    public function getAllDataSources()
    {
        return $this->dataSourcePool->get();
    }

    /**
     * @param string $name
     * @return array
     */
    public function getElementDataSources($name)
    {
        return $this->dataSourcePool->getNamespaceData($name);
    }

    /**
     * Insert container into layout structure
     *
     * @param string $name
     * @param string $label
     * @param array $options
     * @param string $parentName
     * @param string $alias
     * @return DefaultLayout
     */
    public function addContainer($name, $label, array $options = array(), $parentName = '', $alias = '')
    {
        $options['name'] = $name;
        $options['label'] = $label;

        $this->addElement($name, $options);
        if ($parentName) {
            $this->setChild($parentName, $name, $alias);
        }

        return $this;
    }

    /**
     * Rename element in layout and layout structure
     *
     * @param string $oldName
     * @param string $newName
     * @return bool
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
     * @return DefaultLayout
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
     * @throws \Exception
     * @return \Magento\Core\Helper\AbstractHelper
     */
    public function getBlockSingleton($class)
    {
        if (!isset($this->helpers[$class])) {
            if (!class_exists($class)) {
                throw new \Exception(__('Invalid block class name: %1', $class));
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
     */
    public function getBlockFactory()
    {
        die(__METHOD__);
    }

    /**
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
     * Set layout area
     *
     * @param $area
     * @return DefaultLayout
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
     * @return  DefaultLayout
     */
    public function setDirectOutput($flag)
    {
        $this->directOutput = $flag;
    }

    /**
     * Retrieve direct output flag
     *
     * @return bool
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
     */
    public function getElementProperty($name, $attribute)
    {
        return $this->structure->getAttribute($name, $attribute);
    }

    /**
     * Whether specified element is a block
     *
     * @param string $name
     * @return bool
     */
    public function isBlock($name)
    {
        if ($this->structure->hasElement($name)) {
            return Element::TYPE_BLOCK === $this->structure->getAttribute($name, 'type');
        }
        return false;
    }

    /**
     * Checks if element with specified name is container
     *
     * @param string $name
     * @return bool
     */
    public function isContainer($name)
    {
        if ($this->structure->hasElement($name)) {
            return Element::TYPE_CONTAINER === $this->structure->getAttribute($name, 'type');
        }
        return false;
    }

    /**
     * Whether the specified element may be manipulated externally
     *
     * @param string $name
     * @return bool
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
     * @return DefaultLayout
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setBlock($name, $block)
    {
        die(__METHOD__);
    }

    /**
     * Search for an array element using needle, but needle may be '-', which means "first" or "last" element
     *
     * Returns first or last element in the haystack, or the $needle argument
     *
     * @param string $needle
     * @param array $haystack
     * @param bool $isLast
     * @return string
     */
    private function filterSearchMinus($needle, array $haystack, $isLast)
    {
        if ('-' === $needle) {
            if ($isLast) {
                return array_pop($haystack);
            }
            return array_shift($haystack);
        }
        return $needle;
    }
}
