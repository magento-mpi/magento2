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
use Magento\View\Layout;
use Magento\View\Layout\Structure;
use Magento\View\Layout\Handle;
use Magento\View\Layout\HandleFactory;
use Magento\View\Layout\Handle\Render\Block;
use Magento\View\Layout\ProcessorFactory;
use Magento\View\Design\ThemeFactory;
use Magento\ObjectManager;

use Magento\Core\Block\AbstractBlock;
use Magento\Core\Model\View\DesignInterface;

class DefaultLayout extends \Magento\Simplexml\Config implements Layout
{
    static protected $inc = 0;

    /**
     * @var /SimpleXMLElement
     */
    protected $_xml;

    protected $root;

    /**
     * @var array
     */
    protected $elements = array();

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
            /** @var $handle Handle */
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
     * @return Layout
     */
    public function addElement($name, array $element)
    {
        $this->structure->createElement($name, $element);

        return $this;
    }

    public function updateElement($name, array $arguments)
    {
        $this->structure->updateElement($name, $arguments);

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
            /** @var $handle \Magento\View\Layout\Handle\Render */
            $handle = $this->handleFactory->get($element['type']);
            return $handle->render($element, $this);
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
        /** @var $handle \Magento\View\Layout\Handle\Render */
        $handle = $this->handleFactory->get($this->root['type']);
        return $handle->render($this->root, $this);
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
        $this->structure->reorderChild($parentName, $childName, $offsetOrSibling);
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
        return $this->structure->getChildren($parentName);
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

    public function setElementAttribute($name, $attribute, $value)
    {
        $this->structure->setAttribute($name, $attribute, $value);

        return $this;
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
            $name = 'Anonymous-' . self::$inc++;
        }

        $block = $this->blockPool->add($name, $type, $attributes);

        //$block->setType($type);
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
     * @param string $class
     * @param string $elementName
     * @param string $parentName
     * @param string $alias
     * @return AbstractBlock
     */
    public function addBlock($class, $elementName, $parentName = '', $alias = '')
    {
        $block = $this->createBlock($class, $elementName);
        if ($block && !empty($parentName)) {
            $this->structure->setAsChild($parentName, $elementName, $alias);
        }

        return $block;
    }

    public function addDataSource($class, $dataName, $parentName = '', $alias = '')
    {
        $data = $this->dataSourcePool->add($dataName, $class);
        if ($data && !empty($parentName)) {
            $this->dataSourcePool->assign($dataName, $parentName, $alias);
        }

        return $data;
    }

    public function getAllDataSources()
    {
        return $this->dataSourcePool->get();
    }

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
     * @return Layout
     * @todo DELETE (use viewFactory and addElement instead)
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
     * @throws \Exception
     * @return \Magento\Core\Helper\AbstractHelper
     * @todo DELETE use object manager or view factory
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
