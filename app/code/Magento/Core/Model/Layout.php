<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model;

use Magento\View\Element\BlockFactory;
use Magento\View\Layout\Element;

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
class Layout extends \Magento\Simplexml\Config implements \Magento\View\LayoutInterface
{
    /**
     * Scheduled structure array index for name
     */
    const SCHEDULED_STRUCTURE_INDEX_NAME = 0;

    /**
     * Scheduled structure array index for alias
     */
    const SCHEDULED_STRUCTURE_INDEX_ALIAS = 1;

    /**
     * Scheduled structure array index for parent element name
     */
    const SCHEDULED_STRUCTURE_INDEX_PARENT_NAME = 2;

    /**
     * Scheduled structure array index for sibling element name
     */
    const SCHEDULED_STRUCTURE_INDEX_SIBLING_NAME = 3;

    /**
     * Scheduled structure array index for is after parameter
     */
    const SCHEDULED_STRUCTURE_INDEX_IS_AFTER = 4;

    /**
     * Scheduled structure array index for layout element object
     */
    const SCHEDULED_STRUCTURE_INDEX_LAYOUT_ELEMENT = 5;

    /**
     * @var \Magento\View\DesignInterface
     */
    protected $_design;

    /**
     * Layout Update module
     *
     * @var \Magento\View\Layout\ProcessorInterface
     */
    protected $_update;

    /**
     * @var \Magento\View\Element\BlockFactory
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
     * Layout area (f.e. admin, frontend)
     *
     * @var string
     */
    protected $_area;

    /**
     * Helper blocks cache for this layout
     *
     * @var array
     */
    protected $_helpers = array();

    /**
     * A variable for transporting output into observer during rendering
     *
     * @var \Magento\Object
     */
    protected $_renderingOutput = null;

    /**
     * Cache of generated elements' HTML
     *
     * @var array
     */
    protected $_renderElementCache = array();

    /**
     * Layout structure model
     *
     * @var \Magento\Data\Structure
     */
    protected $_structure;

    /**
     * An increment to generate names
     *
     * @var int
     */
    protected $_nameIncrement = array();

    /**
     * @var \Magento\Core\Model\Layout\Argument\Processor
     */
    protected $_argumentProcessor;

    /**
     * @var \Magento\Core\Model\Layout\ScheduledStructure
     */
    protected $_scheduledStructure;

    /**
     * @var array
     */
    protected $_serviceCalls = array();

    /** @var \Magento\Core\Model\DataService\Graph  */
    protected $_dataServiceGraph;

    /**
     * Renderers registered for particular name
     *
     * @var array
     */
    protected $_renderers = array();

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;
    
    /**
     * @var \Magento\Logger $logger
     */
    protected $_logger;

    /**
     * @var \Magento\View\Layout\ProcessorFactory
     */
    protected $_processorFactory;

