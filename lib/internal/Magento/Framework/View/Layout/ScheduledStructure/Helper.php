<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout\ScheduledStructure;

use Magento\Framework\View\Layout;
use Magento\Framework\Data\Structure;

class Helper
{
    /**#@+
     * Scheduled structure array indexes
     */
    const SCHEDULED_STRUCTURE_INDEX_TYPE = 0;
    const SCHEDULED_STRUCTURE_INDEX_ALIAS = 1;
    const SCHEDULED_STRUCTURE_INDEX_PARENT_NAME = 2;
    const SCHEDULED_STRUCTURE_INDEX_SIBLING_NAME = 3;
    const SCHEDULED_STRUCTURE_INDEX_IS_AFTER = 4;
    const SCHEDULED_STRUCTURE_INDEX_LAYOUT_DATA = 5;
    /**#@-*/

    protected $counter = 1;

    /**
     * @var \Magento\Framework\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Framework\Logger $logger
     */
    public function __construct(
        \Magento\Framework\Logger $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * Generate anonymous element name for structure
     *
     * TODO: Must be restored old logic of generation, see \Magento\Framework\View\Layout::_generateAnonymousName
     *
     * @param string $class
     * @return string
     */
    protected function _generateAnonymousName($class)
    {
        $position = strpos($class, '\\Block\\');
        $key = $position !== false ? substr($class, $position + 7) : $class;
        $key = strtolower(trim($key, '_'));

        return $key . $this->counter++;
    }

    /**
     * Populate queue for generating structural elements
     *
     * @param Layout\ScheduledStructure $scheduledStructure
     * @param \Magento\Framework\View\Layout\Element $currentNode
     * @param \Magento\Framework\View\Layout\Element $parentNode
     * @param array $data
     * @return string
     * @see scheduleElement() where the scheduledStructure is used
     */
    public function scheduleStructure(
        Layout\ScheduledStructure $scheduledStructure,
        Layout\Element $currentNode,
        Layout\Element $parentNode,
        array $data = []
    ) {
        // if it hasn't a name it must be generated
        if ((string)$currentNode->getAttribute('name')) {
            $name = (string)$currentNode->getAttribute('name');
        } else {
            $name = $this->_generateAnonymousName($parentNode->getElementName() . '_schedule_block');
            $currentNode->addAttribute('name', $name);
        }
        $path = $name;

        // Prepare scheduled element with default parameters [type, alias, parentName, siblingName, isAfter, node]
        $row = [
            self::SCHEDULED_STRUCTURE_INDEX_TYPE           => $currentNode->getName(),
            self::SCHEDULED_STRUCTURE_INDEX_ALIAS          => '',
            self::SCHEDULED_STRUCTURE_INDEX_PARENT_NAME    => '',
            self::SCHEDULED_STRUCTURE_INDEX_SIBLING_NAME   => null,
            self::SCHEDULED_STRUCTURE_INDEX_IS_AFTER       => true,
            self::SCHEDULED_STRUCTURE_INDEX_LAYOUT_DATA    => $data
        ];

        $parentName = $parentNode->getElementName();
        //if this element has a parent element, there must be reset [alias, parentName, siblingName, isAfter]
        if ($parentName) {
            $row[self::SCHEDULED_STRUCTURE_INDEX_ALIAS] = (string)$currentNode->getAttribute('as');
            $row[self::SCHEDULED_STRUCTURE_INDEX_PARENT_NAME] = $parentName;

            list($row[self::SCHEDULED_STRUCTURE_INDEX_SIBLING_NAME],
                $row[self::SCHEDULED_STRUCTURE_INDEX_IS_AFTER]) = $this->_beforeAfterToSibling($currentNode);

            // materialized path for referencing nodes in the plain array of _scheduledStructure
            if ($scheduledStructure->hasPath($parentName)) {
                $path = $scheduledStructure->getPath($parentName) . '/' . $path;
            }
        }

        $this->_overrideElementWorkaround($scheduledStructure, $name, $path);
        $scheduledStructure->setPathElement($name, $path);
        if ($scheduledStructure->hasStructureElement($name)) {
            // union of arrays
            $scheduledStructure->setStructureElement(
                $name,
                $row + $scheduledStructure->getStructureElement($name)
            );
        } else {
            $scheduledStructure->setStructureElement($name, $row);
        }
        return $name;
    }

    /**
     * Destroy previous element with same name and all its children, if new element overrides it
     *
     * This is a workaround to handle situation, when an element emerges with name of element that already exists.
     * In this case we destroy entire structure of the former element and replace with the new one.
     *
     * @param Layout\ScheduledStructure $scheduledStructure
     * @param string $name
     * @param string $path
     * @return void
     */
    protected function _overrideElementWorkaround(Layout\ScheduledStructure $scheduledStructure, $name, $path)
    {
        if ($scheduledStructure->hasStructureElement($name)) {
            foreach ($scheduledStructure->getPaths() as $potentialChild => $childPath) {
                if (0 === strpos($childPath, "{$path}/")) {
                    $scheduledStructure->unsetPathElement($potentialChild);
                    $scheduledStructure->unsetStructureElement($potentialChild);
                }
            }
        }
    }

    /**
     * Analyze "before" and "after" information in the node and return sibling name and whether "after" or "before"
     *
     * @param \Magento\Framework\View\Layout\Element $node
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
     * Process queue of structural elements and actually add them to structure, and schedule elements for generation
     *
     * The catch is to populate parents first, if they are not in the structure yet.
     * Since layout updates could come in arbitrary order, a case is possible where an element is declared in reference,
     * while referenced element itself is not declared yet.
     *
     * @param \Magento\Framework\View\Layout\ScheduledStructure $scheduledStructure
     * @param \Magento\Framework\Data\Structure $structure
     * @param string $key in _scheduledStructure represent element name
     * @return void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function scheduleElement(
        Layout\ScheduledStructure $scheduledStructure,
        Structure $structure,
        $key
    ) {
        $row = $scheduledStructure->getStructureElement($key);
        // if we have reference container to not existed element
        if (!isset($row[self::SCHEDULED_STRUCTURE_INDEX_TYPE])) {
            $this->logger->log("Broken reference: missing declaration of the element '{$key}'.", \Zend_Log::CRIT);
            $scheduledStructure->unsetPathElement($key);
            $scheduledStructure->unsetStructureElement($key);
            return;
        }
        list($type, $alias, $parentName, $siblingName, $isAfter, $data) = $row;
        $name = $this->_createStructuralElement($structure, $key, $type, $parentName . $alias);
        if ($parentName) {
            // recursively populate parent first
            if ($scheduledStructure->hasStructureElement($parentName)) {
                $this->scheduleElement($scheduledStructure, $structure, $parentName);
            }
            if ($structure->hasElement($parentName)) {
                try {
                    $structure->setAsChild($name, $parentName, $alias);
                } catch (\Exception $e) {
                    $this->logger->log($e->getMessage());
                }
            } else {
                $this->logger->log(
                    "Broken reference: the '{$name}' element cannot be added as child to '{$parentName}', " .
                    'because the latter doesn\'t exist',
                    \Zend_Log::CRIT
                );
            }
        }

        // Move from scheduledStructure to scheduledElement
        $scheduledStructure->unsetStructureElement($key);
        $scheduledStructure->setElement($name, [$type, $data]);

        /**
         * Some elements provide info "after" or "before" which sibling they are supposed to go
         * Make sure to populate these siblings as well and order them correctly
         */
        if ($siblingName) {
            if ($scheduledStructure->hasStructureElement($siblingName)) {
                $this->scheduleElement($scheduledStructure, $structure, $siblingName);
            }
            $this->reorderChild($structure, $parentName, $name, $siblingName, $isAfter);
        }
    }

