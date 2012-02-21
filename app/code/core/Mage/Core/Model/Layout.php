<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Layout model
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Layout extends Varien_Simplexml_Config
{
    /**
     * Layout Update module
     *
     * @var Mage_Core_Model_Layout_Update
     */
    protected $_update;

    /**
     * Blocks registry
     *
     * @var array
     */
    protected $_blocks = array();

    /**
     * Cache of block callbacks to output during rendering
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
     * Flag to have blocks' output go directly to browser as oppose to return result
     *
     * @var boolean
     */
    protected $_directOutput = false;

    /**
     * @var Mage_Core_Model_Layout_Structure
     */
    protected $_structure;

    /**
     * In-memory cache for both flat and hierarchical representations of page types
     *
     * @var array
     */
    protected $_pageTypesCache = array();

    /**
     * Class constructor
     *
     * @param array $data
     */
    public function __construct($data=array())
    {
        $this->_structure = Mage::getModel('Mage_Core_Model_Layout_Structure', $this);
        $this->_elementClass = Mage::getConfig()->getModelClassName('Mage_Core_Model_Layout_Element');
        $this->setXml(simplexml_load_string('<layout/>', $this->_elementClass));
        $this->_update = Mage::getModel('Mage_Core_Model_Layout_Update');
        parent::__construct($data);
    }

    /**
     * Layout update instance
     *
     * @return Mage_Core_Model_Layout_Update
     */
    public function getUpdate()
    {
        return $this->_update;
    }

    /**
     * Set layout area
     *
     * @param   string $area
     * @return  Mage_Core_Model_Layout
     */
    public function setArea($area)
    {
        $this->_area = $area;
        return $this;
    }

    /**
     * Retrieve layout area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->_area;
    }

    /**
     * Declaring layout direct output flag
     *
     * @param   bool $flag
     * @return  Mage_Core_Model_Layout
     */
    public function setDirectOutput($flag)
    {
        $this->_directOutput = $flag;
        return $this;
    }

    /**
     * Retrieve derect output flag
     *
     * @return bool
     */
    public function isDirectOutput()
    {
        return $this->_directOutput;
    }

    /**
     * Loyout xml generation
     *
     * @return Mage_Core_Model_Layout
     */
    public function generateXml()
    {
        $xml = $this->getUpdate()->asSimplexml();
        $removeInstructions = $xml->xpath("//remove");
        if (is_array($removeInstructions)) {
            foreach ($removeInstructions as $infoNode) {
                $attributes = $infoNode->attributes();
                $blockName = (string)$attributes->name;
                if ($blockName) {
                    $ignoreNodes = $xml->xpath("//block[@name='".$blockName."']");
                    if (!is_array($ignoreNodes)) {
                        continue;
                    }
                    $ignoreReferences = $xml->xpath("//reference[@name='".$blockName."']");
                    if (is_array($ignoreReferences)) {
                        $ignoreNodes = array_merge($ignoreNodes, $ignoreReferences);
                    }

                    foreach ($ignoreNodes as $block) {
                        if ($block->getAttribute('ignore') !== null) {
                            continue;
                        }
                        if (($acl = (string)$attributes->acl)
                            && Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed($acl)
                        ) {
                            continue;
                        }
                        if (!isset($block->attributes()->ignore)) {
                            $block->addAttribute('ignore', true);
                        }
                    }
                }
            }
        }
        $this->setXml($xml);
        return $this;
    }

    /**
     * Create layout blocks hierarchy from layout xml configuration
     *
     * @param Mage_Core_Model_Layout_Element|null $parent
     */
    public function generateBlocks($parent=null)
    {
        if (empty($parent)) {
            $parent = $this->getNode();
        }
        /** @var Mage_Core_Model_Layout_Element $node  */
        foreach ($parent as $node) {
            $attributes = $node->attributes();
            if ((bool)$attributes->ignore) {
                continue;
            }
            switch ($node->getName()) {
                case 'container':
                case 'block':
                    $this->_generateElement($node, $parent);
                    $this->generateBlocks($node);
                    break;

                case 'reference':
                    $this->generateBlocks($node);
                    break;

                case 'action':
                    $this->_generateAction($node, $parent);
                    break;
            }
        }
    }

    /**
     * Creates block/container object based on xml node data
     *
     * @param Mage_Core_Model_Layout_Element $node
     * @param Mage_Core_Model_Layout_Element $parent
     * @return Mage_Core_Model_Layout
     */
    protected function _generateElement($node, $parent)
    {
        $elementType = $node->getName();
        $elementName = $node->getAttribute('name');

        $_profilerKey = strtoupper($elementType) . ':' . $elementName;
        Magento_Profiler::start($_profilerKey);

        if (Mage_Core_Model_Layout_Structure::ELEMENT_TYPE_CONTAINER == $parent->getName()) {
            $parentName = $parent->getAttribute('name');
        } else {
            $parentName = $node->getAttribute('parent');
            if (is_null($parentName)) {
                $parentName = $parent->getBlockName();
                if (!$parentName) {
                    $parentName = '';
                }
            }
        }

        $alias = $node->getAttribute('as');
        if (empty($alias)) {
            $alias = $elementName;
        }
        if (isset($node['before'])) {
            $sibling = (string)$node['before'];
            if ('-'===$sibling) {
                $sibling = '';
            }
            $element = $this->getStructure()
                ->insertElement($parentName, $elementName, $elementType, $alias, $sibling, false);
        } elseif (isset($node['after'])) {
            $sibling = (string)$node['after'];
            if ('-'===$sibling) {
                $sibling = '';
            }
            $element = $this->getStructure()
                ->insertElement($parentName, $elementName, $elementType, $alias, $sibling, true);
        } else {
            $element = $this->getStructure()->insertElement($parentName, $elementName, $elementType, $alias);
        }

        $this->getStructure()->extendAttributes($element, $node);

        if (Mage_Core_Model_Layout_Structure::ELEMENT_TYPE_BLOCK == $elementType) {
            $block = $this->_generateBlock($node);
            $updatedName = $block->getNameInLayout();
            if (empty($updatedName)) {
                throw new Magento_exception('Element has no name');
            }
            if ($updatedName !== $elementName) {
                $element->setAttribute('name', $updatedName);
            }
        }

        Magento_Profiler::stop($_profilerKey);

        return $this;
    }

    /**
     * Creates block object based on xml node data and add it to the layout
     *
     * @param Mage_Core_Model_Layout_Element $node
     * @return Mage_Core_Block_Abstract
     */
    protected function _generateBlock(Mage_Core_Model_Layout_Element $node)
    {
        if (!empty($node['class'])) {
            $className = (string)$node['class'];
        } else {
            $className = (string)$node['type'];
        }
        $elementName = $node->getAttribute('name');
        $alias = $node->getAttribute('as');
        if (empty($alias)) {
            $alias = $elementName;
        }

        $block = $this->addBlock($className, $elementName);
        if (!$block) {
            return $this;
        }
        $block->setBlockAlias($alias);
        if (!empty($node['template'])) {
            $block->setTemplate((string)$node['template']);
        }

        // TODO: remove output directive
        if (!empty($node['output'])) {
            $method = (string)$node['output'];
            $this->addOutputBlock($elementName, $method);
        }

        return $block;
    }

    /**
     * Gets Layout Structure model
     *
     * @return Mage_Core_Model_Layout_Structure
     */
    public function getStructure()
    {
        return $this->_structure;
    }

    /**
     * Find an element in layout and render it
     *
     * Returns element's output as string or false if element is not found
     *
     * @param string $name
     * @return string|false
     */
    public function renderElement($name)
    {
        $element = $this->_structure->getElementByName($name);
        if (!$element) {
            return false;
        }
        if ($this->_structure->isBlock($element)) {
            return $this->_renderBlock($element);
        }
        return $this->_renderContainer($element);
    }

    /**
     * Gets HTML of block element
     *
     * @param DOMElement $element
     * @return string
     * @throws Magento_Exception
     */
    protected function _renderBlock($element)
    {
        $block = $this->getBlock($element->getAttribute('name'));
        return $block ? $block->toHtml() : '';
    }

    /**
     * Gets HTML of container element
     *
     * @param DOMElement $element
     * @return string
     */
    protected function _renderContainer($element)
    {
        $html = '';
        $name = $element->getAttribute('name');
        $children = $this->getStructure()->getSortedChildren($name);
        foreach ($children as $child) {
            $html .= $this->renderElement($child);
        }
        if ($html == '' || !$element->hasAttribute('htmlTag')) {
            return $html;
        }
        $htmlId = $element->hasAttribute('htmlId') ? ' id="' . $element->getAttribute('htmlId') . '"' : '';
        $htmlClass = $element->hasAttribute('htmlClass') ? ' class="'. $element->getAttribute('htmlClass') . '"' : '';
        $htmlTag = $element->getAttribute('htmlTag');
        $html = sprintf('<%1$s%2$s%3$s>%4$s</%1$s>', $htmlTag, $htmlId, $htmlClass, $html);

        return $html;
    }

    /**
     * Enter description here...
     *
     * @param Varien_Simplexml_Element $node
     * @param Varien_Simplexml_Element $parent
     * @return Mage_Core_Model_Layout
     */
    protected function _generateAction($node, $parent)
    {
        if (isset($node['ifconfig']) && ($configPath = (string)$node['ifconfig'])) {
            if (!Mage::getStoreConfigFlag($configPath)) {
                return $this;
            }
        }

        if (Mage_Core_Model_Layout_Structure::ELEMENT_TYPE_CONTAINER === $parent->getName()) {
            throw new Magento_Exception('Action can not be placed inside container');
        }

        $method = $node->getAttribute('method');
        $parentName = $node->getAttribute('block');
        if (empty($parentName)) {
            $parentName = $parent->getBlockName();
        }

        $_profilerKey = 'BLOCK_ACTION:' . $parentName . '>' . $method;
        Magento_Profiler::start($_profilerKey);

        if (!empty($parentName)) {
            $block = $this->getBlock($parentName);
        }
        if (!empty($block)) {

            $args = (array)$node->children();
            unset($args['@attributes']);

            foreach ($args as $key => $arg) {
                if (($arg instanceof Mage_Core_Model_Layout_Element)) {
                    if (isset($arg['helper'])) {
                        $helper = (string)$arg['helper'];
                        if (strpos($helper, '::') === false) {
                            $helperName = explode('/', $helper);
                            $helperMethod = array_pop($helperName);
                            $helperName = implode('/', $helperName);
                        } else {
                            list($helperName, $helperMethod) = explode('::', $helper);
                        }
                        $arg = $arg->asArray();
                        unset($arg['@']);
                        $args[$key] = call_user_func_array(array(Mage::helper($helperName), $helperMethod), $arg);
                    } else {
                        /**
                         * if there is no helper we hope that this is assoc array
                         */
                        $arr = array();
                        foreach ($arg as $subkey => $value) {
                            $arr[(string)$subkey] = $value->asArray();
                        }
                        if (!empty($arr)) {
                            $args[$key] = $arr;
                        }
                    }
                }
            }

            if (isset($node['json'])) {
                $json = explode(' ', (string)$node['json']);
                foreach ($json as $arg) {
                    $args[$arg] = Mage::helper('Mage_Core_Helper_Data')->jsonDecode($args[$arg]);
                }
            }

            $this->_translateLayoutNode($node, $args);
            call_user_func_array(array($block, $method), $args);
        }

        Magento_Profiler::stop($_profilerKey);

        return $this;
    }

    /**
     * Translate layout node
     *
     * @param Varien_Simplexml_Element $node
     * @param array $args
     **/
    protected function _translateLayoutNode($node, &$args)
    {
        if (isset($node['translate'])) {
            $items = explode(' ', (string)$node['translate']);
            foreach ($items as $arg) {
                if (isset($node['module'])) {
                    $args[$arg] = Mage::helper($node['module'])->__($args[$arg]);
                } else {
                    $args[$arg] = Mage::helper('Mage_Core_Helper_Data')->__($args[$arg]);
                }
            }
        }
    }

    /**
     * Save block in blocks registry
     *
     * @param string $name
     * @param Mage_Core_Block_abstract $block
     * @return Mage_Core_Model_Layout
     */
    public function setBlock($name, $block)
    {
        $this->_blocks[$name] = $block;
        return $this;
    }

    /**
     * Remove block from registry
     *
     * @param $name
     * @return Mage_Core_Model_Layout
     */
    public function unsetBlock($name)
    {
        $this->_blocks[$name] = null;
        unset($this->_blocks[$name]);
        return $this;
    }

    /**
     * Block Factory
     *
     * @param     string $type
     * @param     string $name
     * @param     array $attributes
     * @return    Mage_Core_Block_Abstract
     */
    public function createBlock($type, $name='', array $attributes = array())
    {
        try {
            $block = $this->_getBlockInstance($type, $attributes);
        } catch (Exception $e) {
            throw $e;
        }

        if (empty($name) || '.'===$name{0}) {
            $block->setIsAnonymous(true);
            if (!empty($name)) {
                $block->setAnonSuffix(substr($name, 1));
            }
            $name = 'ANONYMOUS_'.sizeof($this->_blocks);
        }

        $block->setType($type);
        $block->setNameInLayout($name);
        $block->addData($attributes);
        $block->setLayout($this);

        $this->_blocks[$name] = $block;
        Mage::dispatchEvent('core_layout_block_create_after', array('block'=>$block));
        return $this->_blocks[$name];
    }

    /**
     * Add a block to registry, create new object if needed
     *
     * @param string|Mage_Core_Block_Abstract $block
     * @param string $blockName
     * @return Mage_Core_Block_Abstract
     */
    public function addBlock($block, $blockName)
    {
        return $this->createBlock($block, $blockName);
    }

    /**
     * Create block object instance based on block type
     *
     * @param string $block
     * @param array $attributes
     * @return Mage_Core_Block_Abstract
     */
    protected function _getBlockInstance($block, array $attributes=array())
    {
        if ($block && is_string($block)) {
            $block = Mage::getConfig()->getBlockClassName($block);
            if (Magento_Autoload::getInstance()->classExists($block)) {
                $block = new $block($attributes);
            }
        }
        if (!$block instanceof Mage_Core_Block_Abstract) {
            Mage::throwException(Mage::helper('Mage_Core_Helper_Data')->__('Invalid block type: %s', $block));
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
     * @return Mage_Core_Block_Abstract
     */
    public function getBlock($name)
    {
        if (isset($this->_blocks[$name])) {
            return $this->_blocks[$name];
        } else {
            return false;
        }
    }

    /**
     * Add a block to output
     *
     * @param string $blockName
     * @param string $method
     */
    public function addOutputBlock($blockName, $method='toHtml')
    {
        //$this->_output[] = array($blockName, $method);
        $this->_output[$blockName] = array($blockName, $method);
        return $this;
    }

    public function removeOutputBlock($blockName)
    {
        unset($this->_output[$blockName]);
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
        if (!empty($this->_output)) {
            foreach ($this->_output as $callback) {
                $out .= $this->getBlock($callback[0])->$callback[1]();
            }
        }

        return $out;
    }

    /**
     * Retrieve messages block
     *
     * @return Mage_Core_Block_Messages
     */
    public function getMessagesBlock()
    {
        $block = $this->getBlock('messages');
        if ($block) {
            return $block;
        }
        return $this->createBlock('Mage_Core_Block_Messages', 'messages');
    }

    /**
     * Enter description here...
     *
     * @param string $type
     * @return Mage_Core_Helper_Abstract
     */
    public function getBlockSingleton($type)
    {
        if (!isset($this->_helpers[$type])) {
            $className = Mage::getConfig()->getBlockClassName($type);
            if (!$className) {
                Mage::throwException(Mage::helper('Mage_Core_Helper_Data')->__('Invalid block type: %s', $type));
            }

            $helper = new $className();
            if ($helper) {
                if ($helper instanceof Mage_Core_Block_Abstract) {
                    $helper->setLayout($this);
                }
                $this->_helpers[$type] = $helper;
            }
        }
        return $this->_helpers[$type];
    }

    /**
     * Retrieve helper object
     *
     * @param   string $name
     * @return  Mage_Core_Helper_Abstract
     */
    public function helper($name)
    {
        $helper = Mage::helper($name);
        if (!$helper) {
            return false;
        }
        return $helper->setLayout($this);
    }

    /**
     * Lookup module name for translation from current specified layout node
     *
     * Priorities:
     * 1) "module" attribute in the element
     * 2) "module" attribute in any ancestor element
     * 3) layout handle name - first 1 or 2 parts (namespace is determined automatically)
     *
     * @param Varien_Simplexml_Element $node
     * @return string
     */
    public static function findTranslationModuleName(Varien_Simplexml_Element $node)
    {
        // Commented out code uses not yet implemented functionality.
        $result = (string) $node->getAttribute('module');
        if ($result) {
            //return Mage::getConfig()->getModuleConfig($result) ? $result : 'core';
            return $result;
        }
        foreach (array_reverse($node->xpath('ancestor::*[@module]')) as $element) {
            $result = (string) $element->getAttribute('module');
            if ($result) {
                //return Mage::getConfig()->getModuleConfig($result) ? $result : 'core';
                return $result;
            }
        }
        foreach ($node->xpath('ancestor-or-self::*[last()-1]') as $handle) {
            $name = Mage::getConfig()->determineOmittedNamespace($handle->getName(), true);
            if ($name) {
                //return Mage::getConfig()->getModuleConfig($name) ? $name : 'core';
                return $name;
            }
        }
        return 'Mage_Core';
    }

    /**
     * Add a page type information into the proper place in the hierarchy
     *
     * @param array $result Result hierarchy to add a page type to
     * @param array $pageTypeInfo Page type information
     * @param array $flatLookupTable Lookup table that allows direct and quick access to the page types hierarchy
     */
    protected function _addPageType(array &$result, array $pageTypeInfo, array &$flatLookupTable)
    {
        $name = $pageTypeInfo['name'];
        if (array_key_exists($name, $flatLookupTable)) {
            /* page type has already been added (for instance, as a parent for a page type added earlier) */
            return;
        }
        $parentName = $pageTypeInfo['parent'];
        if ($parentName) {
            /* add parent page types first to be able to nest the page type */
            $pageTypes = $this->getPageTypesFlat();
            $this->_addPageType($result, $pageTypes[$parentName], $flatLookupTable);
            $parent = &$flatLookupTable[$parentName];
        } else {
            $parent = &$result;
        }
        $label = $pageTypeInfo['label'];
        $parent[$name] = array(
            'name'     => $name,
            'label'    => $label,
            'children' => array(),
        );
        /* create a reference to a page type to be able to quickly locate it by name */
        $flatLookupTable[$name] = &$parent[$name]['children'];
    }

    /**
     * Retrieve all page types in the system represented as a hierarchy
     *
     * Result format:
     * array(
     *     'page_type_1' => array(
     *         'name'     => 'page_type_1',
     *         'label'    => 'Page Type 1',
     *         'children' => array(
     *             'page_type_2' => array(
     *                 'name'     => 'page_type_2',
     *                 'label'    => 'Page Type 2',
     *                 'children' => array(
     *                     // ...
     *                 )
     *             ),
     *             // ...
     *         )
     *     ),
     *     // ...
     * )
     *
     * @return array
     */
    public function getPageTypesHierarchy()
    {
        $area    = Mage::getDesign()->getArea();
        $package = Mage::getDesign()->getPackageName();
        $theme   = Mage::getDesign()->getTheme();
        /* @todo use constant cache id, as soon as there will be no ability to introduce page types with a theme */
        $cacheId = "hierarchy_{$area}_{$package}_{$theme}";
        if (array_key_exists($cacheId, $this->_pageTypesCache)) {
            return $this->_pageTypesCache[$cacheId];
        }
        $result = array();
        $flatLookup = array();
        foreach ($this->getPageTypesFlat() as $pageTypeInfo) {
            $this->_addPageType($result, $pageTypeInfo, $flatLookup);
        }
        $this->_pageTypesCache[$cacheId] = $result;
        return $result;
    }

    /**
     * Retrieve all page types in the system represented as a flat list
     *
     * Result format:
     * array(
     *     'page_type_1' => array(
     *         'name'   => 'page_type_1',
     *         'label'  => 'Page Type 1',
     *         'parent' => null,
     *     ),
     *     'page_type_2' => array(
     *         'name'   => 'page_type_2',
     *         'label'  => 'Page Type 2',
     *         'parent' => 'page_type_1',
     *     ),
     *     // ...
     * )
     *
     * @return array
     * @throws UnexpectedValueException
     */
    public function getPageTypesFlat()
    {
        $area    = Mage::getDesign()->getArea();
        $package = Mage::getDesign()->getPackageName();
        $theme   = Mage::getDesign()->getTheme();
        /* @todo use constant cache id, as soon as there will be no ability to introduce page types with a theme */
        $cacheId = "flat_{$area}_{$package}_{$theme}";
        if (array_key_exists($cacheId, $this->_pageTypesCache)) {
            return $this->_pageTypesCache[$cacheId];
        }
        $layoutFullXml = $this->getUpdate()->getFileLayoutUpdatesXml($area, $package, $theme);
        $result = array();
        /** @var $node Varien_Simplexml_Element */
        foreach ($layoutFullXml->xpath('/layouts/*[@type="page"]') as $node) {
            $name = $node->getName();
            $label = (string)$node->label;
            $parentName = $node->getAttribute('parent');
            $result[$name] = array(
                'name'   => $name,
                'label'  => $label,
                'parent' => $parentName,
            );
        }
        /* validate references to parent blocks */
        foreach ($result as $name => $info) {
            $parentName = $info['parent'];
            if ($parentName && !array_key_exists($parentName, $result)) {
                throw new UnexpectedValueException("Page type '$name' refers to non-existing parent '$parentName'.");
            }
        }
        $this->_pageTypesCache[$cacheId] = $result;
        return $result;
    }
}
