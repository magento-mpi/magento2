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
     * Scheduled structure array index for layout element object
     */
    const SCHEDULED_STRUCTURE_INDEX_LAYOUT_ELEMENT = 5;

    /**
     * @var \Magento\Framework\View\DesignInterface
     */
    protected $_design;

    /**
     * Layout Update module
     *
     * @var \Magento\Framework\View\Layout\ProcessorInterface
     */
    protected $_update;

    /**
     * @var \Magento\Framework\View\Element\UiComponentFactory
     */
    protected $_uiComponentFactory;

    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    protected $_blockFactory;

    /**
     * Blocks registry
     *
     * @var array
     */
    protected $_blocks = array();

    /**
     * Cache of elements to output during rendering
     *
     * @var array
     */
    protected $_output = array();

    /**
     * Helper blocks cache for this layout
     *
     * @var array
     */
    protected $_helpers = array();

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
     * @var \Magento\Framework\Data\Structure
     */
    protected $_structure;

    /**
     * An increment to generate names
     *
     * @var int
     */
    protected $_nameIncrement = array();

    /**
     * @var \Magento\Framework\View\Layout\Argument\Parser
     */
    protected $argumentParser;

    /**
     * @var \Magento\Framework\Data\Argument\InterpreterInterface
     */
    protected $argumentInterpreter;

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
     * Application configuration
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Logger $logger
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\View\Layout\ProcessorFactory
     */
    protected $_processorFactory;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var bool
     */
    protected $isPrivate = false;

    /**
     * @var string
     */
    protected $scopeType;

    /**
     * @var \Magento\Framework\View\Design\Theme\ResolverInterface
     */
    protected $themeResolver;

    /**
     * @var \Magento\Framework\App\ScopeResolverInterface
     */
    protected $scopeResolver;

    /**
     * @var Layout\Reader
     */
    protected $reader;

    /**
     * @var bool
     */
    protected $cacheable;

    /**
     * @var \Magento\Framework\View\Page\Config\Reader
     */
    protected $pageConfigReader;

    /**
     * @var \Magento\Framework\View\Page\Config\Generator
     */
    protected $pageConfigGenerator;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Layout\ProcessorFactory $processorFactory
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
     * @param \Magento\Framework\Data\Structure $structure
     * @param Layout\Argument\Parser $argumentParser
     * @param \Magento\Framework\Data\Argument\InterpreterInterface $argumentInterpreter
     * @param ScheduledStructure $scheduledStructure
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param Design\Theme\ResolverInterface $themeResolver
     * @param \Magento\Framework\App\ScopeResolverInterface $scopeResolver
     * @param Layout\Reader $reader
     * @param Page\Config\Reader $pageConfigReader
     * @param Page\Config\Generator $pageConfigGenerator
     * @param $scopeType
     * @param bool $cacheable
     */
    public function __construct(
        \Magento\Framework\View\Layout\ProcessorFactory $processorFactory,
        \Magento\Framework\Logger $logger,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Framework\Data\Structure $structure,
        \Magento\Framework\View\Layout\Argument\Parser $argumentParser,
        \Magento\Framework\Data\Argument\InterpreterInterface $argumentInterpreter,
        \Magento\Framework\View\Layout\ScheduledStructure $scheduledStructure,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\View\Design\Theme\ResolverInterface $themeResolver,
        \Magento\Framework\App\ScopeResolverInterface $scopeResolver,
        \Magento\Framework\View\Layout\Reader $reader,
        \Magento\Framework\View\Page\Config\Reader $pageConfigReader,
        \Magento\Framework\View\Page\Config\Generator $pageConfigGenerator,
        $scopeType,
        $cacheable = true
    ) {
        $this->_eventManager = $eventManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_uiComponentFactory = $uiComponentFactory;
        $this->_uiComponentFactory->setLayout($this);
        $this->_blockFactory = $blockFactory;
        $this->_appState = $appState;
        $this->_structure = $structure;
        $this->argumentParser = $argumentParser;
        $this->argumentInterpreter = $argumentInterpreter;
        $this->_elementClass = 'Magento\Framework\View\Layout\Element';
        $this->setXml(simplexml_load_string('<layout/>', $this->_elementClass));
        $this->_renderingOutput = new \Magento\Framework\Object;
        $this->_scheduledStructure = $scheduledStructure;
        $this->_processorFactory = $processorFactory;
        $this->_logger = $logger;
        $this->messageManager = $messageManager;
        $this->scopeType = $scopeType;
        $this->themeResolver = $themeResolver;
        $this->scopeResolver = $scopeResolver;
        $this->reader = $reader;
        $this->cacheable = $cacheable;
        $this->pageConfigReader = $pageConfigReader;
        $this->pageConfigGenerator = $pageConfigGenerator;
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
     * Create structure of elements from the loaded XML configuration
     *
     * @throws \Magento\Framework\Exception
     * @return void
     */
    public function generateElements()
    {
        \Magento\Framework\Profiler::start(__CLASS__ . '::' . __METHOD__);
        \Magento\Framework\Profiler::start('build_structure');
        $this->_scheduledStructure->flushScheduledStructure();
        $readerContext = new Layout\Reader\Context($this->_scheduledStructure, $this->_structure, null);
        $this->reader->readStructure($readerContext, $this->getNode());
        $this->_addToOutputRootContainers($this->getNode());
        $this->buildStructure();
        \Magento\Framework\Profiler::stop('build_structure');
        \Magento\Framework\Profiler::start('generate_elements');

        $this->pageConfigGenerator->process();

        while (false === $this->_scheduledStructure->isElementsEmpty()) {
            list($type) = current($this->_scheduledStructure->getElements());
            $elementName = key($this->_scheduledStructure->getElements());

            if ($type == Element::TYPE_UI_COMPONENT) {
                $this->_generateUiComponent($elementName);
            } elseif ($type == Element::TYPE_BLOCK) {
                $this->_generateBlock($elementName);
            } elseif ($type == Element::TYPE_CONTAINER) {
                $this->_generateContainer($elementName);
                $this->_scheduledStructure->unsetElement($elementName);
            } else {
                throw new \Magento\Framework\Exception(
                    "Unexpected element type specified for generating: {$type}."
                );
            }
        }
        \Magento\Framework\Profiler::stop('generate_elements');
        \Magento\Framework\Profiler::stop(__CLASS__ . '::' . __METHOD__);
    }

    protected function buildStructure()
    {
        while (false === $this->_scheduledStructure->isStructureEmpty()) {
            $this->_scheduleElement(key($this->_scheduledStructure->getStructure()));
        }
        $this->_scheduledStructure->flushPaths();
        foreach ($this->_scheduledStructure->getListToMove() as $elementToMove) {
            $this->_moveElementInStructure($elementToMove);
        }
        foreach ($this->_scheduledStructure->getListToRemove() as $elementToRemove) {
            $this->_removeElement($elementToRemove);
        }
    }


    /**
     * Compute and return argument values
     *
     * @param array $arguments
     * @return array
     */
    protected function _evaluateArguments(array $arguments)
    {
        $result = array();
        foreach ($arguments as $argumentName => $argumentData) {
            $result[$argumentName] = $this->argumentInterpreter->evaluate($argumentData);
        }
        return $result;
    }

    /**
     * Remove scheduled element
     *
     * @param string $elementName
     * @param bool $isChild
     * @return $this
     */
    protected function _removeElement($elementName, $isChild = false)
    {
        $elementsToRemove = array_keys($this->_structure->getChildren($elementName));
        $this->_scheduledStructure->unsetElement($elementName);

        foreach ($elementsToRemove as $element) {
            $this->_removeElement($element, true);
        }

        if (!$isChild) {
            $this->_structure->unsetElement($elementName);
            $this->_scheduledStructure->unsetElementFromListToRemove($elementName);
        }
        return $this;
    }

    /**
     * Move element in scheduled structure
     *
     * @param string $element
     * @return $this
     */
    protected function _moveElementInStructure($element)
    {
        list($destination, $siblingName, $isAfter, $alias) = $this->_scheduledStructure->getElementToMove($element);
        if (!$alias && false === $this->_structure->getChildId($destination, $this->getElementAlias($element))) {
            $alias = $this->getElementAlias($element);
        }
        $this->_structure->unsetChild($element, $alias)->setAsChild($element, $destination, $alias);
        $this->reorderChild($destination, $element, $siblingName, $isAfter);
        return $this;
    }

    /**
     * Add parent containers to output
     *
     * @param Element $nodeList
     * @return $this
     */
    protected function _addToOutputRootContainers(Element $nodeList)
    {
        /** @var $node Element */
        foreach ($nodeList as $node) {
            if ($node->getName() === Element::TYPE_CONTAINER) {
                $this->addOutputElement($node->getElementName());
            }
        }
        return $this;
    }


    /**
     * Process queue of structural elements and actually add them to structure, and schedule elements for generation
     *
     * The catch is to populate parents first, if they are not in the structure yet.
     * Since layout updates could come in arbitrary order, a case is possible where an element is declared in reference,
     * while referenced element itself is not declared yet.
     *
     * @param string $key in _scheduledStructure represent element name
     * @return void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _scheduleElement($key)
    {
        $row = $this->_scheduledStructure->getStructureElement($key);

        // if we have reference container to not existed element
        if (!isset($row[self::SCHEDULED_STRUCTURE_INDEX_LAYOUT_ELEMENT])) {
            $this->_logger->log("Broken reference: missing declaration of the element '{$key}'.", \Zend_Log::CRIT);
            $this->_scheduledStructure->unsetPathElement($key);
            $this->_scheduledStructure->unsetStructureElement($key);
            return;
        }
        // TODO: $data there
        list($type, $alias, $parentName, $siblingName, $isAfter) = $row;
        $name = $this->_createStructuralElement($key, $type, $parentName . $alias);
        if ($parentName) {
            // recursively populate parent first
            if ($this->_scheduledStructure->hasStructureElement($parentName)) {
                $this->_scheduleElement($parentName);
            }
            if ($this->_structure->hasElement($parentName)) {
                try {
                    $this->_structure->setAsChild($name, $parentName, $alias);
                } catch (\Exception $e) {
                    $this->_logger->log($e->getMessage());
                }
            } else {
                $this->_logger->log(
                    "Broken reference: the '{$name}' element cannot be added as child to '{$parentName}', " .
                    'because the latter doesn\'t exist',
                    \Zend_Log::CRIT
                );
            }
        }

        // Move from scheduledStructure to scheduledElement
        $this->_scheduledStructure->unsetStructureElement($key);
        $data = array(
            $type,
            isset($row['actions']) ? $row['actions'] : array(),
            isset($row['arguments']) ? $row['arguments'] : array(),
            isset($row['attributes']) ? $row['attributes'] : array()
        );
        $this->_scheduledStructure->setElement($name, $data);

        /**
         * Some elements provide info "after" or "before" which sibling they are supposed to go
         * Make sure to populate these siblings as well and order them correctly
         */
        if ($siblingName) {
            if ($this->_scheduledStructure->hasStructureElement($siblingName)) {
                $this->_scheduleElement($siblingName);
            }
            $this->reorderChild($parentName, $name, $siblingName, $isAfter);
        }
    }

    /**
     * Register an element in structure
     *
     * Will assign an "anonymous" name to the element, if provided with an empty name
     *
     * @param string $name
     * @param string $type
     * @param string $class
     * @return string
     */
    protected function _createStructuralElement($name, $type, $class)
    {
        if (empty($name)) {
            $structure = $this->_structure;
            $nameGenerator = function($key, &$incrementName) use ($structure) {
                do {
                    $name = $key . '_' . $incrementName++;
                } while ($structure->hasElement($name));
                return $name;
            };
            $name = $this->_generateAnonymousName($class, $nameGenerator);
        }
        $this->_structure->createElement($name, array('type' => $type));
        return $name;
    }

    /**
     * Generate anonymous element name for structure
     *
     * @param string $class
     * @param callback $nameGenerator
     * @return string
     */
    protected function _generateAnonymousName($class, $nameGenerator)
    {
        $position = strpos($class, '\\Block\\');
        $key = $position !== false ? substr($class, $position + 7) : $class;
        $key = strtolower(trim($key, '_'));
        if (!isset($this->_nameIncrement[$key])) {
            $this->_nameIncrement[$key] = 0;
        }
        return $nameGenerator($key, $this->_nameIncrement[$key]);
    }

    /**
     * Creates block object based on xml node data and add it to the layout
     *
     * @param string $elementName
     * @return \Magento\Framework\View\Element\AbstractBlock|void
     */
    protected function _generateBlock($elementName)
    {
        $row = $this->_scheduledStructure->getElement($elementName);
        /** @var $node Element */
        list($type, $actions, $args, $attributes) = $row;

        if (!empty($attributes['group'])) {
            $this->_structure->addToParentGroup($elementName, $attributes['group']);
        }

        // create block
        $className = $attributes['class'];
        $arguments = $this->_evaluateArguments($args);
        $block = $this->_createBlock($className, $elementName, array('data' => $arguments));

        if (!empty($attributes['template'])) {
            $block->setTemplate($attributes['template']);
        }
        if (!empty($attributes['ttl'])) {
            $ttl = (int)$attributes['ttl'];
            $block->setTtl($ttl);
        }

        $this->_scheduledStructure->unsetElement($elementName);

        // execute block methods
        foreach ($actions as $action) {
            list($methodName, $actionArguments) = $action;
            $this->_generateAction($block, $methodName, $actionArguments);
        }

        return $block;
    }

    /**
     * Run action defined in layout update
     *
     * @param \Magento\Framework\View\Element\AbstractBlock $block
     * @param string $methodName
     * @param array $actionArguments
     * @return void
     */
    protected function _generateAction($block, $methodName, $actionArguments)
    {
        $profilerKey = 'BLOCK_ACTION:' . $block->getNameInLayout() . '>' . $methodName;
        \Magento\Framework\Profiler::start($profilerKey);
        $args = $this->_evaluateArguments($actionArguments);
        call_user_func_array(array($block, $methodName), $args);
        \Magento\Framework\Profiler::stop($profilerKey);
    }

    /**
     * Creates UI Component object based on xml node data and add it to the layout
     *
     * @param string $elementName
     * @return \Magento\Framework\View\Element\AbstractBlock|void
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _generateUiComponent($elementName)
    {
        // TODO: Eliminate $node
        list($type, $node, $actions, $args) = $this->_scheduledStructure->getElement($elementName);
        $configPath = (string)$node->getAttribute('ifconfig');
        if (!empty($configPath)
            && !$this->_scopeConfig->isSetFlag($configPath, $this->scopeType, $this->scopeResolver->getScope())
        ) {
            $this->_scheduledStructure->unsetElement($elementName);
            return;
        }

        $group = (string)$node->getAttribute('group');
        if (!empty($group)) {
            $this->_structure->addToParentGroup($elementName, $group);
        }

        $arguments = $this->_evaluateArguments($args);

        // create Ui Component Object
        $componentName = (string)$node['component'];

        $uiComponent = $this->_uiComponentFactory->createUiComponent($componentName, $elementName, $arguments);

        $this->_blocks[$elementName] = $uiComponent;

        $this->_scheduledStructure->unsetElement($elementName);

        return $uiComponent;
    }

    /**
     * Set container-specific data to structure element
     *
     * @param string $name
     * @return void
     * @throws \Magento\Framework\Exception If any of arguments are invalid
     */
    protected function _generateContainer($name)
    {
        list($type, $actions, $args, $options) = $this->_scheduledStructure->getElement($name);
        $this->_structure->setAttribute($name, Element::CONTAINER_OPT_LABEL, $options[Element::CONTAINER_OPT_LABEL]);
        unset($options[Element::CONTAINER_OPT_LABEL]);
        unset($options['type']);
        $allowedTags = array(
            'dd',
            'div',
            'dl',
            'fieldset',
            'header',
            'footer',
            'hgroup',
            'ol',
            'p',
            'section',
            'table',
            'tfoot',
            'ul'
        );
        if (!empty($options[Element::CONTAINER_OPT_HTML_TAG]) && !in_array(
            $options[Element::CONTAINER_OPT_HTML_TAG],
            $allowedTags
        )
        ) {
            throw new \Magento\Framework\Exception(
                __(
                    'Html tag "%1" is forbidden for usage in containers. Consider to use one of the allowed: %2.',
                    $options[Element::CONTAINER_OPT_HTML_TAG],
                    implode(', ', $allowedTags)
                )
            );
        }
        if (empty($options[Element::CONTAINER_OPT_HTML_TAG]) && (!empty($options[Element::CONTAINER_OPT_HTML_ID]) ||
            !empty($options[Element::CONTAINER_OPT_HTML_CLASS]))
        ) {
            throw new \Magento\Framework\Exception(
                'HTML ID or class will not have effect, if HTML tag is not specified.'
            );
        }
        foreach ($options as $key => $value) {
            $this->_structure->setAttribute($name, $key, $value);
        }
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
        if (is_numeric($offsetOrSibling)) {
            $offset = (int)abs($offsetOrSibling) * ($after ? 1 : -1);
            $this->_structure->reorderChild($parentName, $childName, $offset);
        } elseif (null === $offsetOrSibling) {
            $this->_structure->reorderChild($parentName, $childName, null);
        } else {
            $children = $this->getChildNames($parentName);
            if ($this->_structure->getChildId($parentName, $offsetOrSibling) !== false) {
                $offsetOrSibling = $this->_structure->getChildId($parentName, $offsetOrSibling);
            }
            $sibling = $this->_filterSearchMinus($offsetOrSibling, $children, $after);
            if ($childName !== $sibling) {
                $siblingParentName = $this->_structure->getParentId($sibling);
                if ($parentName !== $siblingParentName) {
                    $this->_logger->log(
                        "Broken reference: the '{$childName}' tries to reorder itself towards '{$sibling}', but " .
                        "their parents are different: '{$parentName}' and '{$siblingParentName}' respectively.",
                        \Zend_Log::CRIT
                    );
                    return;
                }
                $this->_structure->reorderToSibling($parentName, $childName, $sibling, $after ? 1 : -1);
            }
        }
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
    protected function _filterSearchMinus($needle, array $haystack, $isLast)
    {
        if ('-' === $needle) {
            if ($isLast) {
                return array_pop($haystack);
            }
            return array_shift($haystack);
        }
        return $needle;
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
     * @param  string $type
     * @param  string $name
     * @param  array $attributes
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    public function createBlock($type, $name = '', array $attributes = array())
    {
        $name = $this->_createStructuralElement($name, Element::TYPE_BLOCK, $type);
        $block = $this->_createBlock($type, $name, $attributes);
        return $block;
    }

    /**
     * Create block and add to layout
     *
     * @param string|\Magento\Framework\View\Element\AbstractBlock $block
     * @param string $name
     * @param array $attributes
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _createBlock($block, $name, array $attributes = array())
    {
        $block = $this->_getBlockInstance($block, $attributes);

        $block->setType(get_class($block));
        $block->setNameInLayout($name);
        $block->addData(isset($attributes['data']) ? $attributes['data'] : array());
        $block->setLayout($this);

        $this->_blocks[$name] = $block;
        $this->_eventManager->dispatch('core_layout_block_create_after', array('block' => $block));
        return $this->_blocks[$name];
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
        $name = $this->_createStructuralElement(
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
        $name = $this->_createStructuralElement($name, Element::TYPE_CONTAINER, $alias);
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
     * Create block object instance based on block type
     *
     * @param string|\Magento\Framework\View\Element\AbstractBlock $block
     * @param array $attributes
     * @throws \Magento\Framework\Model\Exception
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _getBlockInstance($block, array $attributes = array())
    {
        if ($block && is_string($block)) {
            try {
                $block = $this->_blockFactory->createBlock($block, $attributes);
            } catch (\ReflectionException $e) {
                $this->_logger->log($e->getMessage());
            }
        }
        if (!$block instanceof \Magento\Framework\View\Element\AbstractBlock) {
            throw new \Magento\Framework\Model\Exception(__('Invalid block type: %1', $block));
        }
        return $block;
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
            $this->_generateBlock($name);
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
        if ($this->_scheduledStructure->hasElement($name)) {
            $this->_generateUiComponent($name);
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
        if (!isset($this->_helpers[$type])) {
            if (!$type) {
                throw new \Magento\Framework\Model\Exception('Invalid block type');
            }

            $helper = $this->_blockFactory->createBlock($type);
            if ($helper) {
                if ($helper instanceof \Magento\Framework\View\Element\AbstractBlock) {
                    $helper->setLayout($this);
                }
                $this->_helpers[$type] = $helper;
            }
        }
        return $this->_helpers[$type];
    }

    /**
     * Retrieve block factory
     *
     * @return \Magento\Framework\View\Element\BlockFactory
     */
    public function getBlockFactory()
    {
        return $this->_blockFactory;
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