    /**
     * Register an element in structure
     *
     * Will assign an "anonymous" name to the element, if provided with an empty name
     *
     * @param \Magento\Framework\Data\Structure $structure
     * @param string $name
     * @param string $type
     * @param string $class
     * @return string
     */
    protected function _createStructuralElement(Structure $structure, $name, $type, $class)
    {
        if (empty($name)) {
            $name = $this->_generateAnonymousName($class);
        }
        $structure->createElement($name, array('type' => $type));
        return $name;
    }

    /**
     * Reorder a child of a specified element
     *
     * If $offsetOrSibling is null, it will put the element to the end
     * If $offsetOrSibling is numeric (integer) value, it will put the element after/before specified position
     * Otherwise -- after/before specified sibling
     *
     * @param \Magento\Framework\Data\Structure $structure
     * @param string $parentName
     * @param string $childName
     * @param string|int|null $offsetOrSibling
     * @param bool $after
     * @return void
     */
    public function reorderChild(Structure $structure, $parentName, $childName, $offsetOrSibling, $after = true)
    {
        if (is_numeric($offsetOrSibling)) {
            $offset = (int)abs($offsetOrSibling) * ($after ? 1 : -1);
            $structure->reorderChild($parentName, $childName, $offset);
        } elseif (null === $offsetOrSibling) {
            $structure->reorderChild($parentName, $childName, null);
        } else {
            $children = array_keys($structure->getChildren($parentName));
            if ($structure->getChildId($parentName, $offsetOrSibling) !== false) {
                $offsetOrSibling = $structure->getChildId($parentName, $offsetOrSibling);
            }
            $sibling = $this->_filterSearchMinus($offsetOrSibling, $children, $after);
            if ($childName !== $sibling) {
                $siblingParentName = $structure->getParentId($sibling);
                if ($parentName !== $siblingParentName) {
                    $this->logger->log(
                        "Broken reference: the '{$childName}' tries to reorder itself towards '{$sibling}', but " .
                        "their parents are different: '{$parentName}' and '{$siblingParentName}' respectively.",
                        \Zend_Log::CRIT
                    );
                    return;
                }
                $structure->reorderToSibling($parentName, $childName, $sibling, $after ? 1 : -1);
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
}