    /**
     * @var \Magento\Core\Model\Resource\Theme\CollectionFactory
     */
    protected $themeFactory;

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @param \Magento\View\Layout\ProcessorFactory $processorFactory
     * @param Resource\Theme\CollectionFactory $themeFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\View\DesignInterface $design
     * @param BlockFactory $blockFactory
     * @param \Magento\Data\Structure $structure
     * @param Layout\Argument\Processor $argumentProcessor
     * @param Layout\ScheduledStructure $scheduledStructure
     * @param DataService\Graph $dataServiceGraph
     * @param Store\Config $coreStoreConfig
     * @param \Magento\App\State $appState
     * @param \Magento\Message\ManagerInterface $messageManager
     * @param string $area
     */
    public function __construct(
        \Magento\View\Layout\ProcessorFactory $processorFactory,
        \Magento\Core\Model\Resource\Theme\CollectionFactory $themeFactory,
        \Magento\Logger $logger,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Core\Helper\Data $coreData,
        \Magento\View\DesignInterface $design,
        \Magento\View\Element\BlockFactory $blockFactory,
        \Magento\Data\Structure $structure,
        \Magento\Core\Model\Layout\Argument\Processor $argumentProcessor,
        \Magento\Core\Model\Layout\ScheduledStructure $scheduledStructure,
        \Magento\Core\Model\DataService\Graph $dataServiceGraph,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\App\State $appState,
        \Magento\Message\ManagerInterface $messageManager,
        $area = \Magento\View\DesignInterface::DEFAULT_AREA
    ) {
        $this->_eventManager = $eventManager;
        $this->_coreData = $coreData;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_design = $design;
        $this->_blockFactory = $blockFactory;
        $this->_appState = $appState;
        $this->_area = $area;
        $this->_structure = $structure;
        $this->_argumentProcessor = $argumentProcessor;
        $this->_elementClass = 'Magento\View\Layout\Element';
        $this->setXml(simplexml_load_string('<layout/>', $this->_elementClass));
        $this->_renderingOutput = new \Magento\Object;
        $this->_scheduledStructure = $scheduledStructure;
        $this->_dataServiceGraph = $dataServiceGraph;
        $this->_processorFactory = $processorFactory;
        $this->themeFactory = $themeFactory;
        $this->_logger = $logger;
        $this->messageManager = $messageManager;
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
     * @return \Magento\View\Layout\ProcessorInterface
     */
    public function getUpdate()
    {
        if (!$this->_update) {
            $theme = $this->_getThemeInstance($this->getArea());
            $this->_update = $this->_processorFactory->create(array('theme' => $theme));
        }
        return $this->_update;
    }

    /**
     * Retrieve instance of a theme currently used in an area
     *
     * @param string $area
     * @return \Magento\Core\Model\Theme
     */
    protected function _getThemeInstance($area)
    {
        if ($this->_design->getDesignTheme()->getArea() == $area || $this->_design->getArea() == $area) {
            return $this->_design->getDesignTheme();
        }
        /** @var \Magento\Core\Model\Resource\Theme\Collection $themeCollection */
        $themeCollection = $this->_themeFactory->create();
        $themeIdentifier = $this->_design->getConfigurationDesignTheme($area);
        if (is_numeric($themeIdentifier)) {
            $result = $themeCollection->getItemById($themeIdentifier);
        } else {
            $themeFullPath = $area . \Magento\View\Design\ThemeInterface::PATH_SEPARATOR . $themeIdentifier;
            $result = $themeCollection->getThemeByFullPath($themeFullPath);
        }
        return $result;
    }

    /**
     * Retrieve layout area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->_appState->getAreaCode();
    }

    /**
     * Set area code
     *
     * @deprecated
     * @param string $areaCode
     */
    public function setArea($areaCode)
    {
        $this->_area = $areaCode;
    }

    /**
     * Layout xml generation
     *
     * @return \Magento\Core\Model\Layout
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
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function generateElements()
    {
        \Magento\Profiler::start(__CLASS__ . '::' . __METHOD__);
        \Magento\Profiler::start('build_structure');

        $this->_scheduledStructure->flushScheduledStructure();

        $this->_readStructure($this->getNode());

        $this->_dataServiceGraph
            ->init($this->getServiceCalls());

        while (false === $this->_scheduledStructure->isStructureEmpty()) {
            $this->_scheduleElement(key($this->_scheduledStructure->getStructure()));
        };
        $this->_scheduledStructure->flushPaths();

        foreach ($this->_scheduledStructure->getListToMove() as $elementToMove) {
            $this->_moveElementInStructure($elementToMove);
        }

        foreach ($this->_scheduledStructure->getListToRemove() as $elementToRemove) {
            $this->_removeElement($elementToRemove);
        }

        \Magento\Profiler::stop('build_structure');

        \Magento\Profiler::start('generate_elements');

        while (false === $this->_scheduledStructure->isElementsEmpty()) {
            list($type, $node, $actions, $args, $attributes) = current($this->_scheduledStructure->getElements());
            $elementName = key($this->_scheduledStructure->getElements());

            if (isset($node['output'])) {
                $this->addOutputElement($elementName);
            }
            if ($type == Element::TYPE_BLOCK) {
                $this->_generateBlock($elementName);
            } else {
                $this->_generateContainer($elementName, (string)$node[Element::CONTAINER_OPT_LABEL], $attributes);
                $this->_scheduledStructure->unsetElement($elementName);
            }
        }
        \Magento\Profiler::stop('generate_elements');
        \Magento\Profiler::stop(__CLASS__ . '::' . __METHOD__);
    }

    /**
     * Remove scheduled element
     *
     * @param string $elementName
     * @param bool $isChild
     * @return \Magento\Core\Model\Layout
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
     * @return \Magento\Core\Model\Layout
     */
    protected function _moveElementInStructure($element)
    {
        list ($destination, $siblingName, $isAfter, $alias) = $this->_scheduledStructure->getElementToMove($element);
        if (!$alias && false === $this->_structure->getChildId($destination, $this->getElementAlias($element))) {
            $alias = $this->getElementAlias($element);
        }
        $this->_structure->unsetChild($element, $alias)->setAsChild($element, $destination, $alias);
        $this->reorderChild($destination, $element, $siblingName, $isAfter);
        return $this;
    }

    /**
     * Traverse through all elements of specified XML-node and schedule structural elements of it
     *
     * @param \Magento\View\Layout\Element $parent
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _readStructure($parent)
    {
        foreach ($parent as $node) {
            /** @var $node \Magento\View\Layout\Element */
            switch ($node->getName()) {
                case Element::TYPE_CONTAINER:
                    $this->_scheduleStructure($node, $parent);
                    $this->_mergeContainerAttributes($node);
                    $this->_readStructure($node);
                    break;

                case Element::TYPE_BLOCK:
                    $this->_initServiceCalls($node);
                    $this->_scheduleStructure($node, $parent);
                    $this->_readStructure($node);
                    break;

                case Element::TYPE_REFERENCE_CONTAINER:
                    $this->_mergeContainerAttributes($node);
                    $this->_readStructure($node);
                    break;

                case Element::TYPE_REFERENCE_BLOCK:
                    $this->_readStructure($node);
                    break;

                case Element::TYPE_ACTION:
                    $referenceName = $parent->getAttribute('name');
                    $element = $this->_scheduledStructure->getStructureElement($referenceName, array());
                    $element['actions'][] = array($node, $parent);
                    $this->_scheduledStructure->setStructureElement($referenceName, $element);
                    break;

                case Element::TYPE_ARGUMENTS:
                    $referenceName = $parent->getAttribute('name');
                    $element = $this->_scheduledStructure->getStructureElement($referenceName, array());
                    $args = $this->_parseArguments($node);
                    $element['arguments'] = $this->_mergeArguments($element, $args);

                    $this->_scheduledStructure->setStructureElement($referenceName, $element);
                    break;

                case Element::TYPE_MOVE:
                    $this->_scheduleMove($node);
                    break;

                case Element::TYPE_REMOVE:
                    $this->_scheduledStructure->setElementToRemoveList((string)$node->getAttribute('name'));
                    break;

                default:
                    break;
            }
        }
    }

    /**
     * Grab information about data service from the node
     *
     * @param \Magento\View\Layout\Element $node
     * @return \Magento\Core\Model\Layout
     */
    protected function _initServiceCalls($node)
    {
        if (!$dataServices = $node->xpath('data')) {
            return $this;
        }
        $nodeName = $node->getAttribute('name');
        foreach ($dataServices as $dataServiceNode) {
            $dataServiceName = $dataServiceNode->getAttribute('service_call');
            if (isset($this->_serviceCalls[$dataServiceName])) {
                $this->_serviceCalls[$dataServiceName]['namespaces'][$nodeName] =
                    $dataServiceNode->getAttribute('alias');
            } else {
                $this->_serviceCalls[$dataServiceName] = array(
                    'namespaces' => array($nodeName => $dataServiceNode->getAttribute('alias'))
                );
            }
        }
        return $this;
    }

    /**
     * Merge Container attributes
     *
     * @param \Magento\View\Layout\Element $node
     */
    protected function _mergeContainerAttributes(\Magento\View\Layout\Element $node)
    {
        $containerName = $node->getAttribute('name');
        $element = $this->_scheduledStructure->getStructureElement($containerName, array());

        if (isset($element['attributes'])) {
            $keys = array_keys($element['attributes']);
            foreach ($keys as $key) {
                if (isset($node[$key])) {
                    $element['attributes'][$key] = (string)$node[$key];
                }
            }
        } else {
            $element['attributes'] = array(
                Element::CONTAINER_OPT_HTML_TAG => (string)$node[Element::CONTAINER_OPT_HTML_TAG],
                Element::CONTAINER_OPT_HTML_ID => (string)$node[Element::CONTAINER_OPT_HTML_ID],
                Element::CONTAINER_OPT_HTML_CLASS => (string)$node[Element::CONTAINER_OPT_HTML_CLASS],
                Element::CONTAINER_OPT_LABEL => (string)$node[Element::CONTAINER_OPT_LABEL],
            );
        }
        $this->_scheduledStructure->setStructureElement($containerName, $element);
    }

    /**
     * Merge element arguments
     *
     * @param array $element
     * @param array $arguments
     * @return array
     */
    protected function _mergeArguments(array $element, array $arguments)
    {
        $output = $arguments;
        if (isset($element['arguments'])) {
            $output = array_replace_recursive($element['arguments'], $arguments);
        }
        return $output;
    }

    /**
     * Parse argument nodes and create prepared array of items
     *
     * @param \Magento\View\Layout\Element $node
     * @return array
     */
    protected function _parseArguments(\Magento\View\Layout\Element $node)
    {
        $arguments = array();
        foreach ($node->xpath('argument') as $argument) {
            /** @var $argument \Magento\View\Layout\Element */
            $argumentName = (string)$argument['name'];
            $arguments[$argumentName] = $this->_argumentProcessor->parse($argument);
        }
        return $arguments;
    }

    /**
     * Process arguments
     *
     * @param array $arguments
     * @return array
     */
    protected function _processArguments(array $arguments)
    {
        $result = array();
        foreach ($arguments as $name => $argument) {
            $result[$name] = $this->_argumentProcessor->process($argument);
        }
        return $result;
    }

    /**
     * Schedule structural changes for move directive
     *
     * @param \Magento\View\Layout\Element $node
     * @throws \Magento\Exception
     * @return \Magento\Core\Model\Layout
     */
    protected function _scheduleMove($node)
    {
        $elementName = (string)$node->getAttribute('element');
        $destination = (string)$node->getAttribute('destination');
        $alias = (string)$node->getAttribute('as') ?: '';
        if ($elementName && $destination) {
            list($siblingName, $isAfter) = $this->_beforeAfterToSibling($node);
            $this->_scheduledStructure->setElementToMove(
                $elementName,
                array($destination, $siblingName, $isAfter, $alias)
            );
        } else {
            throw new \Magento\Exception('Element name and destination must be specified.');
        }
        return $this;
    }

    /**
     * Populate queue for generating structural elements
     *
     * @param \Magento\View\Layout\Element $node
     * @param \Magento\View\Layout\Element $parent
     * @see _scheduleElement() where the _scheduledStructure is used
     */
    protected function _scheduleStructure($node, $parent)
    {
        if ((string)$node->getAttribute('name')) {
            $name = (string)$node->getAttribute('name');
        } else {
            $name = $this->_generateAnonymousName($parent->getElementName() . '_schedule_block');
            $node->addAttribute('name', $name);
        }
        $path = $name;

        // type, alias, parentName, siblingName, isAfter, node
        $row = array(
            self::SCHEDULED_STRUCTURE_INDEX_NAME            => $node->getName(),
            self::SCHEDULED_STRUCTURE_INDEX_ALIAS           => '',
            self::SCHEDULED_STRUCTURE_INDEX_PARENT_NAME     => '',
            self::SCHEDULED_STRUCTURE_INDEX_SIBLING_NAME    => null,
            self::SCHEDULED_STRUCTURE_INDEX_IS_AFTER        => true,
            self::SCHEDULED_STRUCTURE_INDEX_LAYOUT_ELEMENT  => $node
        );

        $parentName = $parent->getElementName();
        if ($parentName) {
            $row[self::SCHEDULED_STRUCTURE_INDEX_ALIAS] = (string)$node->getAttribute('as');
            $row[self::SCHEDULED_STRUCTURE_INDEX_PARENT_NAME] = $parentName;

            list(
                $row[self::SCHEDULED_STRUCTURE_INDEX_SIBLING_NAME],
                $row[self::SCHEDULED_STRUCTURE_INDEX_IS_AFTER]
                ) = $this->_beforeAfterToSibling($node);

            // materialized path for referencing nodes in the plain array of _scheduledStructure
            if ($this->_scheduledStructure->hasPath($parentName)) {
                $path = $this->_scheduledStructure->getPath($parentName) . '/' . $path;
            }
        }

        $this->_overrideElementWorkaround($name, $path);
        $this->_scheduledStructure->setPathElement($name, $path);
        if ($this->_scheduledStructure->hasStructureElement($name)) {
            // union of arrays
            $this->_scheduledStructure->setStructureElement(
                $name,
                $row + $this->_scheduledStructure->getStructureElement($name)
            );
        } else {
            $this->_scheduledStructure->setStructureElement($name, $row);
        }
    }

    /**
     * Analyze "before" and "after" information in the node and return sibling name and whether "after" or "before"
     *
     * @param \Magento\View\Layout\Element $node
     * @return array
     */
    protected function _beforeAfterToSibling($node)
    {
        $result = array(null, true);
        if (isset($node['after'])) {
            $result[0] = (string)$node['after'];
        } elseif (isset($node['before'])) {
            $result[0] = (string)$node['before'];
            $result[1] = false;
        }
        return $result;
    }

    /**
     * Destroy previous element with same name and all its children, if new element overrides it
     *
     * This is a workaround to handle situation, when an element emerges with name of element that already exists.
     * In this case we destroy entire structure of the former element and replace with the new one.
     *
     * @param string $name
     * @param string $path
     */
    protected function _overrideElementWorkaround($name, $path)
    {
        if ($this->_scheduledStructure->hasStructureElement($name)) {
            foreach ($this->_scheduledStructure->getPaths() as $potentialChild => $childPath) {
                if (0 === strpos($childPath, "{$path}/")) {
                    $this->_scheduledStructure->unsetPathElement($potentialChild);
                    $this->_scheduledStructure->unsetStructureElement($potentialChild);
                }
            }
        }
    }

    /**
     * Process queue of structural elements and actually add them to structure, and schedule elements for generation
     *
     * The catch is to populate parents first, if they are not in the structure yet.
     * Since layout updates could come in arbitrary order, a case is possible where an element is declared in reference,
     * while referenced element itself is not declared yet.
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @param string $key in _scheduledStructure represent element name
     */
    protected function _scheduleElement($key)
    {
        $row = $this->_scheduledStructure->getStructureElement($key);

        if (!isset($row[self::SCHEDULED_STRUCTURE_INDEX_LAYOUT_ELEMENT])) {
            $this->_logger->log("Broken reference: missing declaration of the element '{$key}'.", \Zend_Log::CRIT);
            $this->_scheduledStructure->unsetPathElement($key);
            $this->_scheduledStructure->unsetStructureElement($key);
            return;
        }
        list($type, $alias, $parentName, $siblingName, $isAfter, $node) = $row;
        $name = $this->_createStructuralElement($key, $type, $parentName . $alias);
        if ($parentName) {
            // recursively populate parent first
            if ($this->_scheduledStructure->hasStructureElement($parentName)) {
                $this->_scheduleElement($parentName, $this->_scheduledStructure->getStructureElement($parentName));
            }
            if ($this->_structure->hasElement($parentName)) {
                $this->_structure->setAsChild($name, $parentName, $alias);
            } else {
                $this->_logger
                    ->log("Broken reference: the '{$name}' element cannot be added as child to '{$parentName}', "
                        . 'because the latter doesn\'t exist', \Zend_Log::CRIT
                    );
            }
        }
        $this->_scheduledStructure->unsetStructureElement($key);
        $data = array(
            $type,
            $node,
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
            $name = $this->_generateAnonymousName($class);
        }
        $this->_structure->createElement($name, array('type' => $type));
        return $name;
    }

    /**
     * Generate anonymous element name for structure
     *
     * @param string $class
     * @return string
     */
    protected function _generateAnonymousName($class)
    {
        $position = strpos($class, '\\Block\\');
        $key = $position !== false ? substr($class, $position + 7) : $class;
        $key = strtolower(trim($key, '_'));

        if (!isset($this->_nameIncrement[$key])) {
            $this->_nameIncrement[$key] = 0;
        }

        if ($this->_nameIncrement[$key] == 0 && !$this->_structure->hasElement($key)) {
            $this->_nameIncrement[$key]++;
            return $key;
        }

        do {
            $name = $key . '_' . $this->_nameIncrement[$key]++;
        } while ($this->_structure->hasElement($name));

        return $name;
    }

    /**
     * Creates block object based on xml node data and add it to the layout
     *
     * @param string $elementName
     * @return \Magento\View\Element\AbstractBlock
     * @throws \Magento\Exception
     */
    protected function _generateBlock($elementName)
    {
        list($type, $node, $actions, $args) = $this->_scheduledStructure->getElement($elementName);
        if ($type !== Element::TYPE_BLOCK) {
            throw new \Magento\Exception("Unexpected element type specified for generating block: {$type}.");
        }


        $configPath = (string)$node->getAttribute('ifconfig');
        if (!empty($configPath) && !$this->_coreStoreConfig->getConfigFlag($configPath)) {
            $this->_scheduledStructure->unsetElement($elementName);
            return;
        }

        $group = (string)$node->getAttribute('group');
        if (!empty($group)) {
            $this->_structure->addToParentGroup($elementName, $group);
        }

        // create block
        $className = (string)$node['class'];

        $arguments = $this->_processArguments($args);
        $dictionary = $this->_dataServiceGraph->getByNamespace((string)$node['name']);

        $block = $this->_createBlock($className, $elementName,
            array('data' => $arguments));

        if (!empty($node['template'])) {
            $templateFileName = (string)$node['template'];
            if ($block instanceof \Magento\View\Element\Template) {
                $block->assign($dictionary);
            }
            $block->setTemplate($templateFileName);
        }

        $this->_scheduledStructure->unsetElement($elementName);

        // execute block methods
        foreach ($actions as $action) {
            list($actionNode, $parent) = $action;
            $this->_generateAction($actionNode, $parent);
        }

        return $block;
    }

    public function getServiceCalls()
    {
        return $this->_serviceCalls;
    }

    /**
     * Set container-specific data to structure element
     *
     * @param string $name
     * @param string $label
     * @param array $options
     * @throws \Magento\Exception if any of arguments are invalid
     */
    protected function _generateContainer($name, $label, array $options)
    {
        $this->_structure->setAttribute($name, Element::CONTAINER_OPT_LABEL, $label);
        unset($options[Element::CONTAINER_OPT_LABEL]);
        unset($options['type']);
        $allowedTags = array(
            'dd', 'div', 'dl', 'fieldset', 'header', 'footer', 'hgroup', 'ol', 'p', 'section','table', 'tfoot', 'ul'
        );
        if (!empty($options[Element::CONTAINER_OPT_HTML_TAG])
            && !in_array($options[Element::CONTAINER_OPT_HTML_TAG], $allowedTags)
        ) {
            throw new \Magento\Exception(
                __('Html tag "%1" is forbidden for usage in containers. Consider to use one of the allowed: %2.',
                $options[Element::CONTAINER_OPT_HTML_TAG], implode(', ', $allowedTags)));
        }
        if (empty($options[Element::CONTAINER_OPT_HTML_TAG])
            && (!empty($options[Element::CONTAINER_OPT_HTML_ID]) || !empty($options[Element::CONTAINER_OPT_HTML_CLASS]))
        ) {
            throw new \Magento\Exception('HTML ID or class will not have effect, if HTML tag is not specified.');
        }
        foreach ($options as $key => $value) {
            $this->_structure->setAttribute($name, $key, $value);
        }
    }

    /**
     * Run action defined in layout update
     *
     * @param \Magento\View\Layout\Element $node
     * @param \Magento\View\Layout\Element $parent
     */
    protected function _generateAction($node, $parent)
    {
        $configPath = $node->getAttribute('ifconfig');
        if ($configPath && !$this->_coreStoreConfig->getConfigFlag($configPath)) {
            return;
        }

        $method = $node->getAttribute('method');
        $parentName = $node->getAttribute('block');
        if (empty($parentName)) {
            $parentName = $parent->getElementName();
        }

        $profilerKey = 'BLOCK_ACTION:' . $parentName . '>' . $method;
        \Magento\Profiler::start($profilerKey);

        $block = $this->getBlock($parentName);
        if (!empty($block)) {
            $args = $this->_parseArguments($node);
            $args = $this->_processArguments($args);
            call_user_func_array(array($block, $method), $args);
        }

        \Magento\Profiler::stop($profilerKey);
    }

    /**
     * Get child block if exists
     *
     * @param string $parentName
     * @param string $alias
     * @return bool|\Magento\View\Element\AbstractBlock
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
     * @return \Magento\Core\Model\Layout
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
            $sibling = $this->_filterSearchMinus($offsetOrSibling, $children, $after);
            if ($childName !== $sibling) {
                $siblingParentName = $this->_structure->getParentId($sibling);
                if ($parentName !== $siblingParentName) {
                    $this->_logger
                        ->log("Broken reference: the '{$childName}' tries to reorder itself towards '{$sibling}', but "
                            . "their parents are different: '{$parentName}' and '{$siblingParentName}' respectively.",
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
     * @return \Magento\Core\Model\Layout
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
            if ($this->isBlock($name)) {
                $result = $this->_renderBlock($name);
            } else {
                $result = $this->_renderContainer($name);
            }
            $this->_renderElementCache[$name] = $result;
        }
        $this->_renderingOutput->setData('output', $this->_renderElementCache[$name]);
        $this->_eventManager->dispatch('core_layout_render_element', array(
            'element_name' => $name,
            'layout'       => $this,
            'transport'    => $this->_renderingOutput,
        ));
        return $this->_renderingOutput->getData('output');
    }

    /**
     * Gets HTML of block element
     *
     * @param string $name
     * @return string
     * @throws \Magento\Exception
     */
    protected function _renderBlock($name)
    {
        $block = $this->getBlock($name);
        return $block ? $block->toHtml() : '';
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
            $htmlClass = ' class="'. $htmlClass . '"';
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
     * @param \Magento\View\Element\AbstractBlock $block
     * @return \Magento\Core\Model\Layout
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
     * @return \Magento\Core\Model\Layout
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
     * @return \Magento\View\Element\AbstractBlock
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
     * @param string|\Magento\View\Element\AbstractBlock $block
     * @param string $name
     * @param array $attributes
     * @return \Magento\View\Element\AbstractBlock
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
     * @param string|\Magento\View\Element\AbstractBlock $block
     * @param string $name
     * @param string $parent
     * @param string $alias
     * @return \Magento\View\Element\AbstractBlock
     */
    public function addBlock($block, $name = '', $parent = '', $alias = '')
    {
        if (empty($name) && $block instanceof \Magento\View\Element\AbstractBlock) {
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
     */
    public function addContainer($name, $label, array $options = array(), $parent = '', $alias = '')
    {
        $name = $this->_createStructuralElement($name, Element::TYPE_CONTAINER, $alias);
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
     * @param string|\Magento\View\Element\AbstractBlock $block
     * @param array $attributes
     * @throws \Magento\Core\Exception
     * @return \Magento\View\Element\AbstractBlock
     */
    protected function _getBlockInstance($block, array $attributes = array())
    {
        if ($block && is_string($block)) {
            if (class_exists($block)) {
                $block = $this->_blockFactory->createBlock($block, $attributes);
            }
        }
        if (!$block instanceof \Magento\View\Element\AbstractBlock) {
            throw new \Magento\Core\Exception(__('Invalid block type: %1', $block));
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
     * @return \Magento\View\Element\AbstractBlock|bool
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
     * @return \Magento\Core\Model\Layout
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
     * @return \Magento\Core\Model\Layout
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
     * @return \Magento\View\Element\Messages
     */
    public function getMessagesBlock()
    {
        $block = $this->getBlock('messages');
        if ($block) {
            return $block;
        }
        return $this->createBlock('Magento\View\Element\Messages', 'messages');
    }

    /**
     * Get block singleton
     *
     * @param string $type
     * @throws \Magento\Core\Exception
     * @return \Magento\App\Helper\AbstractHelper
     */
    public function getBlockSingleton($type)
    {
        if (!isset($this->_helpers[$type])) {
            if (!$type) {
                throw new \Magento\Core\Exception(__('Invalid block type: %1', $type));
            }

            $helper = $this->_blockFactory->createBlock($type);
            if ($helper) {
                if ($helper instanceof \Magento\View\Element\AbstractBlock) {
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
     * @return \Magento\View\Element\BlockFactory
     */
    public function getBlockFactory()
    {
        return $this->_blockFactory;
    }

    /**
     * @param $namespace
     * @param $staticType
     * @param $dynamicType
     * @param $type
     * @param $template
     * @param string $dataServiceName
     * @param array $data
     * @return $this
     */
    public function addAdjustableRenderer($namespace, $staticType, $dynamicType, $type, $template,
        $dataServiceName = '', $data = array()
    ) {
        if (!isset($namespace)) {
            $this->_renderers[$namespace] = array();
        }
        if (!isset($namespace)) {
            $this->_renderers[$namespace][$staticType] = array();
        }
        $this->_renderers[$namespace][$staticType][$dynamicType] = array(
            'type' => $type,
            'template' => $template,
            'dataServiceName' => $dataServiceName,
            'data' => $data
        );
        return $this;
    }

    /**
     * @param $namespace
     * @param $staticType
     * @param $dynamicType
     * @return null
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
     * @param $namespace
     * @param $staticType
     * @param $dynamicType
     * @param $data
     */
    public function executeRenderer($namespace, $staticType, $dynamicType, $data = array())
    {
        if ($options = $this->getRendererOptions($namespace, $staticType, $dynamicType)) {
            $dictionary = array();
            if (!empty($options['dataServiceName'])) {
                $dictionary = $this->_dataServiceGraph->get($options['dataServiceName']);
            }
            /** @var $block \Magento\View\Element\Template */
            $block = $this->createBlock($options['type'], '')
                ->setData($data)
                ->assign($dictionary)
                ->setTemplate($options['template'])
                ->assign($data);

            echo $block->toHtml();
        }
    }

    /**
     * Init messages by message storage(s), loading and adding messages to layout messages block
     *
     * @throws \UnexpectedValueException
     * @param string|array $messageGroups
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
    public function  isCacheable()
    {
        return !(boolean)count($this->_xml->xpath('//' . Element::TYPE_BLOCK . '[@cacheable="false"]'));
    }
}
