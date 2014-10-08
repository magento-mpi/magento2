<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View;

use Magento\Framework\View\Layout\Element;
use Magento\Framework\View\Layout\ScheduledStructure;

/**
 * Layout model
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class Layout extends \Magento\Framework\Simplexml\Config implements \Magento\Framework\View\LayoutInterface
{
    /**
     * Layout Update module
     *
     * @var \Magento\Framework\View\Layout\ProcessorInterface
     */
    protected $_update;

    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    protected $_blockFactory;

    /**
     * Blocks registry
     *
     * @var array
     */
    protected $_blocks = [];

    /**
     * Cache of elements to output during rendering
     *
     * @var array
     */
    protected $_output = [];

    /**
     * Helper blocks cache for this layout
     *
     * @var array
     */
    protected $sharedBlocks = [];

    /**
     * A variable for transporting output into observer during rendering
     *
     * @var \Magento\Framework\Object
     */
    protected $_renderingOutput;

    /**
     * Cache of generated elements' HTML
     *
     * @var array
     */
    protected $_renderElementCache = array();

    /**
     * Layout structure model
     *
     * @var Layout\Data\Structure
     */
    protected $_structure;

    /**
     * @var \Magento\Framework\View\Layout\ScheduledStructure
     */
    protected $_scheduledStructure;

    /**
     * Renderers registered for particular name
     *
     * @var array
     */
    protected $_renderers = array();

    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Framework\View\Layout\ProcessorFactory
     */
    protected $_processorFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var bool
     */
    protected $isPrivate = false;

    /**
     * @var \Magento\Framework\View\Design\Theme\ResolverInterface
     */
    protected $themeResolver;

    /**
     * @var Layout\Reader\Pool
     */
    protected $reader;

    /**
     * @var bool
     */
    protected $cacheable;

    /**
     * @var \Magento\Framework\View\Page\Config\Generator
     */
    protected $pageConfigGenerator;

    /**
     * @var \Magento\Framework\View\Page\Config\Structure
     */
    protected $pageConfigStructure;

    /**
     * @var \Magento\Framework\View\Layout\GeneratorPool
     */
    protected $generatorPool;

    /**
     * Constructor
     *
     * @param Layout\ProcessorFactory $processorFactory
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
     * @param Layout\Data\Structure $structure
     * @param ScheduledStructure $scheduledStructure
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param Design\Theme\ResolverInterface $themeResolver
     * @param Layout\Reader\Pool $reader
     * @param Page\Config\Generator $pageConfigGenerator
     * @param Page\Config\Structure $pageConfigStructure
     * @param Layout\GeneratorPool $generatorPool
     * @param bool $cacheable
     */
    public function __construct(
        \Magento\Framework\View\Layout\ProcessorFactory $processorFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        Layout\Data\Structure $structure,
        \Magento\Framework\View\Layout\ScheduledStructure $scheduledStructure,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\View\Design\Theme\ResolverInterface $themeResolver,
        \Magento\Framework\View\Layout\Reader\Pool $reader,
        \Magento\Framework\View\Page\Config\Generator $pageConfigGenerator,
        \Magento\Framework\View\Page\Config\Structure $pageConfigStructure,
        \Magento\Framework\View\Layout\GeneratorPool $generatorPool,
        $cacheable = true
    ) {
        $this->_elementClass = 'Magento\Framework\View\Layout\Element';
        $this->setXml(simplexml_load_string('<layout/>', $this->_elementClass));
        $this->_eventManager = $eventManager;
        $this->_blockFactory = $blockFactory;
        $this->_structure = $structure;
        $this->_renderingOutput = new \Magento\Framework\Object;
        $this->_scheduledStructure = $scheduledStructure;
        $this->_processorFactory = $processorFactory;
        $this->messageManager = $messageManager;
        $this->themeResolver = $themeResolver;
        $this->reader = $reader;
        $this->cacheable = $cacheable;
        $this->pageConfigGenerator = $pageConfigGenerator;
        $this->pageConfigStructure = $pageConfigStructure;
        $this->generatorPool = $generatorPool;

        $this->readerContext = new Layout\Reader\Context(
            $this->_scheduledStructure,
            $this->_structure,
            $this->pageConfigStructure
        );
    }

    /**
     * Cleanup circular references between layout & blocks
     *
     * Destructor should be called explicitly in order to work around the PHP bug
     * https://bugs.php.net/bug.php?id=62468
     */
    public function __destruct()
    {
        if (isset($this->_update) && is_object($this->_update)) {
            $this->_update->__destruct();
            $this->_update = null;
        }
        $this->_blocks = array();
        $this->_xml = null;
    }

    /**
     * Retrieve the layout update instance
     *
     * @return \Magento\Framework\View\Layout\ProcessorInterface
     */
    public function getUpdate()
    {
        if (!$this->_update) {
            $theme = $this->themeResolver->get();
            $this->_update = $this->_processorFactory->create(array('theme' => $theme));
        }
        return $this->_update;
    }

    /**
     * Layout xml generation
     *
     * @return $this
     */
    public function generateXml()
    {
        $xml = $this->getUpdate()->asSimplexml();
        $this->setXml($xml);
        $this->_structure->importElements(array());
        return $this;
    }

    /**
     * @return Layout\Reader\Context
     */
    public function getReaderContext()
    {
        return $this->readerContext;
    }

    /**
     * Create structure of elements from the loaded XML configuration
     *
     * @throws \Magento\Framework\Exception
     * @return void
     */
    public function generateElements()
    {
        \Magento\Framework\Profiler::start(__CLASS__ . '::' . __METHOD__);
        \Magento\Framework\Profiler::start('build_structure');
        $this->reader->readStructure($this->readerContext, $this->getNode());
        \Magento\Framework\Profiler::stop('build_structure');

        \Magento\Framework\Profiler::start('generate_elements');
        $this->pageConfigGenerator->setStructure($this->readerContext->getPageConfigStructure());
        $this->pageConfigGenerator->process();
        $this->generatorPool->process($this->readerContext, $this);

        $this->_addToOutputRootContainers();
        \Magento\Framework\Profiler::stop('generate_elements');
        \Magento\Framework\Profiler::stop(__CLASS__ . '::' . __METHOD__);
    }

    /**
     * Add parent containers to output
     *
     * @return $this
     */
    protected function _addToOutputRootContainers()
    {
        foreach ($this->_structure->exportElements() as $name => $element) {
            if ($element['type'] === Element::TYPE_CONTAINER && empty($element['parent'])) {
                $this->addOutputElement($name);
            }
        }
        return $this;
    }

    /**
     * Get child block if exists
     *
     * @param string $parentName
     * @param string $alias
     * @return bool|\Magento\Framework\View\Element\AbstractBlock
     */
    public function getChildBlock($parentName, $alias)
    {
        $name = $this->_structure->getChildId($parentName, $alias);
        if ($this->isBlock($name)) {
            return $this->getBlock($name);
        }
        return false;
    }

    /**
     * Set child element into layout structure
     *
     * @param string $parentName
     * @param string $elementName
     * @param string $alias
     * @return $this
     */
    public function setChild($parentName, $elementName, $alias)
    {
        $this->_structure->setAsChild($elementName, $parentName, $alias);
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
     * @return void
     */
    public function reorderChild($parentName, $childName, $offsetOrSibling, $after = true)
    {
        $this->_structure->reorderChildElement($parentName, $childName, $offsetOrSibling, $after);
    }

    /**
     * Remove child element from parent
     *
     * @param string $parentName
     * @param string $alias
     * @return $this
     */
    public function unsetChild($parentName, $alias)
    {
        $this->_structure->unsetChild($parentName, $alias);
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
        return array_keys($this->_structure->getChildren($parentName));
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
        $blocks = array();
        foreach ($this->_structure->getChildren($parentName) as $childName => $alias) {
            $block = $this->getBlock($childName);
            if ($block) {
                $blocks[$alias] = $block;
            }
        }
        return $blocks;
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
        return $this->_structure->getChildId($parentName, $alias);
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
        if (!isset($this->_renderElementCache[$name]) || !$useCache) {
            if ($this->isUiComponent($name)) {
                $result = $this->_renderUiComponent($name);
            } else if ($this->isBlock($name)) {
                $result = $this->_renderBlock($name);
            } else {
                $result = $this->_renderContainer($name);
            }
            $this->_renderElementCache[$name] = $result;
        }
        $this->_renderingOutput->setData('output', $this->_renderElementCache[$name]);
        $this->_eventManager->dispatch(
            'core_layout_render_element',
            array('element_name' => $name, 'layout' => $this, 'transport' => $this->_renderingOutput)
        );
        return $this->_renderingOutput->getData('output');
    }

    /**
     * Gets HTML of block element
     *
     * @param string $name
     * @return string
     * @throws \Magento\Framework\Exception
     */
    protected function _renderBlock($name)
    {
        $block = $this->getBlock($name);
        return $block ? $block->toHtml() : '';
    }

    /**
     * Gets HTML of Ui Component
     *
     * @param string $name
     * @return string
     * @throws \Magento\Framework\Exception
     */
    protected function _renderUiComponent($name)
    {
        $uiComponent = $this->getUiComponent($name);
        return $uiComponent ? $uiComponent->toHtml() : '';
    }

    /**
     * Gets HTML of container element
     *
     * @param string $name
     * @return string
     */
    protected function _renderContainer($name)
    {
        $html = '';
        $children = $this->getChildNames($name);
        foreach ($children as $child) {
            $html .= $this->renderElement($child);
        }
        if ($html == '' || !$this->_structure->getAttribute($name, Element::CONTAINER_OPT_HTML_TAG)) {
            return $html;
        }

        $htmlId = $this->_structure->getAttribute($name, Element::CONTAINER_OPT_HTML_ID);
        if ($htmlId) {
            $htmlId = ' id="' . $htmlId . '"';
        }

        $htmlClass = $this->_structure->getAttribute($name, Element::CONTAINER_OPT_HTML_CLASS);
        if ($htmlClass) {
            $htmlClass = ' class="' . $htmlClass . '"';
        }

        $htmlTag = $this->_structure->getAttribute($name, Element::CONTAINER_OPT_HTML_TAG);

        $html = sprintf('<%1$s%2$s%3$s>%4$s</%1$s>', $htmlTag, $htmlId, $htmlClass, $html);

        return $html;
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
        return $this->_structure->addToParentGroup($blockName, $parentGroupName);
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
        return $this->_structure->getGroupChildNames($blockName, $groupName);
    }

    /**
     * Check if element exists in layout structure
     *
     * @param string $name
     * @return bool
     */
    public function hasElement($name)
    {
        return $this->_structure->hasElement($name);
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
        return $this->_structure->getAttribute($name, $attribute);
    }

    /**
     * Whether specified element is a block
     *
     * @param string $name
     * @return bool
     */
    public function isBlock($name)
    {
        if ($this->_structure->hasElement($name)) {
            return Element::TYPE_BLOCK === $this->_structure->getAttribute($name, 'type');
        }
        return false;
    }

    /**
     * Whether specified element is a UI Component
     *
     * @param string $name
     * @return bool
     */
    public function isUiComponent($name)
    {
        if ($this->_structure->hasElement($name)) {
            return Element::TYPE_UI_COMPONENT === $this->_structure->getAttribute($name, 'type');
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
        if ($this->_structure->hasElement($name)) {
            return Element::TYPE_CONTAINER === $this->_structure->getAttribute($name, 'type');
        }
        return false;
    }

    /**
     * Whether the specified element may be manipulated externally
     *
     * @param string $name
     * @return bool
     */
    public function isManipulationAllowed($name)
    {
        $parentName = $this->_structure->getParentId($name);
        return $parentName && $this->isContainer($parentName);
    }

    /**
     * Save block in blocks registry
     *
     * @param string $name
     * @param \Magento\Framework\View\Element\AbstractBlock $block
     * @return $this
     */
    public function setBlock($name, $block)
    {
        $this->_blocks[$name] = $block;
        $block->setLayout($this);
        return $this;
    }

    /**
     * Remove block from registry
     *
     * @param string $name
     * @return $this
     */
    public function unsetElement($name)
    {
        if (isset($this->_blocks[$name])) {
            $this->_blocks[$name] = null;
            unset($this->_blocks[$name]);
        }
        $this->_structure->unsetElement($name);
        return $this;
    }

    /**
     * Block Factory
     *
     * @param string $type
     * @param string $name
     * @param array $arguments
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    public function createBlock($type, $name = '', array $arguments = array())
    {
        $name = $this->_structure->createStructuralElement($name, Element::TYPE_BLOCK, $type);
        $block = $this->_createBlock($type, $name, $arguments);
        return $block;
    }

    /**
     * Create block and add to layout
     *
     * @param string $type
     * @param string $name
     * @param array $arguments
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _createBlock($type, $name, array $arguments = array())
    {
        /** @var \Magento\Framework\View\Layout\Generator\Block $blockGenerator */
        $blockGenerator = $this->generatorPool->getGenerator(\Magento\Framework\View\Layout\Generator\Block::TYPE);
        $block = $blockGenerator->createBlock($type, $name, $arguments);
        $this->setBlock($name, $block);
        return $block;
    }

    /**
     * Add a block to registry, create new object if needed
     *
     * @param string|\Magento\Framework\View\Element\AbstractBlock $block
     * @param string $name
     * @param string $parent
     * @param string $alias
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    public function addBlock($block, $name = '', $parent = '', $alias = '')
    {
        if (empty($name) && $block instanceof \Magento\Framework\View\Element\AbstractBlock) {
            $name = $block->getNameInLayout();
        }
        $name = $this->_structure->createStructuralElement(
            $name,
            Element::TYPE_BLOCK,
            $name ?: (is_object($block) ? get_class($block) : $block)
        );
        if ($parent) {
            $this->_structure->setAsChild($name, $parent, $alias);
        }
        return $this->_createBlock($block, $name);
    }

    /**
     * Insert container into layout structure
     *
     * @param string $name
     * @param string $label
     * @param array $options
     * @param string $parent
     * @param string $alias
     * @return void
     */
    public function addContainer($name, $label, array $options = array(), $parent = '', $alias = '')
    {
        $name = $this->_structure->createStructuralElement($name, Element::TYPE_CONTAINER, $alias);
        // TODO: eliminate options
        $this->_generateContainer($name, $label, $options);
        if ($parent) {
            $this->_structure->setAsChild($name, $parent, $alias);
        }
    }

    /**
     * Rename element in layout and layout structure
     *
     * @param string $oldName
     * @param string $newName
     * @return bool
     */
    public function renameElement($oldName, $newName)
    {
        if (isset($this->_blocks[$oldName])) {
            $block = $this->_blocks[$oldName];
            $this->_blocks[$oldName] = null;
            unset($this->_blocks[$oldName]);
            $this->_blocks[$newName] = $block;
        }
        $this->_structure->renameElement($oldName, $newName);

        return $this;
    }

    /**
     * Retrieve all blocks from registry as array
     *
     * @return array
     */
    public function getAllBlocks()
    {
        return $this->_blocks;
    }

    /**
     * Get block object by name
     *
     * @param string $name
     * @return \Magento\Framework\View\Element\AbstractBlock|bool
     */
    public function getBlock($name)
    {
        if ($this->_scheduledStructure->hasElement($name)) {
            $this->generatorPool->processElement($this->readerContext, $name, $this);
        }
        if (isset($this->_blocks[$name])) {
            return $this->_blocks[$name];
        } else {
            return false;
        }
    }

    /**
     * Get Ui Component object by name
     *
     * @param string $name
     * @return \Magento\Framework\View\Element\AbstractBlock|bool
     */
    public function getUiComponent($name)
    {
        die('UIComponent');
        if ($this->_scheduledStructure->hasElement($name)) {
            $this->generatorPool->processElement($this->readerContext, $name, $this);
        }
        if (isset($this->_blocks[$name])) {
            return $this->_blocks[$name];
        } else {
            return false;
        }
    }

    /**
     * Gets parent name of an element with specified name
     *
     * @param string $childName
     * @return bool|string
     */
    public function getParentName($childName)
    {
        return $this->_structure->getParentId($childName);
    }

    /**
     * Get element alias by name
     *
     * @param string $name
     * @return bool|string
     */
    public function getElementAlias($name)
    {
        return $this->_structure->getChildAlias($this->_structure->getParentId($name), $name);
    }

    /**
     * Add an element to output
     *
     * @param string $name
     * @return $this
     */
    public function addOutputElement($name)
    {
        $this->_output[$name] = $name;
        return $this;
    }

    /**
     * Remove an element from output
     *
     * @param string $name
     * @return $this
     */
    public function removeOutputElement($name)
    {
        if (isset($this->_output[$name])) {
            unset($this->_output[$name]);
        }
        return $this;
    }

    /**
     * Get all blocks marked for output
     *
     * @return string
     */
    public function getOutput()
    {
        $out = '';
        foreach ($this->_output as $name) {
            $out .= $this->renderElement($name);
        }
        return $out;
    }

    /**
     * Retrieve messages block
     *
     * @return \Magento\Framework\View\Element\Messages
     */
    public function getMessagesBlock()
    {
        $block = $this->getBlock('messages');
        if ($block) {
            return $block;
        }
        return $this->createBlock('Magento\Framework\View\Element\Messages', 'messages');
    }

    /**
     * Get block singleton
     *
     * @param string $type
     * @return \Magento\Framework\App\Helper\AbstractHelper
     * @throws \Magento\Framework\Model\Exception
     */
    public function getBlockSingleton($type)
    {
        if (!isset($this->sharedBlocks[$type])) {
            if (!$type) {
                throw new \Magento\Framework\Model\Exception('Invalid block type');
            }

            $block = $this->_blockFactory->createBlock($type);
            if ($block) {
                if ($block instanceof \Magento\Framework\View\Element\AbstractBlock) {
                    $block->setLayout($this);
                }
                $this->sharedBlocks[$type] = $block;
            }
        }
        return $this->sharedBlocks[$type];
    }

    /**
     * @param string $namespace
     * @param string $staticType
     * @param string $dynamicType
     * @param string $type
     * @param string $template
     * @param array $data
     * @return $this
     */
    public function addAdjustableRenderer($namespace, $staticType, $dynamicType, $type, $template, $data = array())
    {
        $this->_renderers[$namespace][$staticType][$dynamicType] = array(
            'type' => $type,
            'template' => $template,
            'data' => $data
        );
        return $this;
    }

    /**
     * @param string $namespace
     * @param string $staticType
     * @param string $dynamicType
     * @return array|null
     */
    public function getRendererOptions($namespace, $staticType, $dynamicType)
    {
        if (!isset($this->_renderers[$namespace])) {
            return null;
        }
        if (!isset($this->_renderers[$namespace][$staticType])) {
            return null;
        }
        if (!isset($this->_renderers[$namespace][$staticType][$dynamicType])) {
            return null;
        }
        return $this->_renderers[$namespace][$staticType][$dynamicType];
    }

    /**
     * @param string $namespace
     * @param string $staticType
     * @param string $dynamicType
     * @param array $data
     * @return void
     */
    public function executeRenderer($namespace, $staticType, $dynamicType, $data = array())
    {
        if ($options = $this->getRendererOptions($namespace, $staticType, $dynamicType)) {
            $dictionary = array();
            /** @var $block \Magento\Framework\View\Element\Template */
            $block = $this->createBlock($options['type'], '')
                ->setData($data)
                ->assign($dictionary)
                ->setTemplate($options['template'])
                ->assign($data);

            echo $this->_renderBlock($block->getNameInLayout());
        }
    }

    /**
     * Init messages by message storage(s), loading and adding messages to layout messages block
     *
     * @param string|array $messageGroups
     * @return void
     * @throws \UnexpectedValueException
     */
    public function initMessages($messageGroups = array())
    {
        foreach ($this->_prepareMessageGroup($messageGroups) as $group) {
            $block = $this->getMessagesBlock();
            $block->addMessages($this->messageManager->getMessages(true, $group));
            $block->addStorageType($group);
        }
    }

    /**
     * Validate message groups
     *
     * @param array $messageGroups
     * @return array
     */
    protected function _prepareMessageGroup($messageGroups)
    {
        if (!is_array($messageGroups)) {
            $messageGroups = array($messageGroups);
        } elseif (empty($messageGroups)) {
            $messageGroups[] = $this->messageManager->getDefaultGroup();
        }
        return $messageGroups;
    }

    /**
     * Check is exists non-cacheable layout elements
     *
     * @return bool
     */
    public function isCacheable()
    {
        $cacheableXml = !(bool)count($this->_xml->xpath('//' . Element::TYPE_BLOCK . '[@cacheable="false"]'));
        return $this->cacheable && $cacheableXml;
    }

    /**
     * Check is exists non-cacheable layout elements
     *
     * @return bool
     */
    public function isPrivate()
    {
        return $this->isPrivate;
    }

    /**
     * Mark layout as private
     *
     * @param bool $isPrivate
     * @return Layout
     */
    public function setIsPrivate($isPrivate = true)
    {
        $this->isPrivate = (bool)$isPrivate;
        return $this;
    }
}
